<?php

class Edit extends Controller
{
    private $product_id;

    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);

        // Get product_id from URL slug
        $this->product_id = $SLUG_DATA['ProductId'] ?? null;

        // If no product_id, redirect back
        if (!$this->product_id) {
            header('Location: ' . ROOT . 'dashboard/products/all');
            exit;
        }

        // If this is a POST request (form submission), handle the update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpdate();
            exit;
        }

        // Otherwise, show the edit page
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
            header('Location: ' . ROOT . 'dashboard/products/all?error=product_not_found');
            exit;
        }

        if ($product['store_id'] !== $store_id) {
            header('Location: ' . ROOT . 'dashboard/products/all?error=unauthorized');
            exit;
        }

        // Get additional product data based on product type
        $productData = [
            'product' => $product,
            'images' => [],
            'variants' => [],
            'attributes' => []
        ];

        if ($product['is_variant'] == 1) {
            // Multi-variant product
            $productData['variants'] = $productModel->getProductVariantsWithAttributes($this->product_id);
            $productData['attributes'] = $productModel->getProductAttributes($this->product_id);
            $productData['images'] = $productModel->getProductImages($this->product_id);

            // Get attribute values for each attribute
            foreach ($productData['attributes'] as &$attribute) {
                $attribute['values'] = $productModel->getAttributeValues($attribute['id']);
            }
            unset($attribute);
        } else {
            // Single product
            $productData['images'] = $productModel->getProductImages($this->product_id);
        }

        // Load the edit view
        $this->view('dashboard/products/edit', $productData);
    }

    private function handleUpdate()
    {
        // Get current user's store_id from session for security
        $user = Session::user();
        $store_id = $user['store_id'];

        // Load ProductModel
        $productModel = $this->model("ProductModel");

        // Verify product ownership
        $product = $productModel->getProductById($this->product_id);

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
            // Handle main product thumbnail upload for multi-variant products.
            if (isset($_FILES['main_thumbnail'])) {
                $mainThumbnailPath = $this->uploadSingleImage($_FILES['main_thumbnail']);
                if ($mainThumbnailPath) {
                    $productModel->setMainProductImage($this->product_id, $mainThumbnailPath);
                }
            }

            // Variant product - update variants
            if (isset($_POST['variants']) && is_array($_POST['variants'])) {
                foreach ($_POST['variants'] as $variant_id => $variantData) {
                    if (isset($variantData['price'], $variantData['stock'])) {
                        $productModel->updateProductVariant(
                            $variant_id,
                            $variantData['price'],
                            $variantData['stock'],
                            $variantData['low_stock_alert'] ?? null
                        );
                    }
                }
            }

            // Handle variant image uploads
            if (isset($_FILES['variant_images'])) {
                foreach ($_FILES['variant_images']['tmp_name'] as $variant_id => $tmp_name) {
                    if (!empty($tmp_name) && is_uploaded_file($tmp_name)) {
                        $result = $this->uploadVariantImage($variant_id, $_FILES['variant_images'], $variant_id);
                        if ($result) {
                            $productModel->updateVariantImage($variant_id, $result);
                        }
                    }
                }
            }
        } else {
            // Single product - update price/stock in products table
            $updateData['price'] = $_POST['price'] ?? null;
            $updateData['stock_quantity'] = $_POST['stock_quantity'] ?? null;
            $updateData['low_stock_alert'] = $_POST['low_stock_alert'] ?? null;

            // Remove selected existing images for this product.
            if (isset($_POST['removed_images']) && is_array($_POST['removed_images'])) {
                foreach ($_POST['removed_images'] as $imageId) {
                    if (!empty($imageId)) {
                        $productModel->deleteProductImageForProduct($this->product_id, $imageId);
                    }
                }
            }

            // Handle single product image uploads
            if (isset($_FILES['product_images'])) {
                $uploadedImages = $this->uploadProductImages($_FILES['product_images']);
                foreach ($uploadedImages as $imagePath) {
                    $productModel->addProductImage($this->product_id, $imagePath);
                }
            }
        }

        // Validate required fields
        if (empty($updateData['name'])) {
            header('Location: ' . ROOT . 'dashboard/products/all?error=missing_name');
            exit;
        }

        // Update the product
        $result = $productModel->updateProduct($this->product_id, $updateData);

        if ($result) {
            header('Location: ' . ROOT . 'dashboard/products/all?success=product_updated&product_name=' . urlencode($updateData['name']));
        } else {
            header('Location: ' . ROOT . 'dashboard/products/all?error=update_failed');
        }
        exit;
    }

    private function uploadProductImages($files)
    {
        $uploadedPaths = [];
        $uploadDir = '../public/assets/img/products/';

        // Check if upload directory exists, create if not
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Handle multiple file upload
        if (is_array($files['tmp_name'])) {
            foreach ($files['tmp_name'] as $key => $tmp_name) {
                if ($files['error'][$key] === UPLOAD_ERR_OK && is_uploaded_file($tmp_name)) {
                    $originalName = $files['name'][$key];
                    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                    if (!$this->isAllowedImageExtension($fileExtension)) {
                        continue;
                    }

                    $fileName = uniqid() . '_' . basename($originalName);
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmp_name, $targetPath)) {
                        $uploadedPaths[] = 'assets/img/products/' . $fileName;
                    }
                }
            }
        }

        return $uploadedPaths;
    }

    private function uploadVariantImage($variant_id, $files, $index)
    {
        $uploadDir = '../public/assets/img/products/';

        // Check if upload directory exists, create if not
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($files['error'][$index] === UPLOAD_ERR_OK && is_uploaded_file($files['tmp_name'][$index])) {
            $originalName = $files['name'][$index];
            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!$this->isAllowedImageExtension($fileExtension)) {
                return false;
            }

            $fileName = uniqid() . '_' . basename($originalName);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($files['tmp_name'][$index], $targetPath)) {
                return 'assets/img/products/' . $fileName;
            }
        }

        return false;
    }

    private function uploadSingleImage($file)
    {
        $uploadDir = '../public/assets/img/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (
            isset($file['error'], $file['tmp_name'], $file['name'])
            && $file['error'] === UPLOAD_ERR_OK
            && is_uploaded_file($file['tmp_name'])
        ) {
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!$this->isAllowedImageExtension($fileExtension)) {
                return false;
            }

            $fileName = uniqid() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return 'assets/img/products/' . $fileName;
            }
        }

        return false;
    }

    private function isAllowedImageExtension($extension)
    {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array($extension, $allowedTypes, true);
    }
}
