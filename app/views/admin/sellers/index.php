<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sellers - Vendora Admin</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/sellers.css">
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
          <h2 class="font-semibold">Sellers</h2>
          <p class="text-muted">Manage all registered sellers on the platform.</p>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">

        <!-- Search Bar -->
        <div class="filters-container" style="margin-bottom: 20px;">
          <form method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search ?? '') ?>" class="input" style="flex: 1; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
            <button type="submit" class="btn-filter">
              <i class="fas fa-filter"></i>
              Filter
            </button>
            <?php if (!empty($search)): ?>
              <a href="<?= ROOT ?>admin/dashboard/sellers" class="btn btn-secondary" style="padding: 8px 16px; text-decoration: none; display: flex; align-items: center;">Clear</a>
            <?php endif; ?>
          </form>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Seller Name</th>
                <th>Email</th>
                <th>Date Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($sellers)): ?>
                <?php foreach ($sellers as $seller): ?>
                  <tr>
                    <td>
                      <div class="seller-info">
                        <span class="font-semibold"><?= htmlspecialchars($seller['name']) ?></span>
                      </div>
                    </td>
                    <td><?= htmlspecialchars($seller['email']) ?></td>
                    <td><?= date('M d, Y', strtotime($seller['created_at'])) ?></td>
                    <td>
                      <div class="action-buttons">
                        <a href="<?= ROOT ?>admin/dashboard/sellers?view_id=<?= $seller['id'] ?>" class="action-btn view" title="See Seller Information" style="text-decoration: none;">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?= ROOT ?>admin/dashboard/sellers?delete_id=<?= $seller['id'] ?>" class="action-btn delete" title="Delete Seller" onclick="return confirm('Are you sure you want to completely delete this seller and their data?');" style="text-decoration: none;">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" style="text-align: center; padding: 20px; color: #6b7280;">
                    No sellers found matching the current search criteria.
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