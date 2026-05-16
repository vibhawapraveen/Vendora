<?php

class Authenticate extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate input
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validation
            if (empty($email) || empty($password)) {
                $_SESSION['login_error'] = "Email and password are required";
                header("Location: " . ROOT . "admin");
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['login_error'] = "Invalid email format";
                header("Location: " . ROOT . "admin");
                exit;
            }

            // Load AdminModel
            $adminModel = $this->model("AdminModel");

            // Check admin credentials
            $admin = $adminModel->checkLogin($email, $password);

            if ($admin) {
                // Login successful
                Session::loginAdmin($admin);

                $_SESSION['login_success'] = "Welcome back, " . $admin['name'] . "!";
                header("Location: " . ROOT . "admin/dashboard");
                exit;
            } else {
                // Login failed
                $_SESSION['login_error'] = "Invalid email or password";
                header("Location: " . ROOT . "admin");
                exit;
            }
        } else {
            // If not POST, redirect to login page
            header("Location: " . ROOT . "admin");
            exit;
        }
    }
}

?>
