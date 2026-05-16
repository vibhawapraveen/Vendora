<?php

class All extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $CustomerModel = $this->model("CustomerModel");
        
        // Get store_id from session
        $store_id = Session::user()['store_id'] ?? null;
        if (!$store_id) {
            // Handle case where store_id is not available
            die("Store ID not found in session");
        }

        // Get current page from GET parameter, default to 1
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Get search keyword from GET parameter
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Filters / sorting
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $orderSort = isset($_GET['order_sort']) ? trim($_GET['order_sort']) : '';
        $spentSort = isset($_GET['spent_sort']) ? trim($_GET['spent_sort']) : '';

        // Rows per page
        $perPage = 6;

        // Fetch paginated customers
        $paginatedCustomers = $CustomerModel->getCustomersPaginated($store_id, $page, $perPage, $search, $status, $orderSort, $spentSort);

        // Fetch total customers (for pagination)
        $totalCustomersData = $CustomerModel->getTotalCustomers($store_id, $search, $status);
        $totalCustomersCount = $totalCustomersData['total_customers'];
        $totalPages = ceil($totalCustomersCount / $perPage);

        // Fetch dashboard metrics
        $totalCustomers = $CustomerModel->getTotalCustomers($store_id);
        $newCustomersThisMonth = $CustomerModel->getNewCustomersThisMonth($store_id);
        $totalRevenue = $CustomerModel->getTotalRevenue($store_id);

        // Prepare data for view
        $data = [
            'customers' => $paginatedCustomers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'status' => $status,
            'orderSort' => $orderSort,
            'spentSort' => $spentSort,
            'totalCustomers' => $totalCustomers,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'totalRevenue' => $totalRevenue,
        ];

        $this->view('dashboard/customers/all/index', $data);
    }
}
