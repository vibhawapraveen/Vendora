<?php

class Sellers extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        Session::requireRole(["admin"], "admin");
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $sellerModel = $this->model("SellerModel");

        // Handle deletion request within the index route
        if (isset($_GET['delete_id'])) {
            if ($sellerModel->deleteSeller($_GET['delete_id'])) {
                header("Location: " . ROOT . "admin/dashboard/sellers?success=seller_deleted");
            } else {
                header("Location: " . ROOT . "admin/dashboard/sellers?error=delete_failed");
            }
            exit;
        }

        // Handle view details request
        if (isset($_GET['view_id'])) {
            $sellerDetails = $sellerModel->getSellerDetailsById($_GET['view_id']);
            if (!$sellerDetails) {
                header("Location: " . ROOT . "admin/dashboard/sellers?error=seller_not_found");
                exit;
            }
            
            $products = $sellerModel->getSellerProducts($_GET['view_id']);
            
            $this->view("admin/sellers/view", [
                'seller' => $sellerDetails,
                'products' => $products
            ]);
            exit;
        }
        
        $search = $_GET['search'] ?? '';
        $sellers = $sellerModel->getAdminSellers($search);

        $this->view("admin/sellers/index", [
            'sellers' => $sellers,
            'search' => $search
        ]);
    }
}