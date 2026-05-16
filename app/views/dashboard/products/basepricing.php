<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Pricing</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/basepricing.css">
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
                    <span>Product Info</span>
                </div>
                <div class="step active">
                    <i class="fa-solid fa-dollar-sign"></i>
                    <span>Pricing & Inventory</span>
                </div>
                <div class="step pending">
                    <i class="fa-solid fa-image"></i>
                    <span>Media</span>
                </div>
                <div class="step pending">
                    <i class="fa-solid fa-flag-checkered"></i>
                    <span>Complete</span>
                </div>
            </div>



            <!-- Pricing Form -->
            <div class="card">
                <div class="card-header mb-4">
                    <div class="card-subtitle">Product Pricing & Stock</div>
                    <p class="text-muted text-sm mt-1">Set the selling price and available quantity</p>
                </div>

                <form method="POST" action="">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Price Section -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Product Price
                                </label>
                                <div class="price-input-group">
                                    <span class="currency-symbol">$</span>
                                    <input
                                        type="number"
                                        name="price"
                                        class="input"
                                        placeholder="0.00"
                                        step="0.01"
                                        min="0"
                                        required />
                                </div>
                            </div>
                        </div>

                        <!-- Stock Section -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">

                                    Stock Quantity
                                </label>
                                <input
                                    type="number"
                                    name="stock_quantity"
                                    class="input"
                                    placeholder="0"
                                    min="0"
                                    required />
                            </div>
                        </div>

                        <!-- Low stock aler number -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Low Stock Threshold
                                </label>
                                <input
                                    type="number"
                                    name="low_stock_alert"
                                    class="input"
                                    placeholder="0"
                                    min="0"
                                    required />
                            </div>
                        </div>
                    </div>

                    <!-- Product Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mt-6">
                        <h3 class="font-medium mb-3">Product Summary</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-muted">Product Name:</span>
                                <p class="font-medium"><?= htmlspecialchars($data['product']['name']) ?></p>
                            </div>
                            <div>
                                <span class="text-muted">Variant Type:</span>
                                <p class="font-medium">
                                    <?= $data['product']['is_variant'] ? 'Multi Variant' : 'Single Variant' ?>
                                </p>
                            </div>
                            <div>
                                <span class="text-muted">Status:</span>
                                <p class="font-medium">
                                    <?= $data['product']['visibility'] ? 'Active' : 'Inactive' ?>
                                </p>
                            </div>
                            <div>
                                <span class="text-muted">Created:</span>
                                <p class="font-medium">
                                    <?= date('M d, Y', strtotime($data['product']['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class=" mb-2">
                        <button type="submit" class="btn btn-primary mt-5 mr-3">
                            <p class="px-2">Next</p>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>