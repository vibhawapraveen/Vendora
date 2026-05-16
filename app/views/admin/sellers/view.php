<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seller Details - Vendora Admin</title>
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
      <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="page-header-left">
          <h2 class="font-semibold">Seller Details</h2>
          <p class="text-muted">Viewing details for <?= htmlspecialchars($seller['name']) ?></p>
        </div>
        <div class="page-header-right">
          <a href="<?= ROOT ?>admin/dashboard/sellers" class="btn btn-primary" style="text-decoration: none;">
            <i class="fas fa-arrow-left mr-2"></i> Back to Sellers
          </a>
        </div>
      </div>

      <!-- Data Tables (Stacked) -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Seller Information Table -->
        <div class="table-container">
          <div class="table-header">
            <h3 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Seller Information</h3>
          </div>
          <div class="table-wrapper">
            <table class="table">
              <tbody>
                <tr>
                  <th style="text-align: left; background: #f8fafc; width: 40%;">Profile Picture</th>
                  <td>
                    <?php if (!empty($seller['profile_picture'])): ?>
                      <img src="<?= ROOT . $seller['profile_picture'] ?>" alt="Profile" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 1px solid #e5e7eb;">
                    <?php else: ?>
                      <div style="width: 80px; height: 80px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 2rem; border: 1px solid #e2e8f0;">
                        <i class="fas fa-user"></i>
                      </div>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc; width: 40%;">Seller Name</th>
                  <td><?= htmlspecialchars($seller['name']) ?></td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Email</th>
                  <td><?= htmlspecialchars($seller['email']) ?></td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Phone Number</th>
                  <td><?= htmlspecialchars($seller['mobile_number'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Date Joined</th>
                  <td><?= date('F j, Y', strtotime($seller['created_at'])) ?></td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Store</th>
                  <td><?= $seller['store_name'] ? htmlspecialchars($seller['store_name']) : '<span style="color: #94a3b8; font-style: italic;">No active store found</span>' ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>
