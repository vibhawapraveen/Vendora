<?php

class Onboarding extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view("dashboard/onboarding/index");
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->POST();
        }
    }

    public function POST()
    {
        $user = Session::user();
        $storeModel = $this->model("StoreModel");

        // Get and sanitize store code
        $storeCode = trim($_POST['store_code'] ?? '');

        // Validate store code format
        if (empty($storeCode)) {
            $this->view("dashboard/onboarding/index", ['error' => 'Store code is required']);
            return;
        }

        // Validate format: only lowercase letters, numbers, and hyphens
        if (!preg_match('/^[a-z0-9-]+$/', $storeCode)) {
            $this->view("dashboard/onboarding/index", ['error' => 'Store code can only contain lowercase letters, numbers, and hyphens']);
            return;
        }

        // Check minimum length
        if (strlen($storeCode) < 3) {
            $this->view("dashboard/onboarding/index", ['error' => 'Store code must be at least 3 characters long']);
            return;
        }

        // Check if store code already exists
        if ($storeModel->isStoreCodeTaken($storeCode)) {
            $this->view("dashboard/onboarding/index", ['error' => 'This store code is already taken. Please choose another one']);
            return;
        }

        // Update store code
        $updated = $storeModel->updateStoreCode($user['store_id'], $storeCode);

        if ($updated) {
            // Redirect to dashboard on success
            header("Location: " . ROOT . "dashboard");
            exit;
        } else {
            $this->view("dashboard/onboarding/index", ['error' => 'Failed to update store code. Please try again']);
        }
    }
}
