<?php
$categoryOptions = [];
if (!empty($products)) {
  foreach ($products as $product) {
    $categoryName = trim((string)($product['category_name'] ?? ''));
    if ($categoryName === '') {
      $categoryName = 'Uncategorized';
    }
    $categoryOptions[strtolower($categoryName)] = $categoryName;
  }
  asort($categoryOptions, SORT_NATURAL | SORT_FLAG_CASE);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Products</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/all.css">
  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>
    <main class="content">
      <!-- Alert Messages -->
      <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'product_deleted'): ?>
          <div class="alert alert-success" style="margin-bottom: 1rem">
            <div class="alert-icon">✅</div>
            <div class="alert-content">
              <div class="alert-title">Product Deleted</div>
              <div class="alert-description">
                <?php if (isset($_GET['product_name'])): ?>
                  "<?= htmlspecialchars(urldecode($_GET['product_name'])) ?>" has been successfully deleted.
                <?php else: ?>
                  The product has been successfully deleted.
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php elseif ($_GET['success'] === 'product_updated'): ?>
          <div class="alert alert-success" style="margin-bottom: 1rem">
            <div class="alert-icon">✅</div>
            <div class="alert-content">
              <div class="alert-title">Product Updated</div>
              <div class="alert-description">
                <?php if (isset($_GET['product_name'])): ?>
                  "<?= htmlspecialchars(urldecode($_GET['product_name'])) ?>" has been successfully updated.
                <?php else: ?>
                  The product has been successfully updated.
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error" style="margin-bottom: 1rem">
          <div class="alert-icon">❌</div>
          <div class="alert-content">
            <div class="alert-title">Error</div>
            <div class="alert-description">
              <?php
              switch ($_GET['error']) {
                case 'delete_failed':
                  echo 'Failed to delete the product. Please try again.';
                  break;
                case 'update_failed':
                  echo 'Failed to update the product. Please try again.';
                  break;
                case 'unauthorized':
                  echo 'You are not authorized to perform this action.';
                  break;
                case 'missing_product_id':
                  echo 'Product ID is missing.';
                  break;
                case 'missing_name':
                  echo 'Product name is required.';
                  break;
                case 'invalid_method':
                  echo 'Invalid request method.';
                  break;
                default:
                  echo 'An unexpected error occurred.';
              }
              ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Product Dashboard Header -->
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold">All Products</h2>
          <p class="text-muted text-sm">Manage all your products with pagination</p>
        </div>
        <div class="flex gap-2">
          <!-- Add Product Button -->
          <a href="<?= ROOT ?>dashboard/products/newproduct" class="btn btn-secondary" style="text-decoration: none;">
            <i class="fa-solid fa-plus pr-2 blue"></i> Add New Product
          </a>
          <!-- Export Button -->
          <div class="btn btn-secondary" onclick="window.open('?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>', '_blank')" style="cursor: pointer;">
            <i class="fa-solid fa-download pr-2"></i> Export Products
          </div>
        </div>
      </div>

      <!-- Cards Section -->
      <div class="grid grid-cols-4 gap-3 mt-5">
        <!-- Total Products -->
        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Products</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['total_products'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-box-archive text-3xl gray"></i>
        </div>

        <!-- Active Products -->
        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Active Products</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['active_products'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-check text-3xl green"></i>
        </div>

        <!-- Low Stock Alert -->
        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Low Stock</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['low_stock'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-circle-exclamation text-3xl red"></i>
        </div>

        <!-- Total Value -->
        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Value <span class="text-bold">(USD)</span> </div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['total_value'] ?? 0, 2) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-sack-dollar text-3xl gray"></i>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="card mt-5 gap-2 p-3">
        <div class="card-header mb-3">
          <div class="card-subtitle">Filter Products</div>
        </div>
        <div class="flex flex-wrap gap-2 items-center">

          <!-- Search Bar -->
          <div class="card flex items-center px-2 py-1 searchBar searchBar--wide">
            <i class="fa-solid fa-magnifying-glass mr-2 text-gray-500 text-sm"></i>
            <input type="text" id="search-input" placeholder="Search products..." class="outline-none text-sm border-0 w-full">
          </div>

          <!-- Category Dropdown -->
          <div class="card flex items-center px-2 py-1 searchBar">
            <i class="fa-solid fa-tags mr-2 lightblue text-sm"></i>
            <select id="category-filter" class="outline-none text-sm border-0 w-full">
              <option value="">All Categories</option>
              <?php foreach ($categoryOptions as $categoryValue => $categoryLabel): ?>
                <option value="<?= htmlspecialchars($categoryValue) ?>"><?= htmlspecialchars($categoryLabel) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Stock Status Dropdown -->
          <div class="card flex items-center px-2 py-1 searchBar">
            <i class="fa-solid fa-boxes-stacked mr-2 gray text-sm"></i>
            <select id="stock-filter" class="outline-none text-sm border-0 w-full">
              <option value="">All Stock Status</option>
              <option value="in-stock">In Stock</option>
              <option value="low-stock">Low Stock</option>
              <option value="out-of-stock">Out of Stock</option>
            </select>
          </div>

          <!-- Visibility Status Dropdown -->
          <div class="card flex items-center px-2 py-1 searchBar">
            <i class="fa-solid fa-eye mr-2 blue text-sm"></i>
            <select id="visibility-filter" class="outline-none text-sm border-0 w-full">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <!-- Clear Filters Button -->
          <button id="clear-filters-btn" class="btn btn-outline" style="display: none;">
            <i class="fa-solid fa-xmark pr-2"></i>Clear Filters
          </button>

        </div>
      </div>


      <!-- Recent Activity -->
      <div class="card mt-5 gap-3 p-0">
        <table class="table w-full text-sm mb-0 rounded-lg">
          <thead>
            <tr class="text-left">

              <th class="px-6 py-3">Product</th>
              <th class="px-6 py-3">Action</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Last Updated</th>
              <th class="px-6 py-3">Price (USD)</th>
              <th class="px-6 py-3">Stock</th>
              <th class="px-6 py-3">Variant</th>
              <th class="px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($products)): ?>
              <?php foreach ($products as $product): ?>
                <?php $rowCategory = trim((string)($product['category_name'] ?? ''));
                if ($rowCategory === '') {
                  $rowCategory = 'Uncategorized';
                }
                ?>
                <tr data-category="<?= htmlspecialchars(strtolower($rowCategory)) ?>">

                  <td class="px-6 py-4 flex items-center gap-3">
                    <?php if (!empty($product['first_image'])): ?>
                      <img src="<?= ROOT . $product['first_image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-10 h-10 rounded-lg object-cover">
                    <?php else: ?>
                      <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                        <i class="fa-solid fa-image text-gray-400 text-sm"></i>
                      </div>
                    <?php endif; ?>
                    <a href="<?= ROOT ?>dashboard/products/<?= $product['id'] ?>/edit" class="font-medium hover:opacity-70 transition-all duration-200" style="text-decoration: none; color: #000;">
                      <?= htmlspecialchars($product['name']) ?>
                    </a>
                  </td>
                  <td class="px-6 py-4">
                    <?= $product['created_at'] == $product['updated_at'] ? 'Created' : 'Updated' ?>
                  </td>
                  <td class="px-6 py-4">
                    <?php if ($product['visibility'] == 1): ?>
                      <span class="badge badge-success">Active</span>
                    <?php else: ?>
                      <span class="badge badge-destructive">Inactive</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4">
                    <?= date('M d, Y', strtotime($product['updated_at'])) ?>
                  </td>
                  <td class="px-6 py-4">
                    <?php
                    if ($product['is_variant'] == 1 && isset($product['price_range'])) {
                      // Show price range for variant products
                      if ($product['price_range']['min'] == $product['price_range']['max']) {
                        echo number_format($product['price_range']['min'], 2);
                      } else {
                        echo number_format($product['price_range']['min'], 2) . ' - ' . number_format($product['price_range']['max'], 2);
                      }
                    } else {
                      // Show single price for non-variant products
                      echo $product['price'] ? number_format($product['price'], 2) : '-';
                    }
                    ?>
                  </td>
                  <td class="px-6 py-4">
                    <?php
                    if ($product['is_variant'] == 1) {
                      echo '<span title="Variant Product - Total Stock">';
                      echo number_format($product['stock_quantity'] ?? 0);
                      echo '</span>';
                    } else {
                      echo number_format($product['stock_quantity'] ?? 0);
                    }
                    ?>
                  </td>

                  <td class="px-6 py-4">
                    <?php if ($product['is_variant'] == 1): ?>
                      <span class="badge badge-multi">
                        <i class="fa-solid fa-layer-group"></i> Multi
                      </span>
                    <?php else: ?>
                      <span class="badge badge-single">
                        <i class="fa-solid fa-box"></i> Single
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4">
                    <button onclick="editProduct('<?= $product['id'] ?>')" class="action-btn edit-btn mr-3" title="Edit Product">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button onclick="deleteProduct('<?= $product['id'] ?>')" class="action-btn delete-btn" title="Delete Product">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                  No products found. <a href="<?= ROOT ?>dashboard/products/newproduct" class="text-primary">Add your first product</a>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- Table Footer -->
        <div class="tableFooter">
          <div class="text-sm text-gray-500">
            <?php
            $start = ($currentPage - 1) * $limit + 1;
            $end = min($currentPage * $limit, $totalProducts);
            ?>
            Showing <?= $start ?> to <?= $end ?> of <?= $totalProducts ?> results
          </div>
          <div class="flex items-center gap-2 text-sm">
            <?php if ($currentPage > 1): ?>
              <a href="?page=<?= $currentPage - 1 ?>" class="pageBtn" style="text-decoration: none;">Previous</a>
            <?php endif; ?>

            <?php
            // Calculate which page numbers to show
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);

            // Show first page if we're not starting from 1
            if ($startPage > 1): ?>
              <a href="?page=1" class="pageBtn" style="text-decoration: none;">1</a>
              <?php if ($startPage > 2): ?>
                <span class="pageBtn">...</span>
              <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
              <?php if ($i == $currentPage): ?>
                <span class="currentPage"><?= $i ?></span>
              <?php else: ?>
                <a href="?page=<?= $i ?>" class="pageBtn" style="text-decoration: none;"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>

            <?php
            // Show last page if we're not ending at the last page
            if ($endPage < $totalPages): ?>
              <?php if ($endPage < $totalPages - 1): ?>
                <span class="pageBtn">...</span>
              <?php endif; ?>
              <a href="?page=<?= $totalPages ?>" class="pageBtn" style="text-decoration: none;"><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
              <a href="?page=<?= $currentPage + 1 ?>" class="pageBtn" style="text-decoration: none;">Next</a>
            <?php endif; ?>
          </div>
        </div>

    </main>
  </div>

  <!-- Quick Edit Product Modal -->
  <div class="modal-overlay" id="edit-product-modal">
    <div class="modal quick-edit-modal">
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Quick Edit Product</h3>
          <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: #6b7280; font-weight: normal;">Edit basic product information</p>
        </div>
        <button type="button" onclick="closeModal('edit-product-modal')" class="modal-close-btn" aria-label="Close modal">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="modal-body">
        <!-- Loading State -->
        <div id="edit-loading" style="display: none; text-align: center; padding: 2rem;">
          <i class="fa-solid fa-spinner fa-spin text-3xl" style="color: var(--primary); margin-bottom: 1rem;"></i>
          <p style="color: #6b7280;">Loading product data...</p>
        </div>

        <!-- Error State -->
        <div id="edit-error" style="display: none;">
          <div class="alert alert-error">
            <div class="alert-icon">❌</div>
            <div class="alert-content">
              <div class="alert-title">Error Loading Product</div>
              <div class="alert-description" id="edit-error-message">Failed to load product data. Please try again.</div>
            </div>
          </div>
        </div>

        <!-- Form -->
        <form id="edit-product-form" method="POST" style="display: none;">
          <input type="hidden" id="edit-product-id" name="product_id" value="">
          <input type="hidden" id="edit-is-variant" name="is_variant" value="0">

          <!-- Product Name -->
          <div class="form-group">
            <label for="edit-name" class="form-label">
              <i class="fa-solid fa-tag" style="color: #3b82f6;"></i>
              Product Name <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" id="edit-name" name="name" class="input" placeholder="Enter product name" required>
          </div>

          <!-- Single Product Fields -->
          <div id="single-product-fields" style="display: none;">
            <div class="form-row">
              <div class="form-group">
                <label for="edit-price" class="form-label">
                  <i class="fa-solid fa-dollar-sign" style="color: #16a34a;"></i>
                  Price (USD)
                </label>
                <input type="number" id="edit-price" name="price" class="input" step="0.01" min="0" placeholder="0.00">
              </div>
              <div class="form-group">
                <label for="edit-stock" class="form-label">
                  <i class="fa-solid fa-boxes-stacked" style="color: #f59e0b;"></i>
                  Stock Quantity
                </label>
                <input type="number" id="edit-stock" name="stock_quantity" class="input" min="0" placeholder="0">
              </div>
            </div>
          </div>

          <!-- Variant Product Fields -->
          <div id="variant-product-fields" style="display: none;">
            <div class="variant-info-banner">
              <i class="fa-solid fa-layer-group" style="color: #3b82f6;"></i>
              <div>
                <strong>Multi-Variant Product</strong>
                <p>Edit price and stock for each variant below</p>
              </div>
            </div>

            <div id="variants-list" class="variants-container">
              <!-- Variants will be inserted here by JavaScript -->
            </div>
          </div>

          <!-- Visibility Toggle -->
          <div class="form-group" style="margin-top: 1.5rem;">
            <label class="checkbox-label">
              <input type="checkbox" id="edit-visibility" name="visibility" class="checkbox-input">
              <span class="checkbox-text">
                <i class="fa-solid fa-eye" style="color: #3b82f6;"></i>
                Product is Active and Visible
              </span>
            </label>
          </div>

          <!-- Image Edit Notice -->
          <div class="image-edit-notice">
            <div class="image-edit-content">
              <i class="fa-solid fa-images"></i>
              <div>
                <strong>Need to edit images or add more options?</strong>
                <p>Switch to full edit mode for advanced features</p>
              </div>
            </div>
            <button type="button" id="full-edit-btn" class="btn btn-outline btn-sm">
              <i class="fa-solid fa-arrow-up-right-from-square" style="margin-right: 0.5rem;"></i> Edit
            </button>
          </div>
        </form>
      </div>

      <div class="modal-footer" id="modal-footer-actions" style="display: none;">
        <button type="button" class="btn btn-secondary" onclick="closeModal('edit-product-modal')">
          <i class="fa-solid fa-xmark" style="margin-right: 0.5rem;"></i> Cancel
        </button>
        <button type="submit" form="edit-product-form" class="btn btn-primary">
          <i class="fa-solid fa-check" style="margin-right: 0.5rem;"></i> Save Changes
        </button>
      </div>
    </div>
  </div>

  <script>
    // Pass PHP variables to JavaScript
    window.APP_CONFIG = {
      ROOT: '<?= ROOT ?>',
      BASE_URL: '<?= ROOT ?>dashboard/products/'
    };
  </script>
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
  <script src="<?= ROOT ?>assets/js/components/modal.js"></script>
  <script src="<?= ROOT ?>assets/js/products/all.js"></script>
</body>

</html>