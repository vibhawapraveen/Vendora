<?php

class Customers extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $customerModel = $this->model("CustomerModel");
        
        if (isset($_GET['view_id'])) {
            $customerId = $_GET['view_id'];
            $customer = $customerModel->getCustomerById($customerId);
            
            if (!$customer) {
                header("Location: " . ROOT . "admin/dashboard/customers");
                exit;
            }
            
            $orders = $customerModel->getCustomerOrders($customerId);
            
            $totalOrders = count($orders);
            $totalSpent = 0;
            $lastOrder = null;
            if ($totalOrders > 0) {
                foreach ($orders as $o) {
                    $totalSpent += $o['total_amount'];
                }
                $lastOrder = $orders[0]['created_at'];
            }
            
            $this->view("admin/customers/view", [
                'customer' => $customer,
                'orders' => $orders,
                'totalOrders' => $totalOrders,
                'totalSpent' => $totalSpent,
                'lastOrder' => $lastOrder
            ]);
            exit;
        }

        $search = $_GET['search'] ?? '';
        $customers = $customerModel->getAdminCustomers($search);

        $this->view("admin/customers/index", [
            'customers' => $customers,
            'search' => $search
        ]);
    }
}

?>