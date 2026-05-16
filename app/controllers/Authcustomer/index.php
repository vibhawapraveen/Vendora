<?php

class Authcustomer extends Controller
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
        $this->view('authcustomer/login');
    }

    public function POST()
    {
        $password = trim($_POST['password'] ?? '');
        $loginType = trim($_POST['login_type'] ?? 'email');

        // load CustomerModel
        $customerModel = $this->model("CustomerModel");

        $user = null;

        // Check login based on method
        if ($loginType === 'mobile') {
            $mobile = trim($_POST['mobile'] ?? '');
            if (empty($mobile)) {
                $error = "Mobile number is required";
                $this->view("authcustomer/login", ['error' => $error]);
                return;
            }
            $user = $customerModel->checkLoginByMobile($mobile, $password);
        } else {
            // Default to email login
            $email = trim($_POST['email'] ?? '');
            if (empty($email)) {
                $error = "Email is required";
                $this->view("authcustomer/login", ['error' => $error]);
                return;
            }
            $user = $customerModel->checkLogin($email, $password);
        }

        if ($user) {
            Session::loginCustomer([
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name']
            ]);
            header("Location: " . $_GET['redirect_url'] ?? ROOT);
            exit;
        } else {
            if ($loginType === 'mobile') {
                $error = "Invalid mobile number or password";
            } else {
                $error = "Invalid email or password";
            }
            $this->view("authcustomer/login", ['error' => $error]);
            return;
        }
    }
}
