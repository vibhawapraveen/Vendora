<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders - Vendora Admin</title>
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
          <h2 class="font-semibold">Orders</h2>
          <p class="text-muted">Manage all orders across all stores.</p>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <!-- Search and Filter Header -->
        <div class="table-header">
          <form method="GET" action="" class="search-filter-row">
            <?php if (!empty($currentStatus)): ?>
              <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
            <?php endif; ?>
            <div class="search-input-wrapper">
              <input type="text" name="search" class="input" placeholder="Search by order #, customer, or store..."
                value="<?= htmlspecialchars($searchQuery ?? '') ?>">
            </div>
            <button type="submit" class="btn-filter">
              <i class="fas fa-filter"></i>
              Filter
            </button>
          </form>
          <div class="filter-buttons">
            <?php
              $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
              $searchParam = !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '';
            ?>
            <a href="<?= $baseUrl ?>?status=all<?= $searchParam ?>" class="filter-btn <?= (empty($currentStatus) || $currentStatus === 'all') ? 'active' : '' ?>">
              All (<?= ($statusCounts['total'] ?? 0) ?>)
            </a>
            <a href="<?= $baseUrl ?>?status=pending<?= $searchParam ?>" class="filter-btn <?= ($currentStatus === 'pending') ? 'active' : '' ?>">
              Pending (<?= ($statusCounts['pending'] ?? 0) ?>)
            </a>
            <a href="<?= $baseUrl ?>?status=shipped<?= $searchParam ?>" class="filter-btn <?= ($currentStatus === 'shipped') ? 'active' : '' ?>">
              Shipped (<?= ($statusCounts['shipped'] ?? 0) ?>)
            </a>
            <a href="<?= $baseUrl ?>?status=delivered<?= $searchParam ?>" class="filter-btn <?= ($currentStatus === 'delivered') ? 'active' : '' ?>">
              Completed (<?= ($statusCounts['delivered'] ?? 0) ?>)
            </a>
            <a href="<?= $baseUrl ?>?status=cancelled<?= $searchParam ?>" class="filter-btn <?= ($currentStatus === 'cancelled') ? 'active' : '' ?>">
              Cancelled (<?= ($statusCounts['cancelled'] ?? 0) ?>)
            </a>
          </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Store</th>
                <th>Buyer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                  <td><span style="font-weight: 600; color: var(--primary);">#<?= htmlspecialchars($order['order_number']) ?></span></td>
                  <td><?= htmlspecialchars($order['store_name'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></td>
                  <td>$<?= number_format($order['total_amount'], 2) ?></td>
                  <td>
                    <?php
                      $statusClass = 'badge-default';
                      $statusLabel = ucfirst($order['status']);
                      if ($order['status'] === 'delivered') {
                        $statusClass = 'badge-primary';
                        $statusLabel = 'Completed';
                      } elseif ($order['status'] === 'cancelled') {
                        $statusClass = 'badge-destructive';
                      } elseif ($order['status'] === 'shipped') {
                        $statusClass = 'badge-secondary';
                      }
                    ?>
                    <span class="badge <?= $statusClass ?> badge-sm"><?= $statusLabel ?></span>
                  </td>
                  <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                  <td>
                    <div class="action-buttons">
                      <a href="<?= ROOT ?>admin/dashboard/orders?view_id=<?= $order['id'] ?>" class="action-btn view" title="View Details" style="text-decoration: none;">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7">
                    <!-- Empty State -->
                    <div class="empty-state" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; color: #888;">
                      <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                      <p style="font-size: 16px; font-weight: 500;">No orders found</p>
                      <?php if (!empty($searchQuery) || (!empty($currentStatus) && $currentStatus !== 'all')): ?>
                        <p style="font-size: 14px; margin-top: 8px;">Try adjusting your search or filter criteria.</p>
                      <?php endif; ?>
                    </div>
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