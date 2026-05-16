<?php

class Poscheckout extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
       if ($_SERVER['REQUEST_METHOD'] === 'GET' && (($_GET['action'] ?? '') === 'search-customer')) {
           $this->searchCustomer();
           return;
       }

       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           $this->POST();
           return;
       }

       $this->view('dashboard/pos/poscheckout');
    }

    private function jsonResponse(array $payload, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
        die;
    }

        private function searchCustomer()
        {
            $user = Session::user();
            $store_id = $user['store_id'] ?? null;

            if (!$store_id) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Store not found for current user.',
                ], 400);
            }

            $query = trim((string)($_GET['q'] ?? ''));
            if ($query === '') {
                $this->jsonResponse([
                    'success' => true,
                    'customers' => [],
                ]);
            }

            $posModel = $this->model('PosModel');
            $customers = $posModel->searchStoreCustomersByMobile($store_id, $query, 10);

            $response = [];
            foreach ($customers as $customer) {
                $response[] = [
                    'id' => $customer['id'],
                    'name' => $customer['name'],
                    'mobile' => $customer['mobile_number'],
                    'address1' => $customer['address_line1'],
                    'address2' => $customer['address_line2'],
                    'city' => $customer['city'],
                ];
            }

            $this->jsonResponse([
                'success' => true,
                'customers' => $response,
            ]);
        }

    public function POST()
    {
        try {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);

            if (!is_array($data)) {
                $data = $_POST;
            }

            $user = Session::user();
            $store_id = $user['store_id'] ?? null;

            if (!$store_id) {
                $this->jsonResponse(['success' => false, 'message' => 'Store not found for current user.'], 400);
            }

            $cart = $data['cart'] ?? [];
            $customerType = $data['customer_type'] ?? 'new';
            $customerInfo = $data['customer'] ?? [];

            $posModel = $this->model('PosModel');
            $result = $posModel->createPhysicalOrder($store_id, $customerType, $customerInfo, $cart);

            $this->jsonResponse([
                'success' => true,
                'message' => 'POS order created successfully.',
                'order_id' => $result['order_id'],
                'order_number' => $result['order_number'],
            ]);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $statusCode = 500;
            if (
                $message === 'Cart is empty.' ||
                $message === 'New customer requires name and mobile number.' ||
                $message === 'Existing customer requires a mobile number.' ||
                $message === 'Mobile number must contain exactly 10 digits.'
            ) {
                $statusCode = 400;
            }

            $this->jsonResponse([
                'success' => false,
                'message' => $message,
            ], $statusCode);
        }
    }
}

?>

