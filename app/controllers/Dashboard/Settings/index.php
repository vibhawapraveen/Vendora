<?php

class Settings extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $sellerModel = $this->model("SellerModel");
        $sellerId = Session::user()['id'] ?? null;
        $storeId = Session::user()['store_id'] ?? null;
        $storeName = Session::user()['store_name'] ?? '';

        if (!$sellerId) {
            header("Location: " . ROOT . "auth/seller");
            exit;
        }

        $seller = $sellerModel->getSellerById($sellerId);
        $tab = $_GET['tab'] ?? 'profile';
        $success = '';
        $error = '';

        // Handle Profile Update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

            if ($_POST['action'] === 'update_profile') {
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $mobile = trim($_POST['mobile_number'] ?? '');
                $newStoreName = trim($_POST['store_name'] ?? '');

                if (empty($name) || empty($email)) {
                    $error = 'Name and email are required.';
                } else {
                    $sellerModel->updateSellerProfile($sellerId, $name, $email, $mobile);

                    if ($storeId && !empty($newStoreName)) {
                        $sellerModel->updateStoreName($storeId, $newStoreName);
                        $_SESSION['user']['store_name'] = $newStoreName;
                    }

                    $_SESSION['user']['name'] = $name;
                    $_SESSION['user']['email'] = $email;

                    // Handle profile picture upload
                    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/vendora/public/uploads/sellers/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                        $filename = 'seller_' . $sellerId . '_' . time() . '.' . $ext;
                        $uploadPath = $uploadDir . $filename;

                        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
                            $dbPath = 'uploads/sellers/' . $filename;
                            $sellerModel->updateSellerProfilePicture($sellerId, $dbPath);
                            $_SESSION['user']['profile_picture'] = $dbPath;
                        }
                    }

                    $success = 'Profile updated successfully!';
                    $seller = $sellerModel->getSellerById($sellerId);
                }
                $tab = 'profile';
            }

            if ($_POST['action'] === 'change_password') {
                $current = $_POST['current_password'] ?? '';
                $new = $_POST['new_password'] ?? '';
                $confirm = $_POST['confirm_password'] ?? '';

                if (empty($current) || empty($new) || empty($confirm)) {
                    $error = 'All password fields are required.';
                } elseif ($new !== $confirm) {
                    $error = 'New passwords do not match.';
                } elseif (strlen($new) < 6) {
                    $error = 'New password must be at least 6 characters.';
                } else {
                    $result = $sellerModel->updateSellerPassword($sellerId, $current, $new);
                    if ($result) {
                        $success = 'Password changed successfully!';
                    } else {
                        $error = 'Current password is incorrect.';
                    }
                }
                $tab = 'password';
            }
        }

        $this->view('dashboard/settings/index', [
            'seller' => $seller,
            'storeName' => $storeName,
            'storeId' => $storeId,
            'tab' => $tab,
            'success' => $success,
            'error' => $error
        ]);
    }
}

?>