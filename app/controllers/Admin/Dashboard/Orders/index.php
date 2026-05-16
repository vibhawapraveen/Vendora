<?php

class Orders extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $orderModel = $this->model('OrderModel');

        // Check if viewing specific order
        if (isset($_GET['view_id'])) {
            $viewId = trim($_GET['view_id']);
            $details = $orderModel->getAdminOrderDetails($viewId);
            
            if (!$details) {
                // Redirect back if not found
                header("Location: " . ROOT . "admin/dashboard/orders");
                exit;
            }

            $this->view("admin/orders/view", [
                'order' => $details['order'],
                'items' => $details['items']
            ]);
            return;
        }

        // Read filter params from GET
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        // Build filters array
        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        if (!empty($status) && $status !== 'all') {
            $filters['status'] = $status;
        }

        // Fetch data
        $orders = $orderModel->getAdminAllOrders($filters);
        $statusCounts = $orderModel->getAdminOrderStatusCounts();

        $this->view("admin/orders/index", [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
            'searchQuery' => $search
        ]);
    }
}

?>