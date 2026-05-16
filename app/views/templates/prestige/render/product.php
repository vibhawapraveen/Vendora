<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "../public/assets/components/templates/prestige/head.php" ?>
</head>

<body data-storecode="<?= $storecode ?>">
    <!-- NAVBAR -->
    <?php require_once "../public/assets/components/templates/prestige/navbar.php" ?>
    <!-- 
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/" class="navbar-logo">
                <i class="fas fa-bolt" style="color: var(--primary); font-size: 1.8rem;"></i>
                TechHub
            </a>
            <div class="navbar-icons">
                <button class="navbar-icon-btn" id="cartBtn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
                </button>
                <button class="navbar-icon-btn">
                    <i class="fas fa-user"></i>
                </button>
            </div>
        </div>
    </nav> -->

    <!-- PRODUCT DETAIL CONTAINER -->
    <div class="product-detail-container">
        <div class="product-detail-content">
            <!-- LEFT SIDE - IMAGES -->
            <div class="product-detail-left">
                <div class="product-main-image">
                    <img id="mainProductImage" src="<?= $product['is_variant'] ? $product['variants'][0]['image'] : $product['images'][0] ?>" alt="<?= $product['name'] ?>">
                </div>
                <div class="product-thumbnail-carousel">
                    <?php if ($product['is_variant']): ?>
                        <!-- Will be populated by JavaScript for variants -->
                        <div id="thumbnailContainer"></div>
                    <?php else: ?>
                        <?php foreach ($product['images'] as $index => $image): ?>
                            <img src="<?= $image ?>" alt="Thumbnail" class="product-thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage('<?= $image ?>', this)">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT SIDE - PRODUCT INFO -->
            <div class="product-detail-right">
                <h1 class="product-detail-name"><?= $product['name'] ?></h1>
                <p class="product-detail-description"><?= $product['description'] ?></p>

                <?php if ($product['is_variant']): ?>
                    <!-- MULTI-VARIANT PRODUCT -->
                    <div class="product-variants">
                        <?php foreach ($product['attributes'] as $attribute): ?>
                            <div class="variant-group">
                                <label class="variant-label"><?= $attribute['name'] ?>: <span id="selected-<?= strtolower($attribute['name']) ?>" class="selected-variant-value"></span></label>
                                <div class="variant-options">
                                    <?php foreach ($attribute['values'] as $value): ?>
                                        <button class="variant-option"
                                            data-attribute-id="<?= $attribute['id'] ?>"
                                            data-value-id="<?= $value['id'] ?>"
                                            data-value-name="<?= $value['value'] ?>"
                                            onclick="selectVariantOption('<?= $attribute['id'] ?>', '<?= $value['id'] ?>', '<?= $value['value'] ?>')">
                                            <?= $value['value'] ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="product-detail-price-section">
                        <div class="product-detail-price" id="productPrice">$0.00</div>
                        <div class="product-stock" id="productStock">
                            <i class="fas fa-box"></i> <span id="stockQuantity">0</span> in stock
                        </div>
                        <div class="selected-variant-info" id="selectedVariantInfo"></div>
                    </div>

                    <div class="product-add-section">
                        <div class="quantity-selector">
                            <button onclick="decrementQuantity()">−</button>
                            <input type="number" id="productQuantity" value="1" min="1" max="999">
                            <button onclick="incrementQuantity()">+</button>
                        </div>
                        <button class="add-to-cart-btn-large" id="addToCartBtn" onclick="addProductToCart()">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>

                <?php else: ?>
                    <!-- SINGLE VARIANT PRODUCT -->
                    <div class="product-detail-price-section">
                        <div class="product-detail-price">$<?= number_format($product['price'], 2) ?></div>
                        <div class="product-stock">
                            <i class="fas fa-box"></i> <?= $product['stock'] ?> in stock
                        </div>
                    </div>

                    <div class="product-add-section">
                        <div class="quantity-selector">
                            <button onclick="decrementQuantity()">−</button>
                            <input type="number" id="productQuantity" value="1" min="1" max="<?= $product['stock'] ?>">
                            <button onclick="incrementQuantity()">+</button>
                        </div>
                        <button class="add-to-cart-btn-large" onclick="addSingleProductToCart()">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require_once "../public/assets/components/templates/prestige/footer.php" ?>


    <script src="<?= ROOT ?>assets/js/pages/templates/beam/index.js"></script>
    <script src="<?= ROOT ?>assets/js/pages/templates/prestige/product.js"></script>

    <script>
        const stylesFromDB = {
            primary: "<?= $content['primary_color'] ?>",
        };

        const root = document.documentElement; // <html> element
        root.style.setProperty('--primary', stylesFromDB.primary);
    </script>
    <script>
        // Initialize product page with data from PHP
        document.addEventListener('DOMContentLoaded', function() {
            const productData = <?= json_encode($product) ?>;
            initProductPage(productData);
        });
    </script>

</body>

</html>