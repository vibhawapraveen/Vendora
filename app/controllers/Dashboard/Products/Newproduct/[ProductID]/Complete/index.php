<?php

class Complete extends Controller
{
    private $product_id;

    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);

        // Extract product ID from SLUG_DATA
        $this->product_id = $SLUG_DATA['ProductID'];

        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // Handle POST request for visibility update
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->updateVisibility();
            return;
        }

        // Load ProductModel to get product details
        $productModel = $this->model("ProductModel");
        $product = $productModel->getProductById($this->product_id);

        // Ensure user owns this product
        $user = Session::user();
        if (!$product || $product['store_id'] != $user['store_id']) {
            header('Location: ' . ROOT . 'dashboard/products');
            exit;
        }

        // Pass product data to the view
        $this->view('dashboard/products/complete', [
            'product_id' => $this->product_id,
            'product' => $product
        ]);
    }

    private function updateVisibility()
    {
        $visibility = isset($_POST['visibility']) ? (int)$_POST['visibility'] : 0;

        $productModel = $this->model("ProductModel");

        // Get current product data first
        $product = $productModel->getProductById($this->product_id);

        // Update product with current data plus new visibility.
        // For variant products, keep stock data out of the update payload.
        $updateData = [
            'name' => $product['name'],
            'description' => $product['description'],
            'visibility' => $visibility
        ];

        if ((int)($product['is_variant'] ?? 0) === 0) {
            $updateData['price'] = $product['price'] ?? 0;
            $updateData['stock_quantity'] = $product['stock_quantity'] ?? 0;
            $updateData['low_stock_alert'] = $product['low_stock_alert'] ?? null;
        }

        $result = $productModel->updateProduct($this->product_id, $updateData);

        if ($result) {
            // Return success response for AJAX
            echo json_encode(['success' => true]);
        } else {
            // Return error response
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update visibility']);
        }
        exit;
    }
}
