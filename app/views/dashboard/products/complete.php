<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Complete</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/new.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/media.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/complete.css">
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
                <?php if ($data['product']['is_variant'] == 1): ?>
                    <!-- Multi-variant flow -->
                    <div class="step completed">
                        <i class="fa-solid fa-check"></i>
                        <span>Attributes</span>
                    </div>
                    <div class="step completed">
                        <i class="fa-solid fa-check"></i>
                        <span>Variants</span>
                    </div>
                    <div class="step completed">
                        <i class="fa-solid fa-check"></i>
                        <span>Media Upload</span>
                    </div>
                    <div class="step active">
                        <i class="fa-solid fa-flag-checkered"></i>
                        <span>Complete</span>
                    </div>
                <?php else: ?>
                    <!-- Single variant flow -->
                    <div class="step completed">
                        <i class="fa-solid fa-check"></i>
                        <span>Product Info</span>
                    </div>
                    <div class="step completed">
                        <i class="fa-solid fa-check"></i>
                        <span>Pricing & Inventory</span>
                    </div>
                    <div class="step completed">
                        <i class="fa-solid fa-check"></i>
                        <span>Media Upload</span>
                    </div>
                    <div class="step active">
                        <i class="fa-solid fa-flag-checkered"></i>
                        <span>Complete</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card gap-3">
                <div class="success-content">
                    <!-- Success Icon -->
                    <div class="success-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <!-- Success Message -->
                    <h1 class="success-title">Product Created Successfully!</h1>
                    <p class="success-description">
                        Your product has been created and is ready for customers.
                        You can manage it from your products dashboard or continue adding more products.
                    </p>

                    <!-- Product Summary -->
                    <div class="product-summary">
                        <div class="summary-item">
                            <span class="summary-label">Product Name:</span>
                            <span class="summary-value"><?= htmlspecialchars($data['product']['name'] ?? 'Unknown') ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Product ID:</span>
                            <span class="summary-value"><?= htmlspecialchars($data['product_id']) ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Type:</span>
                            <span class="summary-value">
                                <?= $data['product']['is_variant'] == 1 ? 'Multi-Variant Product' : 'Single Product' ?>
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Created:</span>
                            <span class="summary-value"><?= date('M j, Y g:i A') ?></span>
                        </div>
                    </div>

                    <!-- Visibility Setting -->
                    <div class="product-summary">
                        <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Product Visibility</h3>
                        <p style="color: #6b7280; margin-bottom: 1rem; font-size: 0.875rem;">
                            Choose whether this product should be visible to customers or kept as a draft.
                        </p>

                        <form id="visibilityForm"
                            method="POST"
                            action="<?= ROOT ?>dashboard/products/newproduct/<?= $data['product_id'] ?>/complete"
                            data-root="<?= ROOT ?>"
                            data-product-id="<?= $data['product_id'] ?>">
                            <div class="flex gap-4 mb-4">
                                <label class="flex items-center">
                                    <input type="radio" name="visibility" value="1" <?= $data['product']['visibility'] == 1 ? 'checked' : '' ?> class="mr-2">
                                    <span class="font-medium text-green-600">Active</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="visibility" value="0" <?= $data['product']['visibility'] == 0 ? 'checked' : '' ?> class="mr-2">
                                    <span class="font-medium text-gray-600">Draft</span>
                                </label>
                            </div>
                        </form>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="<?= ROOT ?>dashboard/products" class="btn btn-primary">
                            <i class="fa-solid fa-check"></i>
                            <span>Complete</span>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/products/complete.js"></script>

</body>

</html>