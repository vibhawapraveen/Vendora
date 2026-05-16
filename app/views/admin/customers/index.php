<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customers - Vendora Admin</title>
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
          <h2 class="font-semibold">Customers</h2>
          <p class="text-muted">Manage all customers who have placed orders.</p>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <!-- Search and Filter Header -->
        <div class="table-header">
          <form method="GET" action="" class="search-filter-row">
            <div class="search-input-wrapper">
              <input type="text" name="search" class="input" placeholder="Search customers..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <button type="submit" class="btn-filter">
              <i class="fas fa-filter"></i>
              Filter
            </button>
            <?php if (!empty($search)): ?>
            <a href="<?= ROOT ?>admin/dashboard/customers" class="btn btn-secondary" style="text-decoration: none; padding: 8px 16px; margin-left: 10px;">Clear</a>
            <?php endif; ?>
          </form>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Total Orders</th>
                <th>Total Spent</th>
                <th>Last Order</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($customers)): ?>
                <?php foreach ($customers as $customer): ?>
                <tr>
                  <td>
                    <div class="seller-info">
                      <div class="seller-avatar" style="background-color: var(--primary); color: white; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                        <?= strtoupper(substr($customer['customer_name'], 0, 1)) ?>
                      </div>
                      <span><?= htmlspecialchars($customer['customer_name']) ?></span>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($customer['email'] ?? "-") ?></td>
                  <td><?= htmlspecialchars($customer['mobile_number'] ?? "-") ?></td>
                  <td><?= htmlspecialchars($customer['total_orders']) ?></td>
                  <td style="font-weight: 600;">$<?= number_format($customer['total_spent'], 2) ?></td>
                  <td><?= $customer['last_order'] !== 'Never' ? date('M d, Y', strtotime($customer['last_order'])) : 'Never' ?></td>
                  <td>
                    <div class="action-buttons">
                      <a href="<?= ROOT ?>admin/dashboard/customers?view_id=<?= $customer['id'] ?>" class="action-btn view" title="View Details" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" style="text-align: center; padding: 20px;">No customers found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>

          <!-- Empty State (hidden by default) -->
          <div class="empty-state" style="display: none;">
            <i class="fas fa-inbox"></i>
            <p>No customers found</p>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>