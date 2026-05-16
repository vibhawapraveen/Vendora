<?php
class Success extends Controller
{
    private $StoreCode;
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        $this->StoreCode = $SLUG_DATA['StoreCode'];
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $session_id = $_GET['session_id'] ?? null;

        if (!$session_id) {
            header("Location: " . ROOT . $this->StoreCode . "/checkout/error");
            die;
        }

        try {
            require_once '../app/libs/stripe-php/init.php';
            $config = require '../app/core/stripe.php';
            \Stripe\Stripe::setApiKey($config['secret_key']);

            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($session->payment_status !== 'paid') {
                header("Location: " . ROOT . $this->StoreCode . "/checkout/cancel");
                die;
            }

            // IMPORTANT: Get checkout_id from metadata (not cart JSON)
            $metadata = $session->metadata;
            
            if (!isset($metadata->checkout_id)) {
                error_log("Success handler: checkout_id not found in metadata for session: " . $session_id);
                header("Location: " . ROOT . $this->StoreCode . "/checkout/error?msg=" . urlencode("Invalid checkout reference"));
                die;
            }

            // Retrieve checkout data from database
            $checkoutModel = $this->model("CheckoutModel");
            $checkout = $checkoutModel->getCheckoutById($metadata->checkout_id);

            if (!$checkout) {
                error_log("Success handler: Checkout record not found for ID: " . $metadata->checkout_id);
                header("Location: " . ROOT . $this->StoreCode . "/checkout/error?msg=" . urlencode("Checkout record not found"));
                die;
            }

            // Extract customer info from checkout record
            $customer_info = [
                'email' => $checkout['customer_email'],
                'name' => $checkout['customer_name'],
                'address1' => $checkout['address_line1'],
                'address2' => $checkout['address_line2'],
                'city' => $checkout['city']
            ];

            // Get cart from database (not from metadata)
            $cart = $checkoutModel->getCheckoutCart($metadata->checkout_id);
            
            if (!$cart) {
                error_log("Success handler: Cart data not found or invalid JSON for checkout: " . $metadata->checkout_id);
                header("Location: " . ROOT . $this->StoreCode . "/checkout/error?msg=" . urlencode("Cart data not found"));
                die;
            }

            // Get or create customer
            $customerModel = $this->model("CustomerModel");
            $customer = $customerModel->getCustomerByEmail($customer_info['email']);
            if (!$customer) {
                $customer = $customerModel->addNewCustomer($customer_info);
            }

            // Create order
            $storeModel = $this->model("StoreModel");
            $store = $storeModel->getStoreById($metadata->storecode);

            $orderModel = $this->model("OrderModel");
            $orderData = [
                'store_id' => $store['store_id'],
                'customer_id' => $customer['id'],
                'order_number' => uniqid('ORD-'),
                'address_line1' => $customer_info['address1'],
                'address_line2' => $customer_info['address2'],
                'city' => $customer_info['city']
            ];
            $order = $orderModel->createOrder($orderData, $cart);

            // Update customer-store relationship
            $customerModel->upsertCustomerToStore($customer['id'], $store['store_id'], floatval($checkout['total']));

            // Record payment in payments table
            $paymentModel = $this->model("PaymentModel");
            $total_amount = floatval($checkout['total']);
            $platform_fee = $total_amount * 0.02; // 2% platform fee
            $vendor_amount = $total_amount - $platform_fee;

            $payment_data = [
                'order_id' => $order['order_id'],
                'payment_number' => 'PAY-' . uniqid(),
                'store_id' => $store['store_id'],
                'customer_id' => $customer['id'],
                'payment_method' => 'stripe',
                'stripe_session_id' => $session_id,
                'amount' => $total_amount,
                'currency' => 'usd',
                'platform_fee' => $platform_fee,
                'vendor_amount' => $vendor_amount
            ];
            $paymentModel->createPayment($payment_data);

            // Mark checkout as completed
            $checkoutModel->markCompleted($metadata->checkout_id);

            // Store session ID for reference
            $_SESSION['last_order_id'] = $order['order_id'];
            $_SESSION['stripe_session_id'] = $session_id;

            header("Location: " . ROOT . $metadata->storecode . "/orders?success");
        } catch (\Exception $e) {
            error_log("Stripe success handler error: " . $e->getMessage());
            header("Location: " . ROOT . $this->StoreCode . "/checkout/error?msg=" . urlencode($e->getMessage()));
        }
    }
}
