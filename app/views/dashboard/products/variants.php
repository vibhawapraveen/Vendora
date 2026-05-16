<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Variants - Vendora</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/new.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/media.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/variants.css">
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
                <div class="step active">
                    <i class="fa-solid fa-list"></i>
                    <span>Variants</span>
                </div>
                <div class="step pending">
                    <i class="fa-solid fa-image"></i>
                    <span>Media Upload</span>
                </div>
                <div class="step pending">
                    <i class="fa-solid fa-flag-checkered"></i>
                    <span>Complete</span>
                </div>
            </div>

            <!-- Title Section -->
            <div class="flex items-center mb-5">
                <h1 class="page-title">Manage variants</h1>
            </div>

            <div class="card gap-3">
                <!-- Variants Header -->
                <div class="variants-header">
                    <div>
                        <h2 class="section-title mb-2">Variants</h2>
                        <p class="section-description">
                            Variants are generated from attribute combinations. Edit SKU, price, and<br>
                            stock.
                        </p>
                    </div>
                    <div class="header-controls">
                        <div class="flex items-center gap-3">
                            <span class="control-label">Auto SKU</span>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <button class="btn btn-outline" onclick="regenerateVariants()">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            <span>Regenerate</span>
                        </button>
                    </div>
                </div>

                <div class="variants-count mb-4">
                    <?= count($data['variants']) ?> variants
                </div>

                <form id="variantsForm"
                    method="POST"
                    action="<?= ROOT ?>dashboard/products/newproduct/<?= $data['product_id'] ?>/variants"
                    data-root="<?= ROOT ?>"
                    data-product-id="<?= $data['product_id'] ?>">
                    <!-- Bulk Actions -->
                    <div class="bulk-actions">
                        <div class="bulk-input-group">
                            <label class="control-label">Bulk set price</label>
                            <div class="flex items-center gap-2">
                                <span>$</span>
                                <input type="number" class="input bulk-input" placeholder="0.00" step="0.01" id="bulkPrice">
                                <button type="button" class="btn btn-outline" onclick="applyBulkPrice()">Apply</button>
                            </div>
                        </div>

                        <div class="bulk-input-group">
                            <label class="control-label">Bulk set stock</label>
                            <div class="flex items-center gap-2">
                                <input type="number" class="input bulk-input" placeholder="0" id="bulkStock">
                                <button type="button" class="btn btn-outline" onclick="applyBulkStock()">Apply</button>
                            </div>
                        </div>

                        <div class="bulk-input-group">
                            <label class="control-label">Bulk set stock alert</label>
                            <div class="flex items-center gap-2">
                                <input type="number" class="input bulk-input" placeholder="0" id="bulkStockAlert">
                                <button type="button" class="btn btn-outline" onclick="applyBulkStockAlert()">Apply</button>
                            </div>
                        </div>
                    </div>

                    <!-- Variants Table -->
                    <table class="variants-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Enabled</th>
                                <th>Options</th>
                                <th style="width: 140px;">SKU</th>
                                <th style="width: 100px;">Price</th>
                                <th style="width: 100px;">Stock</th>
                                <th style="width: 100px;">Stock alert</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['variants'] as $index => $variant): ?>
                                <tr class="variant-row" data-index="<?= $index ?>">
                                    <td>
                                        <label class="toggle-switch variant-toggle">
                                            <input type="checkbox" <?= $variant['enabled'] ? 'checked' : '' ?> class="variant-enabled" data-index="<?= $index ?>">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($variant['options']) ?></strong>
                                    </td>
                                    <td>
                                        <input type="text" class="variant-input sku-input"
                                            data-index="<?= $index ?>"
                                            value="<?= htmlspecialchars($variant['sku']) ?>"
                                            placeholder="SKU">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <span>$</span>
                                            <input type="number" class="variant-input price-input"
                                                data-index="<?= $index ?>"
                                                value="<?= $variant['price'] ?>"
                                                step="0.01" placeholder="0.00" min="0">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="variant-input stock-input"
                                            data-index="<?= $index ?>"
                                            value="<?= $variant['stock'] ?>"
                                            placeholder="0" min="0">
                                    </td>

                                    <td>
                                        <input type="number" class="variant-input stock_alert-input"
                                            data-index="<?= $index ?>"
                                            value="<?= $variant['low_stock_alert'] ?? '' ?>"
                                            placeholder="0" min="0">
                                    </td>
                                    <input type="hidden" class="attribute-value-ids" data-index="<?= $index ?>" value='<?= json_encode($variant['attribute_value_ids']) ?>'>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="<?= ROOT ?>dashboard/products/newproduct/<?= $data['product_id'] ?>/attributes" class="btn btn-outline">
                            <i class="fa-solid fa-chevron-left"></i>
                            <span>Back</span>
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <span>Next</span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/products/variants.js?v=<?= time() ?>"></script>
</body>

</html>