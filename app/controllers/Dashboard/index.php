<?php

class Dashboard extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Get the current user's store_id from session
        $user = Session::user();
        $store_id = $user['store_id'];

        // load the productModel
        $productModel = $this->model("ProductModel");
        // Load OrderModel
        $orderModel = $this->model("OrderModel");
        // Load DashboardModel
        $dashboardModel = $this->model("DashboardModel");

        $revenueRow = $dashboardModel->getTotalRevenue($store_id);
        $stats = [
            'total_products' => $productModel->getTotalProducts($store_id),
            'total_orders' => $orderModel->getTotalOrders($store_id),
            'active_products' => $productModel->getActiveProducts($store_id),
            'total_revenue' => $revenueRow['total_revenue'] ?? 0
        ];

        // low stock alerts
        $lowStockAlerts = $productModel->getLowStockProductsList($store_id, 10);
        $recentOrders = $productModel->getRecentOrders($store_id, 3);
        $dailyRevenue = $dashboardModel->getDailyRevenueBreakdown($store_id, 30);
        $orderStatusDistribution = $dashboardModel->getStatusDistribution($store_id);
        
        //Load ProductModel
        $this->view(
            'dashboard/index',
            [
                'stats' => $stats,
                'lowStockAlerts' => array_slice($lowStockAlerts, 0, 3),
                'recentOrders' => $recentOrders,
                'dailyRevenue' => $dailyRevenue,
                'orderStatusDistribution' => $orderStatusDistribution
            ]
        );
    }
}

?>