<?php

class Orders extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Get current user's store_id from session
        $user = Session::user();
        $store_id = $user['store_id'];

        // Load OrderModel
        $orderModel = $this->model("OrderModel");

        // Get recent orders (last 5)
        $recentOrders = $orderModel->getRecentOrders($store_id, 5);

        // Get order statistics for the cards
        $stats = [
            'total_orders' => $orderModel->getTotalOrders($store_id),
            'pending_orders' => $orderModel->getOrdersByStatus($store_id, 'pending'),
            'shipped_orders' => $orderModel->getOrdersByStatus($store_id, 'shipped'),
            'delivered_orders' => $orderModel->getOrdersByStatus($store_id, 'delivered'),
            'cancelled_orders' => $orderModel->getOrdersByStatus($store_id, 'cancelled')
        ];

        // Get data for the 'Orders Over Time' graph
        $graphData = [
            '7' => $orderModel->getOrdersOverTime($store_id, 7),
            '30' => $orderModel->getOrdersOverTime($store_id, 30),
            '90' => $orderModel->getOrdersOverTime($store_id, 90)
        ];

        // Pass data to view
        $this->view('dashboard/orders/index', [
            'recentOrders' => $recentOrders,
            'stats' => $stats,
            'graphData' => $graphData
        ]);
    }
}

?>