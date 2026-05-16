<?php
$sections = $home_contents['sections'];
// pre($sections);
// ============ CAROUSEL DATA ============
$carouselSlides = $home_contents['carousel_items'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "../public/assets/components/templates/prestige/head.php" ?>
    <!-- <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/templates/prestige.css"> -->
    <script>
        // Pass carousel data from PHP to JavaScript
        const ROOT = '<?= ROOT ?>';
        window.carouselSlides = <?php echo json_encode($carouselSlides); ?>;
    </script>
</head>

<body data-storecode="<?= $storecode ?>">

    <?php require_once "../public/assets/components/templates/prestige/navbar.php" ?>


    <!-- ============ HERO SECTION ============ -->
    <section class="hero" id="home" style="background-image: url('<?= ROOT ?>assets/img/templates/prestige/hero-default.svg');">
        <button class="arrow-nav arrow-left" id="prevBtn">‹</button>
        <div class="hero-content">
            <h1>Premium Quality</h1>
            <p>DISCOVER LATEST COLLECTION</p>
            <a href="#shop" class="hero-btn">Shop Now</a>
        </div>
        <button class="arrow-nav arrow-right" id="nextBtn">›</button>

        <div class="carousel-dots">
            <?php foreach ($carouselSlides as $index => $slide): ?>
                <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
            <?php endforeach; ?>
        </div>
    </section>

    <?php foreach ($sections as $section): ?>
        <!-- ============ PRODUCTS SECTION ============ -->

        <?php if ($section['section_type'] == 'product_feature') { ?>
            <section class="products-section" id="shop">
                <div class="products-container">
                    <div class="section-header">
                        <h2><?= $section['title'] ?></h2>
                    </div>

                    <div class="products-grid" id="productsGrid">
                        <!-- Product cards will be generated here -->
                        <?php foreach ($section['products'] as $prod): ?>
                            <div class="product-card" onclick="redirect('<?= ROOT.$storecode?>/products/<?= $prod['product_id'] ?>');">
                                <div class="product-image">
                                    <img src="<?= ROOT . htmlspecialchars($prod['image_url']) ?>">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name"><?= $prod['name'] ?></h3>
                                    <div class="product-pricing">
                                        <span class="product-price">$ <?= number_format($prod['display_price'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- 
            <div class="">
                <a href="#" class="cta-btn">View All Products</a>
            </div> -->
                </div>
            </section>

        <?php } else if ($section['section_type'] == 'promotional_banner') { ?>

            <!-- ============ ABOUT SECTION ============ -->
            <section class="about-section" style="background-image: url('<?= $section['background_image'] ?>')">
                <div class="about-content">
                    <h2 class="about-heading"><?= $section['title'] ?></h2>
                </div>
            </section>

        <?php } ?>

    <?php endforeach; ?>

    <!-- ============ FOOTER ============ -->
    <?php require_once "../public/assets/components/templates/prestige/footer.php" ?>

    <script>
        function redirect($url) {
            window.location.href = $url;
        }
    </script>
    <script src="<?= ROOT ?>assets/js/pages/templates/prestige/index.js"></script>
</body>

</html>