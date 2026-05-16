<?php
class Media extends Controller
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
            $this->uploadMedia();
            return;
        }

        // Get product details to display
        $productModel = $this->model("ProductModel");
        $product = $productModel->getProductById($this->product_id);

        if (!$product) {
            header('Location: ' . ROOT . 'dashboard/products');
            exit;
        }

        // Get existing product images
        $productImages = $productModel->getProductImages($this->product_id);

        // Get product variants (for variant products)
        $variants = [];
        if ($product['is_variant']) {
            $variants = $productModel->getProductVariantsWithAttributes($this->product_id);
        }

        // Pass product data to view
        $data = [
            'product' => $product,
            'product_id' => $this->product_id,
            'product_images' => $productImages,
            'variants' => $variants
        ];

        echo $this->view('dashboard/products/media', $data);
    }

    private function uploadMedia()
    {
        $productModel = $this->model("ProductModel");

        // Check if user clicked "Skip for Now"
        if (isset($_POST['skip']) && $_POST['skip'] == '1') {
            header('Location: ' . ROOT . 'dashboard/products/Newproduct/' . $this->product_id . '/Complete');
            exit;
        }

        $uploadDir = '../public/assets/img/products/';

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadedFiles = [];
        $variantImagesUploaded = false;
        $errors = [];

        // Handle variant-specific images
        if (isset($_FILES['variant_images']) && is_array($_FILES['variant_images']['name'])) {
            foreach ($_FILES['variant_images']['name'] as $variantId => $originalName) {
                // Skip if no file was selected for this variant
                if (empty($originalName) || $_FILES['variant_images']['error'][$variantId] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $tmpName = $_FILES['variant_images']['tmp_name'][$variantId];
                $fileSize = $_FILES['variant_images']['size'][$variantId];

                // Validate file size (max 5MB)
                if ($fileSize > 5 * 1024 * 1024) {
                    $errors[] = "File too large for variant $variantId";
                    continue;
                }

                // Generate unique filename
                $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
                $filename = 'variant_' . $variantId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
                $filepath = $uploadDir . $filename;

                // Validate file type
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                if (!in_array(strtolower($fileExtension), $allowedTypes)) {
                    $errors[] = "Invalid file type for variant $variantId";
                    continue;
                }

                if (move_uploaded_file($tmpName, $filepath)) {
                    // Store relative path for database
                    $imageUrl = 'assets/img/products/' . $filename;

                    // Update variant image in database
                    $result = $productModel->updateVariantImage($variantId, $imageUrl);

                    if ($result) {
                        $uploadedFiles[] = $filename;
                        $variantImagesUploaded = true;
                        error_log("Successfully uploaded variant image for variant $variantId: $imageUrl");
                    } else {
                        $errors[] = "Database update failed for variant $variantId";
                        // Delete uploaded file if DB update fails
                        @unlink($filepath);
                    }
                } else {
                    $errors[] = "Failed to move uploaded file for variant $variantId";
                }
            }
        }

        // Handle general product images (if not variant product)
        if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
            $totalFiles = count($_FILES['product_images']['name']);

            for ($i = 0; $i < $totalFiles; $i++) {
                if ($_FILES['product_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['product_images']['tmp_name'][$i];
                    $originalName = $_FILES['product_images']['name'][$i];
                    $fileSize = $_FILES['product_images']['size'][$i];

                    // Validate file size (max 5MB)
                    if ($fileSize > 5 * 1024 * 1024) {
                        $errors[] = "File too large: $originalName";
                        continue;
                    }

                    // Generate unique filename
                    $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
                    $filename = $this->product_id . '_' . time() . '_' . $i . '_' . uniqid() . '.' . $fileExtension;
                    $filepath = $uploadDir . $filename;

                    // Validate file type
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                    if (!in_array(strtolower($fileExtension), $allowedTypes)) {
                        $errors[] = "Invalid file type: $originalName";
                        continue;
                    }

                    if (move_uploaded_file($tmpName, $filepath)) {
                        // Store relative path for database
                        $imageUrl = 'assets/img/products/' . $filename;

                        // Add to database
                        $result = $productModel->addProductImage($this->product_id, $imageUrl);

                        if ($result) {
                            $uploadedFiles[] = $filename;
                            error_log("Successfully uploaded product image: $imageUrl");
                        } else {
                            $errors[] = "Database insert failed for: $originalName";
                            @unlink($filepath);
                        }
                    } else {
                        $errors[] = "Failed to move uploaded file: $originalName";
                    }
                }
            }
        }

        // Log any errors
        if (!empty($errors)) {
            error_log("Upload errors: " . implode(", ", $errors));
        }

        // Log success
        if (!empty($uploadedFiles)) {
            error_log("Successfully uploaded " . count($uploadedFiles) . " images");
        }

        // Redirect to complete page
        header('Location: ' . ROOT . 'dashboard/products/Newproduct/' . $this->product_id . '/Complete');
        exit;
    }
}
