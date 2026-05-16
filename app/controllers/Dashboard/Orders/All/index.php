<?php

class All extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Handle AJAX request for getting order data
        if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_order' && isset($_GET['order_id'])) {
            header('Content-Type: application/json');
            
            try {
                $user = Session::user();
                $store_id = $user['store_id'];
                $order_id = $_GET['order_id'];

                error_log("AJAX Debug: Getting order ID: $order_id for store: $store_id");

                $orderModel = $this->model("OrderModel");
                $order = $orderModel->getOrderById($order_id, $store_id);

                if ($order) {
                    error_log("AJAX Debug: Order found - " . json_encode($order));
                    echo json_encode(['success' => true, 'order' => $order]);
                } else {
                    error_log("AJAX Debug: Order not found for ID: $order_id, Store: $store_id");
                    echo json_encode(['success' => false, 'message' => 'Order not found or access denied']);
                }
            } catch (Exception $e) {
                error_log("AJAX Debug: Exception - " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            }
            return;
        }

        // Regular page load
        // Get current user's store_id from session
        $user = Session::user();
        $store_id = $user['store_id'];

        // Get current page from URL parameter
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $limit = 5; // Orders per page (same as products table)
        
        // Get filters from URL parameters
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? ''
        ];

        // Load OrderModel
        $orderModel = $this->model("OrderModel");

        // Handle PDF Export Layout
        if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
            // Get all orders without pagination
            $exportOrders = $orderModel->getAllOrdersPaginated($store_id, 1, 9999999, $filters);
            
            // For each order, fetch items so the print view can render them
            foreach ($exportOrders as &$order) {
                $order['items'] = $orderModel->getOrderItems($order['id']);
            }
            
            // Pass data to print view and completely exit so regular HTML isn't appended
            $this->view('dashboard/orders/print_all', [
                'orders' => $exportOrders,
                'filters' => $filters,
                'store_name' => Session::user()['store_name'] ?? 'Your Store'
            ]);
            exit;
        }

        // Get filtered and paginated orders
        $orders = $orderModel->getAllOrdersPaginated($store_id, $page, $limit, $filters);
        $totalOrders = $orderModel->getTotalOrdersCount($store_id, $filters);
        $totalPages = ceil($totalOrders / $limit);

        // Get order statistics
        $stats = [
            'total_orders' => $orderModel->getTotalOrders($store_id),
            'pending_orders' => $orderModel->getOrdersByStatus($store_id, 'pending'),
            'shipped_orders' => $orderModel->getOrdersByStatus($store_id, 'shipped'),
            'delivered_orders' => $orderModel->getOrdersByStatus($store_id, 'delivered'),
            'cancelled_orders' => $orderModel->getOrdersByStatus($store_id, 'cancelled')
        ];

        // Pass data to view
        $this->view('dashboard/orders/all', [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
            'limit' => $limit,
            'filters' => $filters,
            'stats' => $stats
        ]);
    }
}
