<?php

// pre($products);
// All filtering, sorting, and pagination is done in the controller
// This view just renders the pre-filtered data
// Extract pagination info
$totalProducts = $pagination['totalProducts'] ?? 0;
$totalPages = $pagination['totalPages'] ?? 1;
$currentPage = $pagination['currentPage'] ?? 1;
$startIndex = $pagination['startIndex'] ?? 0;
$limit = $pagination['limit'] ?? 12;
$searchQuery = $pagination['search'] ?? '';
$sortBy = $pagination['sort'] ?? 'newest';

// Build query string for links (excluding page param)
$queryParams = [];
if (!empty($searchQuery)) $queryParams['search'] = $searchQuery;
if ($sortBy !== 'newest') $queryParams['sort'] = $sortBy;

function buildQueryString($params, $page = null)
{
    if ($page !== null) $params['page'] = $page;
    return http_build_query($params);
}

/**
 * Get product display price (handles variants and simple products)
 * For variant products, shows minimum variant price
 */
function getProductPrice($product)
{
    // Use display_price from query if available (handles variants)
    if (!empty($product['display_price'])) {
        return floatval($product['display_price']);
    }
    // Fallback to product price
    if (!empty($product['price'])) {
        return floatval($product['price']);
    }
    return 0;
}

/**
 * Get product display image
 */
function getProductImage($product)
{
    // Get first image from images array
    if (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
        return $product['images'][0];
    }
    // Fallback placeholder
    return '';
}

/**
 * Get product stock status
 */
function getProductStock($product)
{
    if (!empty($product['total_stock'])) {
        return intval($product['total_stock']);
    }
    return 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "../public/assets/components/templates/prestige/head.php" ?>
</head>

<body data-storeid="<?= htmlspecialchars($store['id'] ?? '') ?>">
    <!-- NAVBAR -->
    <?php require_once "../public/assets/components/templates/prestige/navbar.php" ?>

    <!-- <nav class="navbar">
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

    <!-- SHOP HEADER -->
    <section class="shop-header">
        <div class="section-header">
            <h2>Shop</h2>
        </div>
    </section>

    <!-- SEARCH & CONTROLS -->
    <div class="products-container">
        <form method="GET" class="shop-filters" id="filterForm">
            <div class="search-container">
                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    placeholder="Search Products"
                    value="<?= htmlspecialchars($searchQuery) ?>"
                    autocomplete="off">
            </div>
            <div class="shop-controls">
                <div class="product-count" id="productCount">
                    <?php if ($totalProducts > 0): ?>
                        Showing <?= $startIndex + 1 ?>–<?= min($startIndex + $limit, $totalProducts) ?> of <?= $totalProducts ?> products
                    <?php else: ?>
                        No products found
                    <?php endif; ?>
                </div>
                <div class="sort-dropdown">
                    <select name="sort" id="sortSelect" onchange="document.getElementById('filterForm').submit()">
                        <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Newest</option>
                        <option value="price-low" <?= $sortBy === 'price-low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price-high" <?= $sortBy === 'price-high' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="name-asc" <?= $sortBy === 'name-asc' ? 'selected' : '' ?>>Name: A to Z</option>
                        <option value="name-desc" <?= $sortBy === 'name-desc' ? 'selected' : '' ?>>Name: Z to A</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- PRODUCT GRID -->
        <div class="products-section" style="padding: 2rem;">
            <div class="products-container">
                <?php if (!empty($products)): ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product):
                            $productPrice = getProductPrice($product);
                            $productImage = getProductImage($product);
                            $productStock = getProductStock($product);
                            $isOutOfStock = $productStock === 0;
                        ?>
                            <div class="product-card" onclick="viewProduct('<?= htmlspecialchars($product['id']) ?>')">
                                <div class="product-image">
                                    <img src="<?= htmlspecialchars($productImage) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <?php if ($isOutOfStock): ?>
                                        <div class="out-of-stock-badge">Out of Stock</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                                    <div class="product-pricing">
                                        <?php if ($productPrice > 0): ?>
                                            <span class="product-price">$ <?= number_format($productPrice, 2) ?></span>
                                            <?php if (!empty($product['is_variant'])): ?>
                                                <span style="font-size: 0.8rem; color: var(--gray); font-weight: 400;">from</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: var(--gray); font-size: 0.9rem;">Price on Request</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- PAGINATION -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <!-- Previous button -->
                            <?php if ($currentPage > 1): ?>
                                <a href="?<?= buildQueryString($queryParams, 1) ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn" disabled>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            <?php endif; ?>

                            <!-- Page numbers -->
                            <?php
                            $maxPagesToShow = 5;
                            $startPage = max(1, $currentPage - intval($maxPagesToShow / 2));
                            $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

                            if ($endPage - $startPage < $maxPagesToShow - 1) {
                                $startPage = max(1, $endPage - $maxPagesToShow + 1);
                            }

                            // First page
                            if ($startPage > 1): ?>
                                <a href="?<?= buildQueryString($queryParams, 1) ?>" class="pagination-btn">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span style="padding: 0 0.5rem; color: var(--gray);">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Page numbers -->
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <button class="pagination-btn active"><?= $i ?></button>
                                <?php else: ?>
                                    <a href="?<?= buildQueryString($queryParams, $i) ?>" class="pagination-btn"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <!-- Last page -->
                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span style="padding: 0 0.5rem; color: var(--gray);">...</span>
                                <?php endif; ?>
                                <a href="?<?= buildQueryString($queryParams, $totalPages) ?>" class="pagination-btn"><?= $totalPages ?></a>
                            <?php endif; ?>

                            <!-- Next button -->
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?<?= buildQueryString($queryParams, $currentPage + 1) ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn" disabled>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- EMPTY STATE -->
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your search or filters</p>
                        <!-- <a href="?" class="cta-btn">
                        <i class="fas fa-redo"></i> Clear Search
                    </a> -->
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- FOOTER -->
        <?php require_once "../public/assets/components/templates/prestige/footer.php" ?>

        <!-- <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3><i class="fas fa-bolt"></i> TechHub</h3>
                <p>Your ultimate destination for premium tech products and accessories.</p>
            </div>
            <div class="footer-section"></div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <ul>
                    <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($store['name'] ?? 'Store') ?>. All rights reserved. Store Code: <?= htmlspecialchars($store['code'] ?? 'N/A') ?></p>
        </div>
    </footer> -->

        <script src="<?= ROOT ?>assets/js/pages/templates/prestige/index.js"></script>
        <script>
            const stylesFromDB = {
                primary: "<?= $content['primary_color'] ?>",
            };

            const root = document.documentElement; // <html> element
            root.style.setProperty('--primary', stylesFromDB.primary);
        </script>
        <script>
            /**
             * View product - navigate to product detail page
             */
            function viewProduct(productId) {
                // Navigate to product detail page using store code and product ID
                const storeId = document.body.getAttribute('data-storeid');
                window.location.href = `<?= ROOT ?><?= $storecode ?>/products/${productId}`;
            }

            /**
             * Setup event listeners
             */
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const filterForm = document.getElementById('filterForm');

                if (searchInput) {
                    // Debounced search submission
                    let searchTimeout;
                    searchInput.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(function() {
                            filterForm.submit();
                        }, 500);
                    });
                }
            });
        </script>
</body>

</html>