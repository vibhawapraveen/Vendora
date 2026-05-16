<?php

class Changecode extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $user = Session::user();
            $storeModel = $this->model("StoreModel");

            // Get and sanitize store code
            $storeCode = trim($_POST['store_code'] ?? '');

            // Validate store code format
            if (empty($storeCode)) {
                header("Location: ". ROOT . "dashboard/storefront?error=Invalid Store Code");
                return;
            }

            // Validate format: only lowercase letters, numbers, and hyphens
            if (!preg_match('/^[a-z0-9-]+$/', $storeCode)) {
                header("Location: ". ROOT . "dashboard/storefront?error=Store code can only contain lowercase letters, numbers, and hyphens");
                // $this->view("dashboard/storefront", ['error' => '']);
                return;
            }

            // Check minimum length
            if (strlen($storeCode) < 3) {
                header("Location: ". ROOT . "dashboard/storefront?error=Store code must be at least 3 characters long");
                // $this->view("dashboard/storefront", ['error' => '']);
                return;
            }

            // Check if store code already exists
            if ($storeModel->isStoreCodeTaken($storeCode)) {
                header("Location: ". ROOT . "dashboard/storefront?error=This store code is already taken. Please choose another one");
                // $this->view("dashboard/storefront", ['error' => '']);
                return;
            }

            // Update store code
            $updated = $storeModel->updateStoreCode($user['store_id'], $storeCode);

            if ($updated) {
                // Redirect to dashboard on success
                header("Location: " . ROOT . "dashboard/storefront?success=Store code updated");
                exit;
            } else {
                header("Location: ". ROOT . "dashboard/storefront?error=Failed to update store code. Please try again");
                // $this->view("dashboard/storefront", ['error' => '']);
            }
        }
    }
}
