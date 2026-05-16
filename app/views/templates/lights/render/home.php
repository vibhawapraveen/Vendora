<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyStore - Home</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/templates/lights.css">
</head>

<body data-storecode="<?= $storecode ?>">
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo"><?= $content['navbar_text'] ?></div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <?php require "../app/views/templates/user.php"; ?>
            <a href="<?= ROOT . $storecode ?>/cart" class="cart-link">
                🛒 Your Cart
                <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1><?= $content['heading'] ?></h1>
        <p><?= $content['subheading'] ?></p>
        <a href="#products" class="cta-button">Browse Products</a>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="products">
        <h2>Our Products</h2>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if (!empty($product['images'])): ?>
                        <img src="<?= htmlspecialchars($product['images'][0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    <?php endif; ?>
                    <div>
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        <div class="product-price">$ <?= number_format($product['price'], 2) ?></div>
                        <div class="product-stock">Stock: <?= $product['stock_quantity'] ?> available</div>
                    </div>
                    <div class="product-actions">
                        <div class="quantity-selector">
                            <button class="qty-btn" onclick="decreaseQuantity('<?= $product['id'] ?>')">-</button>
                            <input type="number" id="qty-<?= $product['id'] ?>" value="1" min="1" max="<?= $product['stock_quantity'] ?>" readonly>
                            <button class="qty-btn" onclick="increaseQuantity('<?= $product['id'] ?>', <?= $product['stock_quantity'] ?>)">+</button>
                        </div>
                        <button
                            class="add-to-cart-btn"
                            onclick="showToast('Item added to cart!'); addToCart('<?= $product['id'] ?>','<?= $product['name'] ?>','<?= $product['price'] ?>')"
                            <?= $product['stock_quantity'] == 0 ? 'disabled' : '' ?>>
                            <?= $product['stock_quantity'] == 0 ? 'Out of Stock' : 'Add to Cart' ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <script>
        const ROOT = '<?= ROOT ?>';
    </script>
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <script src="<?= ROOT ?>assets/js/pages/templates/base/index.js"></script>
    <script src="<?= ROOT ?>assets/js/pages/templates/lights.js"></script>

    <script>
        const stylesFromDB = {
            primary: "<?= $content['primary_color'] ?>",
        };

        const root = document.documentElement; // <html> element
        root.style.setProperty('--primary', stylesFromDB.primary);
    </script>
</body>

</html>