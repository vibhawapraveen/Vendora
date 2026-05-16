<?php
class Basepricing extends Controller
{
    private $product_id;

    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require_once __DIR__ . "/../../../../../../models/ProductModel.php";

        // Extract product ID from SLUG_DATA
        $this->product_id = $SLUG_DATA['ProductID'];

        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->updatePricing();
            return;
        }

        // Get product details to display
        $productModel = $this->model("ProductModel");
        $product = $productModel->getProductById($this->product_id);

        if (!$product) {
            header('Location: ' . ROOT . 'dashboard/products');
            exit;
        }

        // Pass product data to view
        $data = [
            'product' => $product,
            'product_id' => $this->product_id
        ];

        echo $this->view('dashboard/products/basepricing', $data);
    }

    private function updatePricing()
    {
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];
        $low_stock_alert = $_POST['low_stock_alert'] ?? null;

        $productModel = $this->model("ProductModel");
        $result = $productModel->updateProductPricing($this->product_id, $price, $stock_quantity, $low_stock_alert);

        if ($result) {
            // Redirect to media upload phase
            header('Location: ' . ROOT . 'dashboard/products/newproduct/' . $this->product_id . '/media');
        } else {
            // Redirect back to pricing form on failure
            header('Location: ' . ROOT . 'dashboard/products/newproduct/' . $this->product_id . '/basepricing?error=1');
        }
        exit;
    }
}
