<?php

class Dashboard extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        Session::requireRole(["admin"], "admin");
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Load Model
        $dashboardModel = $this->model("AdminDashboardModel");

        // Fetch Data
        $stats = $dashboardModel->getStats();
        $revenueData = $dashboardModel->getMonthlyRevenue();
        $ordersData = $dashboardModel->getMonthlyOrders();
        $topStores = $dashboardModel->getTopStores();

        // Pass to view
        $this->view("admin/index", [
            'stats' => $stats,
            'revenueData' => $revenueData,
            'ordersData' => $ordersData,
            'topStores' => $topStores
        ]);
    }
}

?>