<?php

class Payments extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $adminPaymentModel = $this->model("AdminPaymentModel");

        // --- Revenue Graph Filter ---
        $selectedMonth = $_GET['month'] ?? date('n');
        $selectedYear = $_GET['year'] ?? date('Y');
        
        // Month names for the filter dropdown
        $monthsList = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthsList[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }

        // Monthly Stats
        $monthlyRevenue = $adminPaymentModel->getMonthlyRevenue($selectedYear, $selectedMonth);
        $dailyRevenueData = $adminPaymentModel->getDailyRevenue($selectedYear, $selectedMonth);
        
        // Get total daily revenue for "Current Day" (if today matches selected month/year)
        $todayRevenue = 0;
        if ($selectedMonth == date('n') && $selectedYear == date('Y')) {
            foreach ($dailyRevenueData as $data) {
                if ($data['day'] == date('j')) {
                    $todayRevenue = $data['revenue'];
                    break;
                }
            }
        }

        // --- Transaction Table Filters ---
        $filters = [
            'status' => $_GET['status'] ?? '',
            'method' => $_GET['method'] ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? ''
        ];
        
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        
        $transactions = $adminPaymentModel->getAdminTransactions($filters, $page, $limit);
        $totalTransactions = $adminPaymentModel->getAdminTransactionsCount($filters);
        $totalPages = ceil($totalTransactions / $limit);

        $this->view("admin/payments/index", [
            'monthlyRevenue' => $monthlyRevenue,
            'todayRevenue' => $todayRevenue,
            'dailyRevenueData' => $dailyRevenueData,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'monthsList' => $monthsList,
            'transactions' => $transactions,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalTransactions' => $totalTransactions
        ]);
    }
}

?>