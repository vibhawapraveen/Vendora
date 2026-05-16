<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stores - Vendora Admin</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/sellers.css">
  <style>
    a, .filter-btn, .action-btn {
      text-decoration: none !important;
    }
  </style>
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
          <h2 class="font-semibold">Stores</h2>
          <p class="text-muted">Manage all stores on the platform.</p>
        </div>

      </div>

      <!-- Table Section -->
      <div class="table-container">
        <!-- Search and Filter Header -->
        <div class="table-header">
          <form method="GET" class="search-filter-row">
            <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
            <div class="search-input-wrapper">
              <input type="text" name="search" class="input" placeholder="Search stores..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit" class="btn-filter">
              <i class="fas fa-filter"></i>
              Filter
            </button>
            <?php if (!empty($search)): ?>
            <a href="<?= ROOT ?>admin/dashboard/stores?status=<?= htmlspecialchars($status) ?>" class="btn btn-secondary" style="text-decoration: none;">Clear</a>
            <?php endif; ?>
          </form>
          
          <div class="filter-buttons">
            <a href="<?= ROOT ?>admin/dashboard/stores?status=all&search=<?= urlencode($search) ?>" class="filter-btn <?= $status === 'all' || empty($status) ? 'active' : '' ?>" style="text-decoration: none;">All</a>
            <a href="<?= ROOT ?>admin/dashboard/stores?status=active&search=<?= urlencode($search) ?>" class="filter-btn <?= $status === 'active' ? 'active' : '' ?>" style="text-decoration: none;">Active</a>
            <a href="<?= ROOT ?>admin/dashboard/stores?status=disabled&search=<?= urlencode($search) ?>" class="filter-btn <?= $status === 'disabled' ? 'active' : '' ?>" style="text-decoration: none;">Disabled</a>
          </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Store Name</th>
                <th>Owner</th>
                <th>Products</th>
                <th>Created Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($stores)): ?>
                <?php foreach ($stores as $store): ?>
                <tr>
                  <td>
                    <div class="seller-info">
                      <div class="seller-avatar text-center" style="display:flex; justify-content:center; align-items:center;"><?= strtoupper(substr($store['store_name'], 0, 2)) ?></div>
                      <span class="font-semibold"><?= htmlspecialchars($store['store_name']) ?></span>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($store['owner_name']) ?></td>
                  <td><?= htmlspecialchars($store['products_count']) ?></td>
                  <td><?= date('M d, Y', strtotime($store['created_at'])) ?></td>
                  <td>
                    <?php if ($store['visibility']): ?>
                        <span class="badge badge-primary badge-sm">Active</span>
                    <?php else: ?>
                        <span class="badge badge-secondary badge-sm">Disabled</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <a href="<?= ROOT ?>admin/dashboard/stores?view_id=<?= $store['id'] ?>" class="action-btn view" title="See Store Catalog" style="text-decoration: none;">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="<?= ROOT ?>admin/dashboard/stores?toggle_id=<?= $store['id'] ?>&current=<?= $store['visibility'] ?>" class="action-btn suspend" title="Toggle Status" style="text-decoration: none;">
                        <i class="fas <?= $store['visibility'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                      </a>
                      <a href="<?= ROOT ?>admin/dashboard/stores?delete_id=<?= $store['id'] ?>" class="action-btn delete" title="Delete Store" onclick="return confirm('WARNING: Deleting this store will delete its store configurations, products, and categories. Proceed?');" style="text-decoration: none;">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" style="text-align: center; padding: 30px; color: #6b7280;">
                      <i class="fas fa-store-slash" style="font-size: 30px; display:block; margin-bottom: 10px; color: #9ca3af;"></i>
                      No stores found matching your criteria.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>