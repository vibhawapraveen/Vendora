<?php
class Newproduct extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require_once __DIR__ . "/../../../../models/ProductModel.php";
        require_once __DIR__ . "/../../../../models/CategoryModel.php";
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->createProduct();
            return;
        }

        $user = Session::user();
        $store_id = $user['store_id'];
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getCategoriesByStore($store_id);

        echo $this->view('dashboard/products/new', [
            'categories' => $categories
        ]);
    }

    private function createProduct()
    {
        // Get current user's store_id from session
        $user = Session::user();
        $store_id = $user['store_id']; // Get store_id from session

        $description = $this->sanitizeRichText($_POST['description'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');

        // Prepare basic product data
        $productData = [
            'store_id' => $store_id,
            'name' => $_POST['product_name'],
            'description' => $description,
            'category_id' => $category_id !== '' ? $category_id : null,
            'is_variant' => $_POST['is_variant'] === 'true' ? 1 : 0,
            'visibility' => 0  // Default to inactive, will be set in complete step
        ];

        $productModel = $this->model("ProductModel");
        $product_id = $productModel->createBasicProduct($productData);

        if ($product_id) {
            // Check if single variant - redirect to pricing page
            if ($_POST['is_variant'] === 'false') {
                header('Location: ' . ROOT . 'dashboard/products/newproduct/' . $product_id . '/basepricing');
            } else {
                // For multi-variant products, redirect to attributes setup
                header('Location: ' . ROOT . 'dashboard/products/newproduct/' . $product_id . '/attributes');
            }
        } else {
            // Redirect back to form on failure
            header('Location: ' . ROOT . 'dashboard/products/newproduct?error=1');
        }
        exit;
    }

    private function sanitizeRichText($html)
    {
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a>';
        $clean = strip_tags($html, $allowedTags);

        // Neutralize javascript URLs in links.
        $clean = preg_replace('/href\s*=\s*"\s*javascript:[^"]*"/i', 'href="#"', $clean);
        $clean = preg_replace('/href\s*=\s*\'\s*javascript:[^\']*\'/i', 'href="#"', $clean);

        return $clean;
    }
}
