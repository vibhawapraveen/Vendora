<?php

class Products extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $adminProductModel = $this->model("AdminProductModel");
        $productModel = $this->model("ProductModel");

        // Export all products as printable view (Save as PDF from print dialog).
        if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
            $exportProducts = $adminProductModel->getAllProducts();

            foreach ($exportProducts as &$product) {
                if ((int)$product['is_variant'] === 1) {
                    $product['stock_quantity'] = $productModel->getTotalVariantStock($product['id']);
                    $product['price_range'] = $productModel->getVariantPriceRange($product['id']);
                }
            }
            unset($product);

            $this->view("admin/products/print_all", [
                'products' => $exportProducts,
                'report_name' => 'Vendora Admin'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $productId = isset($_POST['product_id']) ? $_POST['product_id'] : '';

            if ($action === 'ban_product') {
                if ($productId > 0) {
                    $adminProductModel->banProductById($productId);
                }

                header('Location: ' . ROOT . 'admin/dashboard/products');
                exit;
            }

            if ($action === 'unban_product') {
                if ($productId > 0) {
                    $adminProductModel->unbanProductById($productId);
                }

                header('Location: ' . ROOT . 'admin/dashboard/products');
                exit;
            }
        }


        // Get current page from URL parameter
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $limit = 10; // Products per page

        $products = $adminProductModel->getProductsPaginated($page, $limit);

        // For variant products, get total stock and price range from variants and image
        foreach ($products as &$product) {
            if ($product['is_variant'] == 1) {
                $product['stock_quantity'] = $productModel->getTotalVariantStock($product['id']);
                $product['price_range'] = $productModel->getVariantPriceRange($product['id']);
            }
            // Get first image
            $product['first_image'] = $productModel->getFirstProductImage($product['id'], $product['is_variant']);
        }
        unset($product); // Break reference

        $stats = [
            'total_products' => $adminProductModel->getGlobalTotalProducts(),
            'active_products' => $adminProductModel->getGlobalActiveProducts(),
            'total_platform_fee' => $adminProductModel->getPlatformRevenue(),
            'banned_products' => $adminProductModel->getGlobalTotalBannedProducts()
        ];

        // Calculate pagination info using actual product count (including deleted/banned)
        $totalProducts = $adminProductModel->getTotalProductsForPagination();
        $totalPages = ceil($totalProducts / $limit);
        $totalPages = max(1, $totalPages);
        $page = min($page, $totalPages); // Ensure page doesn't exceed total pages

        $this->view("admin/products/index", [
            'stats' => $stats,
            'products' => $products,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalProducts' => $totalProducts,
                'limit' => $limit
            ]
        ]);
    }
}
