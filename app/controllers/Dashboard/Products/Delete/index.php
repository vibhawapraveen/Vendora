<?php

class Delete extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . 'dashboard/products/all?error=invalid_method');
            exit;
        }

        if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
            header('Location: ' . ROOT . 'dashboard/products/all?error=missing_product_id');
            exit;
        }

        $product_id = $_POST['product_id'];

        // Get current user's store_id from session for security
        $user = Session::user();
        $store_id = $user['store_id'];

        // Load ProductModel
        $productModel = $this->model("ProductModel");

        // First verify the product belongs to this store
        $product = $productModel->getProductById($product_id);

        if (!$product || $product['store_id'] !== $store_id) {
            header('Location: ' . ROOT . 'dashboard/products/all?error=unauthorized');
            exit;
        }

        // Attempt to delete the product
        $result = $productModel->deleteProduct($product_id);

        if ($result) {
            header('Location: ' . ROOT . 'dashboard/products/all?success=product_deleted&product_name=' . urlencode($product['name']));
        } else {
            header('Location: ' . ROOT . 'dashboard/products/all?error=delete_failed');
        }
        exit;
    }
}
