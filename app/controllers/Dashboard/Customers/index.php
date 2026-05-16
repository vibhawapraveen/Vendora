<?php

class Customers extends Controller
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

        $getAllCustomer = $CustomerModel->getAllCustomers($store_id);
        $getTotalCustomers = $CustomerModel->getTotalCustomers($store_id);
        $getNewCustomersThisMonth = $CustomerModel->getNewCustomersThisMonth($store_id);
        $totalRevenue   = $CustomerModel->getTotalRevenue($store_id);
        $topCustomersBySpending = $CustomerModel->getTopCustomersBySpending($store_id, 5);


        $data = [
            'allCustomers' => $getAllCustomer,
            'totalCustomers' => $getTotalCustomers,
            'newCustomersThisMonth' => $getNewCustomersThisMonth,
            'totalRevenue' => $totalRevenue,
            'topCustomersBySpending' => $topCustomersBySpending

        ];


        $this->view('dashboard/customers/index', $data);
    }
}
