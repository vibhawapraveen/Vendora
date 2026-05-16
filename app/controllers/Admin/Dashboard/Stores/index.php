<?php

class Stores extends Controller
{
    public function __construct($PREV_URL,$URL,$SLUG_DATA=NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $storeModel = $this->model("StoreModel");
        
        // Product deletion handling
        if (isset($_GET['delete_product_id']) && isset($_GET['store_id'])) {
            $productId = $_GET['delete_product_id'];
            $storeId = $_GET['store_id'];
            
            $productModel = $this->model("ProductModel");
            if (method_exists($productModel, 'deleteProduct')) {
                try {
                    $productModel->deleteProduct($productId);
                } catch (\Exception $e) { }
            }
            
            header("Location: " . ROOT . "admin/dashboard/stores?view_id=" . $storeId);
            exit;
        }

        // Product visibility toggle
        if (isset($_GET['toggle_product_id']) && isset($_GET['store_id']) && isset($_GET['current_visibility'])) {
            $productId = $_GET['toggle_product_id'];
            $storeId = $_GET['store_id'];
            $newVisibility = $_GET['current_visibility'] ? 0 : 1;
            
            $productModel = $this->model("ProductModel");
            if (method_exists($productModel, 'updateProductVisibility')) {
                $productModel->updateProductVisibility($productId, $newVisibility);
            }
            
            header("Location: " . ROOT . "admin/dashboard/stores?view_id=" . $storeId);
            exit;
        }

        // Store Visibility Toggle
        if (isset($_GET['toggle_id']) && isset($_GET['current'])) {
            $storeId = $_GET['toggle_id'];
            $newStatus = $_GET['current'] ? 0 : 1;
            
            if (method_exists($storeModel, 'updateStoreVisibility')) {
                $storeModel->updateStoreVisibility($storeId, $newStatus);
            }
            
            header("Location: " . ROOT . "admin/dashboard/stores");
            exit;
        }

        // Store Deletion Handler (cascading cleanup)
        if (isset($_GET['delete_id'])) {
            $storeId = $_GET['delete_id'];
            
            // 1. Identify the store owner
            $sellerId = $storeModel->getSellerIdByStoreId($storeId);
            
            if ($sellerId) {
                // 2. Use SellerModel's robust cascading delete
                $sellerModel = $this->model("SellerModel");
                $sellerModel->deleteSeller($sellerId);
            }
            
            header("Location: " . ROOT . "admin/dashboard/stores");
            exit;
        }

        // Handle view details request
        if (isset($_GET['view_id'])) {
            $storeId = $_GET['view_id'];
            $store = $storeModel->getStoreDetailsInfo($storeId);
            if (!$store) {
                header("Location: " . ROOT . "admin/dashboard/stores");
                exit;
            }
            $products = $storeModel->getStoreProductsWithImages($storeId);
            
            // Attach variant data and fetch correct images
            $productModel = $this->model("ProductModel");
            foreach ($products as &$p) {
                // Fetch image using the reliable ProductModel method if the SQL fallback failed
                if (empty($p['image_url'])) {
                    $p['image_url'] = $productModel->getFirstProductImage($p['id'], $p['is_variant'] == 1);
                }

                if ($p['is_variant'] == 1) {
                    $p['variants'] = $productModel->getProductVariantsWithAttributes($p['id']);
                }
            }

            $this->view("admin/stores/view", [
                'store' => $store,
                'products' => $products
            ]);
            exit;
        }

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? 'all';
        
        $stores = $storeModel->getAdminStores($search, $status);

        $this->view("admin/stores/index", [
            'stores' => $stores,
            'search' => $search,
            'status' => $status
        ]);
    }
}

?>