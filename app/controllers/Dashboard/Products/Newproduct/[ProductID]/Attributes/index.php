<?php

class Attributes extends Controller
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
        // Handle AJAX POST request for attribute submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handleAttributeSubmission();
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

        // Get existing attributes if any (for edit mode)
        $existingAttributes = $productModel->getProductAttributesWithValues($this->product_id);

        // Pass product data to the view
        $this->view('dashboard/products/attributes', [
            'product_id' => $this->product_id,
            'product' => $product,
            'existingAttributes' => $existingAttributes
        ]);
    }

    private function handleAttributeSubmission()
    {
        // Set JSON response header
        header('Content-Type: application/json');

        try {
            // Get JSON data from request
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);

            if (!$data || !isset($data['attributes'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid data format'
                ]);
                return;
            }

            // Validate that we have at least one attribute
            if (empty($data['attributes'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please add at least one attribute'
                ]);
                return;
            }

            // Validate each attribute has values
            foreach ($data['attributes'] as $attribute) {
                if (empty($attribute['name']) || empty($attribute['values'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Each attribute must have a name and at least one value'
                    ]);
                    return;
                }
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

            // Delete existing attributes (in case of re-edit)
            $productModel->deleteAllProductAttributes($this->product_id);

            // Save new attributes and values
            foreach ($data['attributes'] as $attribute) {
                // Save attribute
                $attribute_id = $productModel->saveProductAttributes(
                    $this->product_id,
                    trim($attribute['name'])
                );

                if ($attribute_id) {
                    // Save attribute values
                    foreach ($attribute['values'] as $value) {
                        $productModel->saveAttributeValue($attribute_id, trim($value));
                    }
                }
            }

            // Return success response with redirect URL
            echo json_encode([
                'success' => true,
                'message' => 'Attributes saved successfully',
                'redirect_url' => ROOT . 'dashboard/products/newproduct/' . $this->product_id . '/variants'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error saving attributes: ' . $e->getMessage()
            ]);
        }
    }
}
