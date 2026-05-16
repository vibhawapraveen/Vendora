<?php
class Checkout extends Controller
{
    private $StoreCode;
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        $this->StoreCode = $SLUG_DATA['StoreCode'];
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->GET();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->POST();
        }
    }

    public function GET()
    {
        $storeM = $this->model("StoreModel");
        $store = ($storeM->getStoreById($this->StoreCode));

        $storefrontCustomerM = $this->model("StorefrontCustomerModel");

        if (!isset(Session::user()['customer_id'])) {
            $redirect_url = ROOT . $this->StoreCode . "/checkout";
            header("Location: " . ROOT . "authcustomer?redirect_url=" . urlencode($redirect_url));
            die;
        }

        $this->view("templates/checkout", ['storecode' => $this->StoreCode]);

        // print_r($storefrontContent);
    }

    public function POST()
    {
        header('Content-Type: application/json');

        try {
            $customer_info = [
                'email' => Session::user()['customer_email'],
                'name' => $_POST['name'],
                'address1' => $_POST['address1'],
                'address2' => $_POST['address2'],
                'city' => $_POST['city']
            ];
            $cart = json_decode($_POST['cart'], true);

            if (empty($cart)) {
                http_response_code(400);
                echo json_encode(['error' => 'Cart is empty']);
                die;
            }

            $storeModel = $this->model("StoreModel");
            $store = $storeModel->getStoreById($_POST['storecode']);

            if (!$store) {
                http_response_code(404);
                echo json_encode(['error' => 'Store not found']);
                die;
            }

            $stripe_account_id = $storeModel->getStoreSellerStripeAccountId($store['store_id']);
            $stripe_account_id = $stripe_account_id['stripe_account_id'];

            // Calculate total
            $total = 0;
            foreach ($cart as $item) {
                $total += ($item['price'] * $item['quantity']);
            }

            // IMPORTANT: Save checkout data to database instead of Stripe metadata
            // This avoids Stripe's 500-character metadata limit
            $checkoutModel = $this->model("CheckoutModel");
            $checkout = $checkoutModel->createCheckout([
                'store_id' => $store['store_id'],
                'cart_json' => json_encode($cart),
                'customer_name' => $customer_info['name'],
                'customer_email' => $customer_info['email'],
                'address_line1' => $customer_info['address1'],
                'address_line2' => $customer_info['address2'],
                'city' => $customer_info['city'],
                'total' => $total
            ]);

            if (!$checkout) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save checkout data']);
                die;
            }

            // Initialize Stripe
            require_once '../app/libs/stripe-php/init.php';
            $config = require '../app/core/stripe.php';
            \Stripe\Stripe::setApiKey($config['secret_key']);

            // Prepare line items for Stripe
            $line_items = [];
            foreach ($cart as $item) {
                $product_data = ['name' => $item['name']];

                // Only add description if it's not empty
                if (!empty($item['variantDescription'])) {
                    $product_data['description'] = $item['variantDescription'];
                }

                $line_items[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => $product_data,
                        'unit_amount' => intval($item['price'] * 100) // Convert to cents
                    ],
                    'quantity' => $item['quantity']
                ];
            }

            // Create Stripe checkout session
            // Note: Only pass checkout_id in metadata, not the full cart
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => ROOT . $this->StoreCode . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => ROOT . $this->StoreCode . '/checkout',
                'customer_email' => $customer_info['email'],
                'payment_intent_data' => [
                    'transfer_data' => [
                        'destination' => $stripe_account_id
                    ],
                    'application_fee_amount' => (int) round($total * 100 * 0.02)
                ],
                'metadata' => [
                    'checkout_id' => $checkout['id'],
                    'storecode' => $_POST['storecode'],
                    'customer_email' => $customer_info['email']
                ]
            ]);

            // Update checkout with Stripe session ID for reference
            $checkoutModel->updateCheckoutStatus($checkout['id'], $session->id, 'pending');

            echo json_encode(['checkout_url' => $session->url]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Payment error: ' . $e->getMessage()]);
        }
    }

    public function success() {}

    public function cancel()
    {
        $this->view('templates/checkout-cancelled', ['storecode' => $this->StoreCode]);
    }

    public function error()
    {
        $msg = $_GET['msg'] ?? 'An error occurred during payment processing';
        $this->view('templates/checkout-error', ['storecode' => $this->StoreCode, 'message' => $msg]);
    }
}
