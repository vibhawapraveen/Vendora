<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Media</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/media.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>
        <main class="content">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed">
                    <i class="fa-solid fa-check"></i>
                    <span>Attributes</span>
                </div>
                <div class="step completed">
                    <i class="fa-solid fa-check"></i>
                    <span>Variants</span>
                </div>
                <div class="step active">
                    <i class="fa-solid fa-image"></i>
                    <span>Media Upload</span>
                </div>
                <div class="step pending">
                    <i class="fa-solid fa-flag-checkered"></i>
                    <span>Complete</span>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="card">
                <div class="card-header mb-4">
                    <div class="card-subtitle">Product Images</div>
                    <p class="text-muted text-sm mt-1">
                        <?php if (!empty($data['variants'])): ?>
                            Add images for each product variant. Each variant can have its own unique image.
                        <?php else: ?>
                            Add images for your product. These are local previews only.
                        <?php endif; ?>
                    </p>
                </div>

                <form method="POST" action="" enctype="multipart/form-data">

                    <?php if (!empty($data['variants'])): ?>
                        <!-- Main Product Images Section -->
                        <div class="variant-images-section mb-6">
                            <h4 class="font-medium mb-4">Main Product Images</h4>
                            <p class="text-muted text-sm mb-4">Upload general product images that will be shown in the product gallery.</p>

                            <div class="upload-area" id="uploadArea">
                                <div class="upload-icon">
                                    <i class="fa-solid fa-cloud-upload-alt"></i>
                                </div>
                                <h3 class="text-lg font-semibold mb-2">Drop images here or click to browse</h3>
                                <p class="text-muted">Support for JPG, PNG, GIF, WEBP up to 5MB</p>
                                <input type="file" name="product_images[]" multiple accept="image/*" id="imageInput">
                            </div>

                            <!-- Selected Files Preview -->
                            <div class="selected-files" id="selectedFiles" style="display: none;">
                                <h4 class="font-medium mb-3">Selected Images</h4>
                                <div class="preview-grid" id="previewGrid"></div>
                            </div>

                            <!-- Existing Product Images -->
                            <?php if (!empty($data['product_images'])): ?>
                                <div class="mt-4">
                                    <h4 class="font-medium mb-3">Existing Product Images</h4>
                                    <div class="preview-grid">
                                        <?php foreach ($data['product_images'] as $image): ?>
                                            <div class="preview-item">
                                                <img src="<?= ROOT . $image['image_url'] ?>" alt="Product Image">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Variant-Specific Images Section -->
                        <div class="variant-images-section mb-6">
                            <h4 class="font-medium mb-4">Variant-Specific Images</h4>
                            <p class="text-muted text-sm mb-4">Upload unique images for each product variant. </p>

                            <div class="variants-grid">
                                <?php foreach ($data['variants'] as $variant): ?>
                                    <div class="variant-card">
                                        <div class="variant-info mb-3">
                                            <?php if (!empty($variant['variant_name'])): ?>
                                                <div class="variant-name mb-2">
                                                    <strong><?= htmlspecialchars($variant['variant_name']) ?></strong>
                                                </div>
                                            <?php endif; ?>
                                            <div class="variant-details">
                                                <strong>SKU:</strong> <?= htmlspecialchars($variant['sku']) ?><br>
                                                <span class="text-sm text-muted">Price: $<?= number_format($variant['price'], 2) ?> | Stock: <?= $variant['stock_quantity'] ?></span>
                                            </div>
                                        </div>

                                        <div class="variant-upload-area">
                                            <?php if (!empty($variant['image'])): ?>
                                                <div class="current-image mb-2">
                                                    <img src="<?= ROOT . $variant['image'] ?>" alt="Variant Image" style="max-width: 100%; border-radius: 8px;">
                                                </div>
                                            <?php endif; ?>

                                            <label class="variant-upload-label">
                                                <i class="fa-solid fa-image"></i>
                                                <span><?= !empty($variant['image']) ? 'Change Image' : 'Upload Image' ?></span>
                                                <input type="file" name="variant_images[<?= $variant['id'] ?>]" accept="image/*" class="variant-image-input">
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- General Product Upload Area -->
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-icon">
                                <i class="fa-solid fa-cloud-upload-alt"></i>
                            </div>
                            <h3 class="text-lg font-semibold mb-2">Drop images here or click to browse</h3>
                            <p class="text-muted">Support for JPG, PNG up to a few MB</p>
                            <input type="file" name="product_images[]" multiple accept="image/*" id="imageInput">
                        </div>

                        <!-- Selected Files Preview -->
                        <div class="selected-files" id="selectedFiles" style="display: none;">
                            <h4 class="font-medium mb-3">Selected Images</h4>
                            <div class="preview-grid" id="previewGrid"></div>
                        </div>

                        <!-- Existing Images -->
                        <?php if (!empty($data['product_images'])): ?>
                            <div class="mt-6">
                                <h4 class="font-medium mb-3">Existing Images</h4>
                                <div class="preview-grid">
                                    <?php foreach ($data['product_images'] as $image): ?>
                                        <div class="preview-item">
                                            <img src="<?= ROOT . $image['image_url'] ?>" alt="Product Image">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>


                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center mt-8">
                        <button type="submit" name="skip" value="1" class="btn btn-secondary">
                            Skip for Now
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Upload & Continue
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/products/media.js"></script>
</body>

</html>