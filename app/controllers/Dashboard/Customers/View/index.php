<?php

class View extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Load the Customer model
        $CustomerModel = $this->model("CustomerModel");

        // Get customer ID from URL query
        $id = isset($_GET['id']) ? trim($_GET['id']) : null;

        if ($id) {
            // Fetch customer info from database
            $customer = $CustomerModel->getCustomerById($id);

            if ($customer) {
                // Pass customer data to the view
                $this->view("dashboard/customers/view", ['customer' => $customer]);
            } else {
                echo "<h2>Customer not found.</h2>";
            }
        } else {
            echo "<h2>Invalid customer ID.</h2>";
        }
    }
}

?>
