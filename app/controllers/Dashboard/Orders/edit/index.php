<?php

class Edit extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $user = Session::user();
        $store_id = $user['store_id'];

        // Validate required fields
        $required_fields = ['order_id', 'status', 'total_amount', 'address_line1', 'city'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                return;
            }
        }

        $order_id = $_POST['order_id'];
        $data = [
            'status' => $_POST['status'],
            'total_amount' => floatval($_POST['total_amount']),
            'address_line1' => $_POST['address_line1'],
            'address_line2' => $_POST['address_line2'] ?? '',
            'city' => $_POST['city']
        ];

        // Validate status
        $valid_statuses = ['pending', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($data['status'], $valid_statuses)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid status value']);
            return;
        }

        $orderModel = $this->model("OrderModel");
        $result = $orderModel->updateOrder($order_id, $data, $store_id);

        if ($result) {
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
            } else {
                header("Location: " . ROOT . "dashboard/orders/all?success=order_updated");
            }
        } else {
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to update order or order not found']);
            } else {
                header("Location: " . ROOT . "dashboard/orders/all?error=update_failed");
            }
        }
    }
}