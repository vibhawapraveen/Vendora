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

    <main class="content ">

      <h2 class="font-semibold">All Customer</h2>
      <p class="text-muted">Welcome back! Here's what's happening with your business.</p>
      <div class=" grid grid-cols-3 gap-5 mt-5">

        <div class="card flex justify-between items-center">
          <div class="w-1/2">
            <div class="text-sm text-muted">Total Customers</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?php echo $totalCustomers['total_customers'] ?></div>
            </div>
          </div>
          <div>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:rgba(59,130,246,.12);">
              <i class="fa-solid fa-users" style="font-size:22px;color:#3b82f6"></i>
            </span>
          </div>
        </div>




        <div class="card flex justify-between items-center">
          <div class="w-1/2">
            <div class="text-sm text-muted">New This Month</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?php echo $newCustomersThisMonth['new_customers'] ?></div>
            </div>
          </div>
          <div>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:rgba(16,185,129,.12);">
              <i class="fa-solid fa-user-plus" style="font-size:22px;color:#10b981"></i>
            </span>
          </div>
        </div>

        <div class="card flex justify-between items-center">
          <div class="w-1/2">
            <div class="text-sm text-muted">Total spent</div>
            <div class="card-content">
              <div class="text-2xl font-bold">$ <?php echo number_format($totalRevenue['total_revenue']) ?></div>
            </div>
          </div>
          <div>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:rgba(245,158,11,.14);">
              <i class="fa-solid fa-dollar-sign" style="font-size:22px;color:#f59e0b"></i>
            </span>
          </div>

        </div>
      </div>


      <section class="grid grid-cols- gap-5" style="margin-top: 20px">

        <div class="card" style="margin-bottom: 1rem">
          <div class="card-header">
            <div class="card-subtitle">Top Customers by Spending</div>
          </div>

          <!-- Two-column layout inside the card: chart (left) + customer info (right) -->
          <div class="grid grid-cols-2 gap-5" style="padding: 1rem;">
            <div class="card-content mt-3" style="height: 330px; padding: 0;">
              <canvas id="topCustomersChart"></canvas>
            </div>

            <div class="card-content" style="padding: 0;">
              <div class="text-sm text-muted" style="margin-bottom: .75rem;">Customer information</div>

              <div class="grid grid-cols-1 gap-3">
                <?php if (!empty($topCustomersBySpending) && is_array($topCustomersBySpending)): ?>
                  <?php foreach ($topCustomersBySpending as $c): ?>
                    <div class="flex justify-between items-center" style="padding: .75rem; border: 1px solid rgba(0,0,0,.08); border-radius: .5rem;">
                      <div>
                        <div class="font-semibold"><?php echo htmlspecialchars($c['customer_name'] ?? 'Unknown'); ?></div>
                        <div class="text-sm text-muted">
                          Orders: <?php echo (int)($c['total_orders'] ?? 0); ?>
                          • Last order: <?php echo !empty($c['last_order_at']) ? date('Y-m-d', strtotime($c['last_order_at'])) : 'Never'; ?>
                        </div>
                      </div>
                      <div class="font-semibold">$<?php echo number_format((float)($c['total_spent'] ?? 0), 2); ?></div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="text-sm text-muted">No customer spending data yet.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </section>

    
      <div class="grid grid-cols-1 gap-5">
          <button class="card flex  justify-content items-center  text-left p-8" style="height: 100px; " onclick="window.location.href='<?= ROOT ?>dashboard/customers/all'">
            <i class="fa-solid fa-list" style="font-size:25px"></i>
            <div class="ml-3 justify-center items-cente ml-5">
              <h3>View All Customers</h3>
              <h4 class="text-muted mt-1 font-normal">Browse complete customer list</h4>
            </div>
          </button>

        </div>
 


    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Quick checks
      console.log('Chart available?', typeof Chart !== 'undefined');

      const topCanvas = document.getElementById('topCustomersChart');
      if (!topCanvas) {
        console.error('Canvas #topCustomersChart not found in DOM');
        return;
      }
      if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded (Chart is undefined). Check network/CSP.');
        return;
      }

      const topCustomersData = <?php echo json_encode($topCustomersBySpending ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      const labels = topCustomersData.map(c => c.customer_name || 'Unknown');
      const data = topCustomersData.map(c => Number(c.total_spent || 0));

      // Chart colors (repeat if > 5)
      const palette = [
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(245, 158, 11, 0.8)',
        'rgba(239, 68, 68, 0.8)',
        'rgba(139, 92, 246, 0.8)',
        'rgba(236, 72, 153, 0.8)',
        'rgba(34, 197, 94, 0.8)'
      ];
      const backgroundColor = labels.map((_, i) => palette[i % palette.length]);

      // Top Customers by Spending Chart
      const topCustomersCtx = topCanvas.getContext('2d');
      new Chart(topCustomersCtx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Total Spent ($)',
            data,
            backgroundColor,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: (value) => '$' + Number(value).toLocaleString()
              }
            }
          }
        }
      });
    });
  </script>
</body>

</html>