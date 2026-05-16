<?php

class Variants extends Controller
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
        // Handle POST request for variants submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handleVariantsSubmission();
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

        // Check if product is variant type
        if (!$product['is_variant']) {
            // Redirect non-variant products to base pricing
            header('Location: ' . ROOT . 'dashboard/products/newproduct/' . $this->product_id . '/basepricing');
            exit;
        }

        // Generate variants from attributes
        $variants = $productModel->generateVariantCombinations($this->product_id);

        // If no variants generated, redirect back to attributes
        if (empty($variants)) {
            header('Location: ' . ROOT . 'dashboard/products/newproduct/' . $this->product_id . '/attributes?error=no_attributes');
            exit;
        }

        // Pass product data and generated variants to the view
        $this->view('dashboard/products/variants', [
            'product_id' => $this->product_id,
            'product' => $product,
            'variants' => $variants
        ]);
    }

    private function handleVariantsSubmission()
    {
        // Get JSON data
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        // Set JSON response header
        header('Content-Type: application/json');

        try {
            if (!$data || !isset($data['variants'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid data format'
                ]);
                return;
            }

            // Load ProductModel
            $productModel = $this->model("ProductModel");

            // Verify product ownership
            $product = $productModel->getProductById($this->product_id);
            $user = Session::user();

            if (!$product || $product['store_id'] != $user['store_id']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
                return;
            }

            // Delete existing variants (in case of re-edit)
            $productModel->deleteAllProductVariants($this->product_id);

            // Save enabled variants
            $savedCount = 0;
            foreach ($data['variants'] as $variant) {
                if ($variant['enabled']) {
                    $result = $productModel->saveProductVariant(
                        $this->product_id,
                        $variant['sku'],
                        $variant['price'],
                        $variant['stock'],
                        $variant['attribute_value_ids'],
                        $variant['low_stock_alert'] ?? null
                    );
                    if ($result) {
                        $savedCount++;
                    }
                }
            }

            // Return success response
            echo json_encode([
                'success' => true,
                'message' => $savedCount . ' variants saved successfully',
                'redirect_url' => ROOT . 'dashboard/products/newproduct/' . $this->product_id . '/media'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error saving variants: ' . $e->getMessage()
            ]);
        }
    }
}
