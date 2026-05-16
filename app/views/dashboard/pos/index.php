<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Point of Sale</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/pos/index.css">
  <script>
    window.ROOT = '<?= ROOT ?>';
  </script>

</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>

    <main class="content">
            <div class="pos-container">
                <!-- Left Section: Products -->
                <div class="pos-products-section">
                    <div class="pos-header">
                        <div>
                            <h2 class="font-semibold">Point of Sale</h2>
                            <p class="text-muted">Handle walk-in orders</p>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <?php
                        $categoryList = [];
                        if (!empty($products)) {
                            foreach ($products as $p) {
                                $cat = trim((string)($p['category_name'] ?? $p['category'] ?? ''));
                                if ($cat !== '') {
                                    $categoryList[$cat] = $cat;
                                }
                            }
                        }
                        ksort($categoryList);
                    ?>
                    <div class="pos-filters mx-2">
                        <div class="pos-search-wrapper">
                            <input type="text" id="product-search" class="input" placeholder="Search products by name">
                        </div>
                        <select id="category-filter" class="input">
                            <option value="all">All Categories</option>
                            <?php foreach ($categoryList as $cat): ?>
                                <option value="<?= htmlspecialchars(strtolower($cat)) ?>"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Product Grid -->
                    <div class="pos-product-grid" id="product-grid">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <div class="pos-product-card" data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>" data-category="<?= htmlspecialchars(strtolower($product['category_name'] ?? $product['category'] ?? '')) ?>">
                                    <?php if (!empty($product['images'][0])): ?>
                                        <img src="<?= ROOT . htmlspecialchars($product['images'][0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <?php else: ?>
                                        <div class="no-product-image"></div>
                                    <?php endif; ?>
                                    <?php if ($product['is_variant'] == 1): ?>
                                        <span class="variant-badge">Variants</span>
                                    <?php endif; ?>

                                    <h4><?= htmlspecialchars($product['name']) ?></h4>
                                    <div class="price">$<?= number_format($product['price'], 2) ?></div>
                                   <?php if ($product['stock_quantity'] > 0): ?>
                                            <div class="stock">Available: <?= (int)$product['stock_quantity'] ?></div>

                                            <?php if ($product['is_variant'] == 1): ?>
                                                <button 
                                                    class="add-to-cart-btn variant-btn"
                                                    data-id="<?= htmlspecialchars($product['id'], ENT_QUOTES) ?>"
                                                    data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                                                    data-is-variant="1"
                                                    data-variants="<?= htmlspecialchars(json_encode($product['variants']), ENT_QUOTES) ?>"
                                                    data-attributes="<?= htmlspecialchars(json_encode($product['attributes']), ENT_QUOTES) ?>"
                                                >
                                                    Select Variant
                                                </button>
                                            <?php else: ?>
                                                <button 
                                                    class="add-to-cart-btn"
                                                    data-id="<?= htmlspecialchars($product['id'], ENT_QUOTES) ?>"
                                                    data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                                                    data-price="<?= (float)$product['price'] ?>"
                                                    data-stock="<?= (int)$product['stock_quantity'] ?>"
                                                    data-is-variant="0"
                                                >
                                                    Add to Cart
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button class="add-to-cart-btn" disabled>Out of Stock</button>
                                        <?php endif; ?>


                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No products available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Section: Cart -->
                <div class="pos-cart-section">
                    <div class="pos-cart-header">
                        <h3 class="font-semibold">Current Order</h3>
                        <button class="btn btn-sm btn-outline" id="clear-cart">Clear</button>
                    </div>

                    <div class="pos-cart-items" id="cart-items">
                        <div class="pos-cart-empty">
                            <div class="pos-cart-empty-icon">🛒</div>
                            <p>No items in cart</p>
                            <span class="text-muted">Add products to get started</span>
                        </div>
                    </div>

                    <div class="pos-cart-summary">
                        <div class="pos-summary-row">
                            <span>Subtotal</span>
                            <span id="cart-subtotal">$0.00</span>
                        </div>
                        <div class="pos-summary-row pos-summary-total">
                            <span>Total</span>
                            <span id="cart-total">$0.00</span>
                        </div>

                            <form method="post" action="<?=ROOT?>dashboard/pos/poscheckout">
                                <button type="submit" class="btn btn-primary" id="checkout-btn">
                                    Checkout
                                </button>
                            </form>
                          </div>
                </div>
            </div>

            <!-- Variant Selection Modal -->
            <div id="variant-modal" class="pos-modal" style="display: none;">
                <div class="pos-modal-content">
                    <div class="pos-modal-header">
                        <h3 id="variant-modal-title">Select Variant</h3>
                        <button class="pos-modal-close" id="variant-modal-close">&times;</button>
                    </div>
                    <div class="pos-modal-body" id="variant-modal-body">
                        <!-- Variant options will be inserted here -->
                    </div>
                    <div class="pos-modal-footer">
                        <button class="btn btn-outline" id="variant-modal-cancel">Cancel</button>
                        <button class="btn btn-primary" id="variant-modal-confirm" disabled>Add to Cart</button>
                    </div>
                </div>
            </div>
        </main>


  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/pages/pos/filters.js"></script>
  <script src="<?= ROOT ?>assets/js/pages/pos/variants.js"></script>
  <script src="<?= ROOT ?>assets/js/pages/pos/cart.js"></script>

</body>

</html>








