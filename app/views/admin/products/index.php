<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - Vendora Admin</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/dashboard.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/sellers.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/all.css">

</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/admin-sidebar.php' ?>

    <main class="content">
      <!-- Header Section -->
      <div class="page-header">
        <div class="page-header-left">
          <h2 class="font-semibold">Products</h2>
          <p class="text-muted">Platform-wide product management.</p>
        </div>
        <!-- Export Button -->
        <a class="btn btn-secondary" href="<?= ROOT ?>admin/dashboard/products?export=pdf" target="_blank" rel="noopener" style="cursor: pointer; text-decoration: none;">
          <i class="fa-solid fa-download pr-2"></i> Export Products
        </a>
      </div>

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

        <!-- Banned Products -->
        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Banned Products</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['banned_products'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-pause text-3xl text-amber"></i>
        </div>

        <!-- Total Platform Revenue -->
        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Revenue <span class="text-bold">(USD)</span> </div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['total_platform_fee'] ?? 0, 2) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-sack-dollar text-3xl gray"></i>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <!-- Search and Filter Header -->
        <div class="table-header">
          <div class="products-toolbar-row">
            <div class="search-filter-row" style="margin: 0; flex: 1;">
              <div class="search-input-wrapper">
                <input type="text" id="search-input" class="input" placeholder="Search by product or category...">
              </div>
            </div>
          </div>

          <div class="filter-buttons">
            <button class="filter-btn active" type="button" data-filter="all">All</button>
            <button class="filter-btn" type="button" data-filter="active">Active</button>
            <button class="filter-btn" type="button" data-filter="inactive">Inactive</button>
            <button class="filter-btn" type="button" data-filter="deleted">Deleted</button>
            <button class="filter-btn" type="button" data-filter="banned">Banned</button>
          </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Store</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                  <tr data-status="<?= ($product['is_banned'] ?? 0) == 1 ? 'banned' : ((($product['delete_flag'] ?? 0) == 1) ? 'deleted' : ((($product['visibility'] ?? 0) == 1) ? 'active' : 'inactive')) ?>">
                    <!-- Name -->
                    <td class="px-6 py-4 flex items-center gap-3">
                      <?php if (!empty($product['first_image'])): ?>
                        <img src="<?= ROOT . $product['first_image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-10 h-10 rounded-lg object-cover">
                      <?php else: ?>
                        <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                          <i class="fa-solid fa-image text-gray-400 text-sm"></i>
                        </div>
                      <?php endif; ?>
                      <div class="font-medium" style="text-decoration: none; color: #000;">
                        <?= htmlspecialchars($product['name']) ?>
                      </div>
                    </td>

                    <!-- Store Name -->
                    <td class="px-6 py-4">
                      <?= htmlspecialchars($product['store_name']) ?>
                    </td>

                    <!-- Category -->
                    <td class="px-6 py-4">
                      <?= !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Uncategorized' ?>
                    </td>

                    <!-- Price -->
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

                    <!-- Stock -->
                    <td class="px-6 py-4">
                      <?php
                      if ($product['is_variant'] == 1) {
                        echo number_format($product['stock_quantity'] ?? 0);
                      } else {
                        echo number_format($product['stock_quantity'] ?? 0);
                      }
                      ?>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                      <?php if (($product['is_banned'] ?? 0) == 1): ?>
                        <span class="badge badge-destructive">Banned</span>
                      <?php elseif (($product['delete_flag'] ?? 0) == 1): ?>
                        <span class="badge badge-destructive">Deleted</span>
                      <?php else: ?>
                        <?php if (($product['visibility'] ?? 0) == 1): ?>
                          <span class="badge badge-success">
                            Active
                          </span>
                        <?php else: ?>
                          <span class="badge badge-default">
                            Inactive
                          </span>
                        <?php endif; ?>
                      <?php endif; ?>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4">
                      <?php if (($product['is_banned'] ?? 0) == 1): ?>
                        <button
                          class="action-btn"
                          type="button"
                          title="Un-Ban Product"
                          onclick="openProductStatusModal('<?= $product['id'] ?>', '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>', true)">
                          <i class="fa-solid fa-unlock"></i>
                        </button>
                      <?php else: ?>
                        <button
                          class="action-btn"
                          type="button"
                          title="Ban Product"
                          onclick="openProductStatusModal('<?= $product['id'] ?>', '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>', false, <?= (int) ($product['delete_flag'] ?? 0) ?>)">
                          <i class="fa-solid fa-ban"></i>
                        </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <tr id="no-products-row" style="display:none;">
                  <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    No products to show.
                  </td>
                </tr>
              <?php else: ?>
                <tr id="no-products-row">
                  <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    No products found.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- TABLE FOOTER -->
        <div class="table-footer">
          <?php
          $pagination = $pagination ?? [];
          $totalPages = $pagination['totalPages'] ?? 1;
          $currentPage = $pagination['currentPage'] ?? 1;
          $totalProducts = $pagination['totalProducts'] ?? 0;
          $limit = $pagination['limit'] ?? 10;
          ?>
          <div class="footer-info">
            <span>Showing <?= max(1, ($currentPage - 1) * $limit + 1) ?> to <?= min($currentPage * $limit, $totalProducts) ?> of <?= $totalProducts ?> products</span>
          </div>
          <?php if ($totalPages > 1): ?>
            <div class="pagination">
              <!-- Previous button -->
              <?php if ($currentPage > 1): ?>
                <a href="<?= ROOT ?>admin/dashboard/products?page=1" class="pagination-btn">
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
                <a href="<?= ROOT ?>admin/dashboard/products?page=1" class="pagination-btn">1</a>
                <?php if ($startPage > 2): ?>
                  <span style="padding: 0 0.5rem; color: var(--text-light);">...</span>
                <?php endif; ?>
              <?php endif; ?>

              <!-- Page numbers -->
              <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <?php if ($i === $currentPage): ?>
                  <button class="pagination-btn active"><?= $i ?></button>
                <?php else: ?>
                  <a href="<?= ROOT ?>admin/dashboard/products?page=<?= $i ?>" class="pagination-btn"><?= $i ?></a>
                <?php endif; ?>
              <?php endfor; ?>

              <!-- Last page -->
              <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                  <span style="padding: 0 0.5rem; color: var(--text-light);">...</span>
                <?php endif; ?>
                <a href="<?= ROOT ?>admin/dashboard/products?page=<?= $totalPages ?>" class="pagination-btn"><?= $totalPages ?></a>
              <?php endif; ?>

              <!-- Next button -->
              <?php if ($currentPage < $totalPages): ?>
                <a href="<?= ROOT ?>admin/dashboard/products?page=<?= $currentPage + 1 ?>" class="pagination-btn">
                  <i class="fas fa-chevron-right"></i>
                </a>
              <?php else: ?>
                <button class="pagination-btn" disabled>
                  <i class="fas fa-chevron-right"></i>
                </button>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="modal-overlay" id="product-status-modal" style="display:none;">
        <div class="modal">
          <div class="modal-header" id="product-status-modal-title">Ban Product</div>
          <div class="modal-body">
            <form method="POST" action="<?= ROOT ?>admin/dashboard/products" id="product-status-form">
              <input type="hidden" name="action" id="product-status-action" value="ban_product">
              <input type="hidden" name="product_id" id="product-status-id">

              <div class="mb-3">
                <label class="mb-1" for="product-status-name" style="display:block;">Product Name</label>
                <input type="text" id="product-status-name" class="input" readonly>
                <p class="text-sm text-muted mt-2" id="product-status-help-text">This action marks the product as banned and sets it to inactive.</p>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary btn-sm" type="button" onclick="closeProductStatusModal()">Cancel</button>
            <button class="btn btn-primary btn-sm" type="submit" form="product-status-form" id="product-status-submit">Confirm Ban</button>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
  <script>
    function openProductStatusModal(productId, productName, isBanned, isDeleted) {
      var modal = document.getElementById('product-status-modal');
      var idInput = document.getElementById('product-status-id');
      var actionInput = document.getElementById('product-status-action');
      var nameInput = document.getElementById('product-status-name');
      var titleEl = document.getElementById('product-status-modal-title');
      var helpTextEl = document.getElementById('product-status-help-text');
      var submitBtn = document.getElementById('product-status-submit');

      if (!modal || !idInput || !actionInput || !nameInput || !titleEl || !helpTextEl || !submitBtn) {
        return;
      }

      submitBtn.disabled = false;

      if (isBanned) {
        titleEl.textContent = 'Un-Ban Product';
        actionInput.value = 'unban_product';
        submitBtn.textContent = 'Confirm Un-Ban';
        helpTextEl.textContent = 'This action removes the banned flag and sets the product back to active.';
      } else if (isDeleted) {
        titleEl.textContent = 'Cannot Ban Product';
        actionInput.value = '';
        submitBtn.textContent = 'Cannot Ban';
        submitBtn.disabled = true;
        helpTextEl.textContent = 'This product is already deleted by the seller.';
      } else {
        titleEl.textContent = 'Ban Product';
        actionInput.value = 'ban_product';
        submitBtn.textContent = 'Confirm Ban';
        helpTextEl.textContent = 'This action marks the product as banned and sets it to inactive.';
      }

      idInput.value = productId;
      nameInput.value = productName;
      modal.style.display = 'flex';
    }

    function closeProductStatusModal() {
      var modal = document.getElementById('product-status-modal');

      if (!modal) {
        return;
      }

      modal.style.display = 'none';
    }

    (function initProductTableFilters() {
      var filterButtons = document.querySelectorAll('.filter-btn[data-filter]');
      var productRows = document.querySelectorAll('.table tbody tr[data-status]');
      var noProductsRow = document.getElementById('no-products-row');
      var searchInput = document.getElementById('search-input');

      if (!filterButtons.length || !productRows.length) {
        return;
      }

      var activeFilter = 'all';

      function applyFilters() {
        var query = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
        var visibleCount = 0;

        productRows.forEach(function(row) {
          var rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
          var rowText = row.textContent.toLowerCase();
          var statusMatches = activeFilter === 'all' || rowStatus === activeFilter;
          var searchMatches = !query || rowText.indexOf(query) !== -1;
          var shouldShow = statusMatches && searchMatches;

          row.style.display = shouldShow ? '' : 'none';

          if (shouldShow) {
            visibleCount++;
          }
        });

        if (noProductsRow) {
          noProductsRow.style.display = visibleCount === 0 ? 'table-row' : 'none';
        }
      }

      filterButtons.forEach(function(button) {
        button.addEventListener('click', function() {
          activeFilter = (button.getAttribute('data-filter') || 'all').toLowerCase();

          filterButtons.forEach(function(btn) {
            btn.classList.remove('active');
          });

          button.classList.add('active');
          applyFilters();
        });
      });

      if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
      }

      applyFilters();
    })();

    document.addEventListener('click', function(event) {
      var modal = document.getElementById('product-status-modal');

      if (!modal) {
        return;
      }

      if (event.target === modal) {
        closeProductStatusModal();
      }
    });
  </script>
</body>

</html>