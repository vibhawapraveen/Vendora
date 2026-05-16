<?php

class All extends Controller
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

        // Export products as printable view (Save as PDF from print dialog).
        if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
            $exportProducts = $productModel->getAllProducts($store_id);

            foreach ($exportProducts as &$product) {
                if ((int)$product['is_variant'] === 1) {
                    $product['stock_quantity'] = $productModel->getTotalVariantStock($product['id']);
                    $product['price_range'] = $productModel->getVariantPriceRange($product['id']);
                }
            }
            unset($product);

            $this->view('dashboard/products/print_all', [
                'products' => $exportProducts,
                'store_name' => Session::user()['store_name'] ?? 'Your Store'
            ]);
            exit;
        }

        // Get current page from URL parameter
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $limit = 5; // Products per page

        // Get statistics
        $stats = [
            'total_products' => $productModel->getTotalProducts($store_id),
            'active_products' => $productModel->getActiveProducts($store_id),
            'low_stock' => $productModel->getLowStockProducts($store_id, 10), // 10 is threshold
            'total_value' => $productModel->getTotalValue($store_id)
        ];

        // Get paginated products
        $products = $productModel->getProductsPaginated($store_id, $page, $limit);
        $totalProducts = $productModel->getTotalProducts($store_id);
        $totalPages = ceil($totalProducts / $limit);

        // For variant products, get total stock and price range from variants
        // Also get first product image for all products
        foreach ($products as &$product) {
            if ($product['is_variant'] == 1) {
                $product['stock_quantity'] = $productModel->getTotalVariantStock($product['id']);
                $product['price_range'] = $productModel->getVariantPriceRange($product['id']);
            }
            // Get first image
            $product['first_image'] = $productModel->getFirstProductImage($product['id'], $product['is_variant']);
        }
        unset($product); // Break reference

        // Pass data to view
        $this->view('dashboard/products/all', [
            'stats' => $stats,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'limit' => $limit
        ]);
    }
}
