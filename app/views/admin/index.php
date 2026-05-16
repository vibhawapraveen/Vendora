<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Vendora Admin</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/dashboard.css">
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/admin-sidebar.php' ?>

    <main class="content">
      <!-- Header Section -->
      <div class="dashboard-header">
        <h2 class="font-semibold">Dashboard</h2>
        <p class="text-muted">Welcome back! Here's your platform overview.</p>
      </div>

      <!-- Statistics Grid -->
      <div class="stats-grid">
        <!-- Total Sellers -->
        <div class="card">
          <div class="stat-content">
            <div class="stat-info">
              <span class="stat-label">Total Sellers</span>
              <h3 class="stat-value"><?= number_format($stats['total_sellers'] ?? 0) ?></h3>
            </div>
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
          </div>
        </div>

        <!-- Total Stores -->
        <div class="card">
          <div class="stat-content">
            <div class="stat-info">
              <span class="stat-label">Total Stores</span>
              <h3 class="stat-value"><?= number_format($stats['total_stores'] ?? 0) ?></h3>
            </div>
            <div class="stat-icon">
              <i class="fas fa-store"></i>
            </div>
          </div>
        </div>

        <!-- Total Orders -->
        <div class="card">
          <div class="stat-content">
            <div class="stat-info">
              <span class="stat-label">Total Orders</span>
              <h3 class="stat-value"><?= number_format($stats['total_orders'] ?? 0) ?></h3>
            </div>
            <div class="stat-icon">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
        </div>

        <!-- Total Revenue -->
        <div class="card">
          <div class="stat-content">
            <div class="stat-info">
              <span class="stat-label">Total Revenue</span>
              <h3 class="stat-value">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h3>
            </div>
            <div class="stat-icon">
              <i class="fas fa-dollar-sign"></i>
            </div>
          </div>
        </div>

        <!-- Pending Orders -->
        <div class="card">
          <div class="stat-content">
            <div class="stat-info">
              <span class="stat-label">Pending Orders</span>
              <h3 class="stat-value"><?= number_format($stats['pending_orders'] ?? 0) ?></h3>
            </div>
            <div class="stat-icon">
              <i class="fas fa-clock"></i>
            </div>
          </div>
        </div>

        <!-- Active Products -->
        <div class="card">
          <div class="stat-content">
            <div class="stat-info">
              <span class="stat-label">Active Products</span>
              <h3 class="stat-value"><?= number_format($stats['active_products'] ?? 0) ?></h3>
            </div>
            <div class="stat-icon">
              <i class="fas fa-box"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="charts-row">
        <!-- Revenue Chart -->
        <div class="chart-card">
          <h3 class="chart-title">Revenue Overview</h3>
          <div class="chart-container">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>

        <!-- Orders Chart -->
        <div class="chart-card">
          <h3 class="chart-title">Orders Trend</h3>
          <div class="chart-container">
            <canvas id="ordersChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="charts-row-full">
        <!-- Top Stores Chart -->
        <div class="chart-card">
          <h3 class="chart-title">Top Performing Stores</h3>
          <div class="chart-container">
            <canvas id="topStoresChart"></canvas>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
  <script src="<?= ROOT ?>assets/chartjs/chart.umd.min.js"></script>

  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
  
  <script>
    // Inject graph data
    window.dashboardData = {
        revenue: <?= json_encode($revenueData) ?>,
        orders: <?= json_encode($ordersData) ?>,
        topStores: <?= json_encode($topStores) ?>
    };
  </script>
  <script src="<?= ROOT ?>assets/js/pages/admin/dashboard.js"></script>
</body>

</html>