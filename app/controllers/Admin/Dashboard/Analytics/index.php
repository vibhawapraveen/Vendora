<?php

class Analytics extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Load Model
        $dashboardModel = $this->model("AdminDashboardModel");

        // Fetch Data
        $salesByCategory = $dashboardModel->getSalesByCategory();
        $topSellingStores = $dashboardModel->getTopStores(10); // Get top 10 for analytics
        $sellerSignups = $dashboardModel->getNewSellerSignups();
        $monthlyOrders = $dashboardModel->getMonthlyOrders();

        // Pass to view
        $this->view("admin/analytics/index", [
            'salesByCategory' => $salesByCategory,
            'topSellingStores' => $topSellingStores,
            'sellerSignups' => $sellerSignups,
            'monthlyOrders' => $monthlyOrders
        ]);
    }
}

?>