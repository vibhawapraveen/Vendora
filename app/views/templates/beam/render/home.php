<?php
// pre($home_contents);
// pre($products);
// Mock product data
// $products = [
//     [
//         'id' => 1,
//         'name' => 'Pro Wireless Headphones',
//         'price' => 199.99,
//         'images' => ['https://via.placeholder.com/400x300/0066cc/ffffff?text=Headphones+1', 'https://via.placeholder.com/400x300/0066cc/ffffff?text=Headphones+2'],
//     ],
//     [
//         'id' => 2,
//         'name' => 'Smart Watch Ultra',
//         'price' => 349.99,
//         'images' => ['https://via.placeholder.com/400x300/0066cc/ffffff?text=SmartWatch+1', 'https://via.placeholder.com/400x300/0066cc/ffffff?text=SmartWatch+2'],
//     ],
//     [
//         'id' => 3,
//         'name' => '4K Webcam',
//         'price' => 129.99,
//         'images' => ['https://via.placeholder.com/400x300/0066cc/ffffff?text=Webcam+1', 'https://via.placeholder.com/400x300/0066cc/ffffff?text=Webcam+2'],
//     ],
//     [
//         'id' => 4,
//         'name' => 'Portable SSD 1TB',
//         'price' => 89.99,
//         'images' => ['https://via.placeholder.com/400x300/0066cc/ffffff?text=SSD+1', 'https://via.placeholder.com/400x300/0066cc/ffffff?text=SSD+2'],
//     ],
//     [
//         'id' => 5,
//         'name' => 'USB-C Hub Pro',
//         'price' => 59.99,
//         'images' => ['https://via.placeholder.com/400x300/0066cc/ffffff?text=Hub+1', 'https://via.placeholder.com/400x300/0066cc/ffffff?text=Hub+2'],
//     ],
//     [
//         'id' => 6,
//         'name' => 'Mechanical Keyboard RGB',
//         'price' => 149.99,
//         'images' => ['https://via.placeholder.com/400x300/0066cc/ffffff?text=Keyboard+1', 'https://via.placeholder.com/400x300/0066cc/ffffff?text=Keyboard+2'],
//     ],
// ];

// $storecode = 'BEAM-001';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $content['title'] ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/templates/beam.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body data-storecode="<?= $storecode ?>">
    <!-- NAVBAR -->
     <?php require_once "../public/assets/components/templates/beam/navbar.php" ?>
    <!-- <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="navbar-logo">
                <i class="fas fa-bolt" style="color: var(--primary-blue); font-size: 1.8rem;"></i>
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

    <!-- HERO SECTION -->
    <section class="hero" style="background-image: url('<?= $content['hero_img'] ?>')">
        <div class="hero-content">
            <h1 class="hero-heading"><?= $content['heading'] ?></h1>
            <p class="hero-paragraph"><?= $content['subheading'] ?></p>
            <a href="<?= ROOT.$storecode ?>/products" class="cta-button"><?= $content['cta_text'] ?></a>
        </div>
    </section>

    <!-- FEATURED PRODUCTS SECTION -->
    <section class="featured-products" id="featured-products">
        <div class="featured-products-container">
            <h2 class="section-title"><?= $content['featured_products_title'] ?></h2>
            <div class="products-grid">
                <?php foreach ($home_contents as $product): ?>
                    <div class="product-card" data-product-id="<?= $product['id'] ?>">
                        <div class="product-image-carousel">
                            <?php foreach ($product['images'] as $index => $image): ?>
                                <img src="<?= $image ?>" alt="<?= $product['name'] ?>" class="carousel-image" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                            <?php endforeach; ?>
                            <div class="carousel-nav">
                                <?php foreach ($product['images'] as $index => $image): ?>
                                    <button class="carousel-dot <?= $index === 0 ? 'active' : '' ?>" data-image-index="<?= $index ?>" onclick="changeImage(this)"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="product-card-content">
                            <h3 class="product-name"><?= $product['name'] ?></h3>
                            <p class="product-price">USD <?= number_format($product['base_price'], 2) ?></p>
                            <div class="product-card-actions">
                                <!-- <button class="add-to-cart-btn" onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes($product['name']) ?>', <?= $product['price'] ?>, 1)">
                                <i class="fas fa-shopping-cart"></i> Add
                            </button> -->
                            <a href="<?= ROOT.$storecode."/products/".$product['id'] ?>">
                                <button class="view-product-btn">View</button>
                            </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- BANNER SECTION -->
    <section class="banner" style="background-image: url('<?= $content['promotional_img'] ?>')">
        <div class="banner-content">
            <h2 class="banner-heading"><?= $content['promotional_text'] ?></h2>
        </div>
    </section>

    <!-- FOOTER -->
     <?php require_once "../public/assets/components/templates/beam/footer.php" ?>

    <!-- <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3><i class="fas fa-bolt"></i> TechHub</h3>
                <p><?= $content['footer_text'] ?></p>
            </div>
            <div class="footer-section"></div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <ul>
                    <li><a href="<?= $content['fb_url'] ?>"><i class="fab fa-facebook"></i> Facebook</a></li>
                    <li><a href="<?= $content['insta_url'] ?>"><i class="fab fa-instagram"></i> Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p><?= $content['footer_copyright'] ?></p>
        </div>
    </footer> -->

    <script src="<?= ROOT ?>assets/js/pages/templates/beam/index.js"></script>
    <script>
        const stylesFromDB = {
            primary: "<?= $content['primary_color'] ?>",
        };

        const root = document.documentElement; // <html> element
        root.style.setProperty('--primary', stylesFromDB.primary);
    </script>
</body>

</html>