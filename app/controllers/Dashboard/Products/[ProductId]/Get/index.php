<?php

class Get extends Controller
{
    private $product_id;

    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);

        // Get product_id from URL slug
        $this->product_id = $SLUG_DATA['ProductId'] ?? null;

        // If no product_id, return error
        if (!$this->product_id) {
            $this->jsonResponse(['error' => 'Product ID is required'], 400);
            exit;
        }

        // Get product data
        $this->index();
    }

    public function index()
    {
        // Get current user's store_id from session for security
        $user = Session::user();
        $store_id = $user['store_id'];

        // Load ProductModel
        $productModel = $this->model("ProductModel");

        // Get product and verify ownership
        $product = $productModel->getProductById($this->product_id);

        if (!$product) {
            $this->jsonResponse(['error' => 'Product not found'], 404);
            exit;
        }

        if ($product['store_id'] !== $store_id) {
            $this->jsonResponse(['error' => 'Unauthorized'], 403);
            exit;
        }

        // Prepare response data
        $responseData = [
            'product' => $product,
            'variants' => []
        ];

        // Get variants if it's a variant product
        if ($product['is_variant'] == 1) {
            $variants = $productModel->getProductVariantsWithAttributes($this->product_id);
            $responseData['variants'] = $variants;
        }

        // Return JSON response
        $this->jsonResponse($responseData, 200);
    }

    private function jsonResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
