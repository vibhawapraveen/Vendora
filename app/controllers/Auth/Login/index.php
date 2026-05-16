<?php

class Login extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // load SellerModel
            $sellerModel = $this->model("SellerModel");

            // check login
            $user = $sellerModel->checkLogin($email, $password);

            // print_r($user);
            if ($user) {
                Session::login([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'mobile_number' => $user['mobile_number'],
                    'profile_picture' => $user['profile_picture'],
                    'store_name' => $user['store_name'],
                    'store_id' => $user['store_id'],
                    'role' => 'seller'
                ]);
                header("Location: " . ROOT . "dashboard");
                exit;
            } else {
                $error = "Invalid email or password";
                $this->view("auth/login_2", ['error' => $error]);
                return;
            }
        }

        $this->view("auth/login_2");
    }
}
