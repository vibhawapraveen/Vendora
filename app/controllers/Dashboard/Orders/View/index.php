<?php

class View extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Get order ID from URL parameter
        $orderId = $_GET['id'] ?? null;
        
        if (!$orderId) {
            // Redirect back to orders page if no ID provided
            header("Location: " . ROOT . "dashboard/orders/all");
            exit;
        }

        // Get current user's store_id from session
        $user = Session::user();
        $store_id = $user['store_id'];

        // Load OrderModel
        $orderModel = $this->model("OrderModel");

        // Get complete order details
        $orderDetails = $orderModel->getOrderDetails($orderId, $store_id);
        
        if (!$orderDetails) {
            // Order not found or doesn't belong to this store
            header("Location: " . ROOT . "dashboard/orders/all");
            exit;
        }

        // Pass data to view
        $this->view('dashboard/orders/view', [
            'order' => $orderDetails['order'],
            'orderItems' => $orderDetails['items'],
            'customer' => $orderDetails['customer']
        ]);
    }
}