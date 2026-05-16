<?php

class Register extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->GET();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->POST();
        }
    }

    public function GET()
    {
        $this->view('authcustomer/register');
    }

    public function POST()
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');

        $customerModel = $this->model("CustomerModel");

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = "Name is required";
        }

        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }

        if (empty($mobile)) {
            $errors[] = "Mobile number is required";
        }

        if (empty($password)) {
            $errors[] = "Password is required";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long";
        }

        // Check if customer already exists by email
        if (empty($errors)) {
            $existingCustomer = $customerModel->getCustomerByEmail($email);
            if ($existingCustomer) {
                $errors[] = "An account with this email already exists";
            }
        }

        // Check if mobile number already exists
        $existingMobile = null;
        if (empty($errors)) {
            $existingMobile = $customerModel->getCustomerByMobile($mobile);
            if ($existingMobile && !empty($existingMobile['email'])) {
                // Mobile exists with an email, can't register
                $errors[] = "An account with this mobile number already exists";
            }
        }

        // If there are errors, return to form with errors
        if (!empty($errors)) {
            $error = implode(". ", $errors);
            $this->view("authcustomer/register", ['error' => $error]);
            return;
        }

        // Check if we're linking to an existing customer with mobile but no email
        if ($existingMobile && empty($existingMobile['email'])) {
            // Update existing customer with email, password, and name
            $result = $customerModel->updateCustomerEmailAndPassword(
                $existingMobile['id'],
                $name,
                $email,
                $password
            );

            if ($result) {
                $customer = $customerModel->getCustomerById($existingMobile['id']);
                Session::loginCustomer($customer);
                $redirect_url = $_GET['redirect_url'] ?? ROOT;
                header("Location: " . $redirect_url);
                exit;
            } else {
                $error = "Registration failed. Please try again.";
                $this->view("authcustomer/register", ['error' => $error]);
                return;
            }
        }

        // Create new customer
        $result = $customerModel->createCustomerAccount($name, $email, $password, $mobile);

        if ($result) {
            $customer = $customerModel->getCustomerByEmail($email);
            Session::loginCustomer($customer);
            $redirect_url = $_GET['redirect_url'] ?? ROOT;
            header("Location: " . $redirect_url);
            exit;
        } else {
            $error = "Registration failed. Please try again.";
            $this->view("authcustomer/register", ['error' => $error]);
        }
    }
}
