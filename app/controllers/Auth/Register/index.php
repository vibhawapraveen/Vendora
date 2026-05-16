<?php

class Register extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view("auth/register_2");
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->POST();
        }
    }

    public function POST() {
        // Get form data
        $name = trim($_POST['name'] ?? '');
        $storeName = trim($_POST['store_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validate required fields
        if (empty($name) || empty($storeName) || empty($email) || empty($mobile) || empty($password)) {
            $this->view("auth/register_2", ['error' => 'All fields are required']);
            return;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view("auth/register_2", ['error' => 'Invalid email format']);
            return;
        }

        // Validate password match
        if ($password !== $passwordConfirm) {
            $this->view("auth/register_2", ['error' => 'Passwords do not match']);
            return;
        }

        // Validate password strength (minimum 6 characters)
        if (strlen($password) < 6) {
            $this->view("auth/register_2", ['error' => 'Password must be at least 6 characters']);
            return;
        }

        // Register seller
        $sellerModel = $this->model("SellerModel");
        $user = $sellerModel->registerSeller($email, $password, $name, $mobile, $storeName);

        if ($user) {
            Session::login($user);
            // Registration successful, redirect to login
            header("Location: " . ROOT . "dashboard/onboarding?welcome=1");
            exit;
        } else {
            $this->view("auth/register_2", ['error' => 'Email already exists. Please use a different email.']);
        }
    }
}
