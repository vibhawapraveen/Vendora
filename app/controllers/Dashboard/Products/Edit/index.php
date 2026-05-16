<?php

class Edit extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Handle AJAX requests for getting product data
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && isset($_GET['product_id'])) {
            $this->getProductData($_GET['product_id']);
            return;
        }

        // Handle form submission for updating product
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateProduct();
            return;
        }

        // Redirect if accessed incorrectly
        header('Location: ' . ROOT . 'dashboard/products/all');
        exit;
    }

    private function getProductData($product_id)
    {
        header('Content-Type: application/json');

        try {
            // Get current user's store_id from session for security
            $user = Session::user();
            if (!$user || !isset($user['store_id'])) {
                echo json_encode(['error' => 'User not authenticated or store_id missing']);
                exit;
            }

            $store_id = $user['store_id'];

            // Load ProductModel
            $productModel = $this->model("ProductModel");

            // Get product and verify ownership
            $product = $productModel->getProductById($product_id);

            if (!$product) {
                echo json_encode(['error' => 'Product not found']);
                exit;
            }

            if ($product['store_id'] !== $store_id) {
                echo json_encode(['error' => 'Unauthorized access to product']);
                exit;
            }

            // For variant products, get total stock and variants
            if ($product['is_variant'] == 1) {
                $product['stock_quantity'] = $productModel->getTotalVariantStock($product['id']);
                $product['variants'] = $productModel->getProductVariants($product['id']);
            }

            echo json_encode(['success' => true, 'product' => $product]);
            exit;
        } catch (Exception $e) {
            error_log("Error in getProductData: " . $e->getMessage());
            echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
            exit;
        }
    }

    private function updateProduct()
    {
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

        // Verify product ownership
        $product = $productModel->getProductById($product_id);

        if (!$product || $product['store_id'] !== $store_id) {
            header('Location: ' . ROOT . 'dashboard/products/all?error=unauthorized');
            exit;
        }

        // Prepare update data for product table
        $updateData = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'visibility' => isset($_POST['visibility']) ? 1 : 0
        ];

        // Handle variant vs single product updates differently
        if ($product['is_variant'] == 1) {
            // Variant product - don't update price/stock in products table
            // Update variants instead
            if (isset($_POST['variants']) && is_array($_POST['variants'])) {
                foreach ($_POST['variants'] as $variantData) {
                    if (isset($variantData['id'], $variantData['price'], $variantData['stock'])) {
                        $productModel->updateProductVariant(
                            $variantData['id'],
                            $variantData['price'],
                            $variantData['stock'],
                            $variantData['low_stock_alert'] ?? null
                        );
                    }
                }
            }
        } else {
            // Single product - update price/stock in products table
            $updateData['price'] = $_POST['price'] ?? null;
            $updateData['stock_quantity'] = $_POST['stock_quantity'] ?? null;
            $updateData['low_stock_alert'] = $_POST['low_stock_alert'] ?? null;
        }

        // Validate required fields
        if (empty($updateData['name'])) {
            header('Location: ' . ROOT . 'dashboard/products/all?error=missing_name');
            exit;
        }

        // Update the product
        $result = $productModel->updateProduct($product_id, $updateData);

        if ($result) {
            header('Location: ' . ROOT . 'dashboard/products/all?success=product_updated&product_name=' . urlencode($updateData['name']));
        } else {
            header('Location: ' . ROOT . 'dashboard/products/all?error=update_failed');
        }
        exit;
    }
}
