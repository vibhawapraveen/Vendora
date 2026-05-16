<?php

class Newcustomer extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);
            $address1 = trim($_POST['address1']); 
            $address2 = trim($_POST['address2']);
            $city = trim($_POST['city']);

            if ($password !== $confirm_password) {
                $error = "Passwords do not match!";
                $this->view('dashboard/customers/new', ['error' => $error]);
                return;
            }

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $customer_info = [
                'name' => $name,
                'email' => $email,
                'password_hash' => $password_hash,
                'address1' => $address1,
                'address2' => $address2,
                'city' => $city
            ];

            $CustomerModel = $this->model("CustomerModel");
            $CustomerModel->addNewCustomer($customer_info);

            $success = "New customer added successfully!";
            $this->view('dashboard/customers/new', ['success' => $success]);
            return;
        }

        $this->view('dashboard/customers/new');
    }
}
