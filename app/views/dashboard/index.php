<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
  <script src="<?= ROOT ?>assets/chartjs/chart.umd.min.js"></script>

</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>

    <main class="content">
      <h2 class="font-semibold">Dashboard</h2>
      <p class="text-muted">Welcome back! Here's what's happening with your business.</p>

      <div class="grid grid-cols-4 gap-3 mt-5">
        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Products</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['total_products'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-box-archive text-3xl gray"></i>
        </div>

        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Revenue</div>
            <div class="card-content">
              <div class="text-2xl font-bold">$ <?php echo number_format($stats['total_revenue']) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-dollar-sign" style="font-size:25px; color:green"></i>
        </div>

        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Orders</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['total_orders'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-truck text-3xl" style="color:#3b82f6;"></i>
        </div>

        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Active Products</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['active_products'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-check text-3xl text-success"></i>
        </div>

      </div>

      <div class="mt-10 grid grid-cols-3 gap-3">
        <!--Recent Orders -->
        <div class="col-span-2 card">
          <div class="card-header mb-4">
            <div class="card-subtitle">Recent Orders</div>
          </div>
          <div class="card-content">
            <div class="grid gap-3">
              <?php if (!empty($recentOrders)): ?>
                <?php foreach ($recentOrders as $order): ?>
                  <?php
                  $statusClass = 'badge-default';
                  switch ($order['status']) {
                    case 'pending':
                      $statusClass = 'badge-default';
                      break;
                    case 'shipped':
                      $statusClass = 'badge-primary';
                      break;
                    case 'delivered':
                      $statusClass = 'badge-success';
                      break;
                    case 'cancelled':
                      $statusClass = 'badge-destructive';
                      break;
                  }

                  $imageUrl = !empty($order['product_image'])
                    ? ROOT . ltrim($order['product_image'], '/')
                    : '';
                  ?>
                  <a
                    href="<?= ROOT ?>dashboard/orders/view?id=<?= urlencode($order['id']) ?>"
                    class="block rounded-xl border border-border bg-card p-3 transition hover:-translate-y-0.5 hover:shadow-md"
                    style="text-decoration:none;">
                    <div class="flex items-center gap-3">
                      <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-muted flex items-center justify-center">
                        <?php if (!empty($imageUrl)): ?>
                          <img
                            src="<?= htmlspecialchars($imageUrl) ?>"
                            alt="<?= htmlspecialchars($order['product_name'] ?? 'Order item') ?>"
                            class="h-full w-full object-cover">
                        <?php else: ?>
                          <i class="fa-solid fa-box text-xl text-muted"></i>
                        <?php endif; ?>
                      </div>

                      <div class="min-w-0 flex-1">
                        <div class="font-semibold truncate">
                          <?= htmlspecialchars($order['product_name'] ?? 'Order item') ?>
                        </div>
                        <div class="text-sm text-muted">
                          Order <?= htmlspecialchars($order['order_number']) ?>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                          <span class="badge <?= $statusClass ?>">
                            <?= ucfirst($order['status']) ?>
                          </span>
                          <span class="text-sm text-muted">
                            $ <?= number_format((float)$order['total_amount'], 2) ?>
                          </span>
                        </div>
                      </div>

                      <i class="fa-solid fa-chevron-right text-muted"></i>
                    </div>
                  </a>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-sm text-muted">No recent orders yet.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="card">
          <div class="card-header mb-4">
            <div class="card-subtitle">Low Stock Alerts</div>
          </div>
          <div class="card-content">
            <div class="flex flex-col gap-3">
              <?php if (!empty($lowStockAlerts)): ?>
                <?php foreach ($lowStockAlerts as $item): ?>
                  <div class="flex items-start gap-3 p-3 rounded border border-muted hover:bg-muted transition-colors">
                    <div class="mt-1">
                      <i class="fa-solid fa-circle-exclamation text-destructive"></i>
                    </div>
                    <div class="flex-1">
                      <div class="font-semibold text-sm"><?= htmlspecialchars($item['name']) ?></div>
                      <div class="flex items-center gap-2 mt-2">
                        <span class="badge badge-destructive badge">
                          <?= (int)$item['stock_quantity'] ?> unit<?= ((int)$item['stock_quantity'] === 1 ? '' : 's') ?> left
                        </span>
                        <a href="<?= ROOT ?>dashboard/products/<?= urlencode($item['id']) ?>/edit"
                          class="cursor-pointer"
                          style="text-decoration: underline;">
                          Restock
                        </a>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-sm text-muted">No low stock alerts right now.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-10" style="height: 400px;">
        <div class="card-header">
          <div class="card-subtitle">Sales Analytics</div>
        </div>

        <div class="flex gap-5">
          <div class="w-full rounded h-full" style="height: 330px;">
            <canvas id="salesChart"></canvas>
          </div>
          <div class="w-full rounded h-full" style="height: 330px;">
            <canvas id="orderStatusChart"></canvas>
          </div>
        </div>
      </div>

    </main>
  </div>

  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>

  <script>
    const dailyRevenueLabels = <?= json_encode($dailyRevenue['labels'] ?? []) ?>;
    const dailyRevenueValues = <?= json_encode($dailyRevenue['values'] ?? []) ?>;
    const orderStatusCounts = <?= json_encode($orderStatusDistribution ?? ['delivered' => 0, 'shipped' => 0, 'pending' => 0, 'cancelled' => 0]) ?>;

    // Sales Over Time Chart (Line Chart)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: dailyRevenueLabels,
        datasets: [{
          label: 'Sales Revenue ($)',
          data: dailyRevenueValues,
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top',
          },
          title: {
            display: true,
            text: 'Daily Sales Revenue (Last 30 Days)'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '$' + value.toLocaleString();
              }
            }
          }
        }
      }
    });

    // Order Status Chart (Doughnut Chart)
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusChart = new Chart(orderStatusCtx, {
      type: 'doughnut',
      data: {
        labels: ['Delivered', 'Shipped', 'Pending', 'Cancelled'],
        datasets: [{
          data: [
            orderStatusCounts.delivered || 0,
            orderStatusCounts.shipped || 0,
            orderStatusCounts.pending || 0,
            orderStatusCounts.cancelled || 0,
          ],
          backgroundColor: [
            'rgb(34, 197, 94)',
            'rgb(59, 130, 246)',
            'rgb(234, 179, 8)',
            'rgb(239, 68, 68)'
          ],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'bottom',
          },
          title: {
            display: true,
            text: 'Order Status Distribution'
          }
        }
      }
    });
  </script>
</body>

</html>