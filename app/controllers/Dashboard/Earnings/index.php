<?php

class Earnings extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $user = Session::user();
        $store_id = $user['store_id'];
        
        // Get page number from query params
        $page = $_GET['page'] ?? 1;
        $page = max(1, intval($page));
        $limit = 10;

        // Build filters from query params
        $filters = [
            'status' => $_GET['status'] ?? '',
            'method' => $_GET['method'] ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? ''
        ];

        $earningsM = $this->model('EarningsModel');
        
        // Get all-time data
        $allTimeData = $earningsM->getTotalEarnings($store_id) ?? [];
        
        // Get current month data
        $monthData = $earningsM->getCurrentMonthEarnings($store_id) ?? [];
        $pendingData = $earningsM->getPendingMonthEarnings($store_id) ?? [];
        
        // Get payment transactions
        $payments = $earningsM->getPaymentTransactions($store_id, $page, $limit, $filters);
        $totalTransactions = $earningsM->getPaymentTransactionsCount($store_id, $filters);
        $totalPages = ceil($totalTransactions / $limit);

        // Get monthly breakdown
        $monthlyBreakdown = $earningsM->getMonthlyBreakdown($store_id, 12);

        // Pass data to view
        $this->view('dashboard/earnings/index', [
            'allTimeData' => $allTimeData,
            'monthData' => $monthData,
            'pendingData' => $pendingData,
            'payments' => $payments,
            'page' => $page,
            'limit' => $limit,
            'totalTransactions' => $totalTransactions,
            'totalPages' => $totalPages,
            'filters' => $filters,
            'monthlyBreakdown' => $monthlyBreakdown
        ]);
    }
}

?>