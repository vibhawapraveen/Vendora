<?php

class Products extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Get current user's store_id from session
        $user = Session::user();
        $store_id = $user['store_id'];

        // Load ProductModel
        $productModel = $this->model("ProductModel");

        // Get Statistics
        $stats = [
            'total_products' => $productModel->getTotalProducts($store_id),
            'active_products' => $productModel->getActiveProducts($store_id),
            'inactive_products' => $productModel->getInactiveProducts($store_id),
            'low_stock' => $productModel->getLowStockProducts($store_id, 10) // 10 is threshold
        ];

        // Get recent products for the table (limit to 5)
        $recentProducts = $productModel->getRecentProducts($store_id, 5);

        // For variant products, get total stock from variants
        // Also get first product image for all products
        foreach ($recentProducts as &$product) {
            if ($product['is_variant'] == 1) {
                $product['stock_quantity'] = $productModel->getTotalVariantStock($product['id']);
            }
            // Get first image
            $product['first_image'] = $productModel->getFirstProductImage($product['id'], $product['is_variant']);
        }
        unset($product); // Break reference

        //Load ProductModel
        $this->view(
            'dashboard/products/index',
            [
                'stats' => $stats,
                'recentProducts' => $recentProducts
            ]
        );
    }
}
