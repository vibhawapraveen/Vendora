<?php
// pre($pagination);
// pre($products);

$totalProducts = $pagination['totalProducts'] ?? 0;
$totalPages = $pagination['totalPages'] ?? 1;
$currentPage = $pagination['currentPage'] ?? 1;
$startIndex = $pagination['startIndex'] ?? 0;
$limit = $pagination['limit'] ?? 12;
$searchQuery = $pagination['search'] ?? '';
$sortBy = $pagination['sort'] ?? 'newest';
$category = $pagination['category'] ?? '';

$queryParams = [];
if (!empty($searchQuery)) $queryParams['search'] = $searchQuery;
if ($sortBy !== 'newest') $queryParams['sort'] = $sortBy;

function buildQueryString($params, $page = null)
{
    if ($page !== null) $params['page'] = $page;
    return http_build_query($params);
}

function getProductPrice($product)
{
    if (!empty($product['display_price'])) {
        return floatval($product['display_price']);
    }

    if (!empty($product['price'])) {
        return floatval($product['price']);
    }
    return 0;
}

function getProductImage($product)
{
    if (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
        return $product['images'][0];
    }
    return '';
}

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
    <?php require_once "../public/assets/components/templates/ignite/head.php" ?>
</head>

<body data-storeid="<?= htmlspecialchars($store['id'] ?? '') ?>">
    <?php require_once "../public/assets/components/templates/ignite/navbar.php" ?>

    <section class="shop-header">
        <div class="section-header">
            <h2>Shop</h2>
        </div>
    </section>

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

                <div class="sort-dropdown">
                    Filter by category
                    <select name="category" id="categorySelect" onchange="document.getElementById('filterForm').submit()">
                        <option value="" <?= $category === '' ? 'selected' : '' ?>>None</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['name'] ?>" <?= $cat['name'] === $category ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

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

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?<?= buildQueryString($queryParams, 1) ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn" disabled>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            <?php endif; ?>
                            <?php
                            $maxPagesToShow = 5;
                            $startPage = max(1, $currentPage - intval($maxPagesToShow / 2));
                            $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

                            if ($endPage - $startPage < $maxPagesToShow - 1) {
                                $startPage = max(1, $endPage - $maxPagesToShow + 1);
                            }

                            if ($startPage > 1): ?>
                                <a href="?<?= buildQueryString($queryParams, 1) ?>" class="pagination-btn">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span style="padding: 0 0.5rem; color: var(--gray);">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <button class="pagination-btn active"><?= $i ?></button>
                                <?php else: ?>
                                    <a href="?<?= buildQueryString($queryParams, $i) ?>" class="pagination-btn"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span style="padding: 0 0.5rem; color: var(--gray);">...</span>
                                <?php endif; ?>
                                <a href="?<?= buildQueryString($queryParams, $totalPages) ?>" class="pagination-btn"><?= $totalPages ?></a>
                            <?php endif; ?>

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

        <?php require_once "../public/assets/components/templates/ignite/footer.php" ?>


        <script src="<?= ROOT ?>assets/js/pages/templates/ignite/index.js"></script>
        <script>
            const stylesFromDB = {
                primary: "<?= $content['primary_color'] ?>",
            };

            const root = document.documentElement;
            root.style.setProperty('--primary', stylesFromDB.primary);
        </script>
        <script>
            function viewProduct(productId) {
                const storeId = document.body.getAttribute('data-storeid');
                window.location.href = `<?= ROOT ?><?= $storecode ?>/products/${productId}`;
            }

            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const filterForm = document.getElementById('filterForm');

                if (searchInput) {
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