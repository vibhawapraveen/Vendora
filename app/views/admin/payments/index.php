<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments & Revenue - Admin Dashboard</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/payments.css">
  <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
  <script src="<?= ROOT ?>assets/chartjs/chart.umd.min.js"></script>

</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/admin-sidebar.php' ?>

    <main class="content">
      <div class="page-header">
        <div class="page-header-left">
          <h2 class="font-semibold">Payments & Revenue</h2>
          <p class="text-muted">Track platform fees and transaction history.</p>
        </div>
      </div>

      <!-- Revenue Cards -->
      <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
          <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 8px;">Monthly Revenue (Platform Fee)</div>
          <div style="font-size: 1.5rem; font-weight: 700; color: #0f172a;">$<?= number_format($monthlyRevenue, 2) ?></div>
          <div style="color: #64748b; font-size: 0.75rem; margin-top: 4px;"><?= $monthsList[$selectedMonth] ?> <?= $selectedYear ?></div>
        </div>
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
          <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 8px;">Today's Revenue</div>
          <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">$<?= number_format($todayRevenue, 2) ?></div>
          <div style="color: #64748b; font-size: 0.75rem; margin-top: 4px;"><?= date('M d, Y') ?></div>
        </div>
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
          <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 8px;">Total Transactions</div>
          <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;"><?= number_format($totalTransactions) ?></div>
          <div style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">For filtered criteria</div>
        </div>
      </div>

      <!-- Graph Section -->
      <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
          <h3 style="font-size: 1.125rem; font-weight: 600;">Daily Revenue Growth</h3>
          <form method="GET" id="monthFilterForm" style="display: flex; gap: 10px;">
            <select name="month" class="input" style="padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px;" onchange="this.form.submit()">
              <?php foreach ($monthsList as $num => $name): ?>
                <option value="<?= $num ?>" <?= $num == $selectedMonth ? 'selected' : '' ?>><?= $name ?></option>
              <?php endforeach; ?>
            </select>
            <select name="year" class="input" style="padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px;" onchange="this.form.submit()">
              <?php for ($y = date('Y'); $y >= 2024; $y--): ?>
                <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </form>
        </div>
        <div style="height: 300px;">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>

      <!-- Transaction Table -->
      <div class="table-container" style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid #f1f5f9;">
          <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 15px;">Transaction History</h3>
          
          <form method="GET" class="filters-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
             <!-- Keep graph filters -->
             <input type="hidden" name="month" value="<?= $selectedMonth ?>">
             <input type="hidden" name="year" value="<?= $selectedYear ?>">

             <div class="filter-group">
               <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 4px;">Status</label>
               <select name="status" class="input" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                 <option value="">All Status</option>
                 <option value="paid" <?= $filters['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                 <option value="pending" <?= $filters['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                 <option value="failed" <?= $filters['status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                 <option value="refunded" <?= $filters['status'] == 'refunded' ? 'selected' : '' ?>>Refunded</option>
               </select>
             </div>
             <div class="filter-group">
               <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 4px;">Method</label>
               <select name="method" class="input" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
                 <option value="">All Methods</option>
                 <option value="stripe" <?= $filters['method'] == 'stripe' ? 'selected' : '' ?>>Stripe</option>
                 <option value="cash" <?= $filters['method'] == 'cash' ? 'selected' : '' ?>>Cash</option>
               </select>
             </div>
             <div class="filter-group">
               <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 4px;">From Date</label>
               <input type="date" name="from_date" value="<?= $filters['from_date'] ?>" class="input" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
             </div>
             <div class="filter-group">
               <label style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 4px;">To Date</label>
               <input type="date" name="to_date" value="<?= $filters['to_date'] ?>" class="input" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px;">
             </div>
             <div class="filter-group" style="display: flex; align-items: flex-end;">
               <button type="submit" class="btn btn-primary" style="width: 100%; padding: 9px;">Filter</button>
             </div>
          </form>
        </div>

        <div class="table-wrapper" style="overflow-x: auto;">
          <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
              <tr style="text-align: left; background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                <th style="padding: 12px 20px; font-weight: 600; color: #64748b;">Date</th>
                <th style="padding: 12px 20px; font-weight: 600; color: #64748b;">Payment #</th>
                <th style="padding: 12px 20px; font-weight: 600; color: #64748b;">Store</th>
                <th style="padding: 12px 20px; font-weight: 600; color: #64748b;">Customer</th>
                <th style="padding: 12px 20px; font-weight: 600; color: #64748b;">Total Amount</th>
                <th style="padding: 12px 20px; font-weight: 600; color: #64748b;">Platform Fee</th>
                <th style="padding: 12px 20px; font-weight: 600; color: #64748b;">Method</th>
                <th style="padding: 12px 20px; font-weight: 600; color: #64748b;">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $tx): ?>
                  <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                    <td style="padding: 12px 20px; font-size: 0.875rem;"><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                    <td style="padding: 12px 20px; font-size: 0.875rem; font-weight: 500; font-family: monospace;"><?= $tx['payment_number'] ?></td>
                    <td style="padding: 12px 20px; font-size: 0.875rem;"><?= htmlspecialchars($tx['store_name']) ?></td>
                    <td style="padding: 12px 20px; font-size: 0.875rem;">
                       <div style="font-weight: 500;"><?= htmlspecialchars($tx['customer_name']) ?></div>
                       <!-- <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($tx['customer_email']) ?></div> -->
                    </td>
                    <td style="padding: 12px 20px; font-size: 0.875rem;">$<?= number_format($tx['amount'], 2) ?></td>
                    <td style="padding: 12px 20px; font-size: 0.875rem; font-weight: 600; color: #0f172a;">$<?= number_format($tx['platform_fee'], 2) ?></td>
                    <td style="padding: 12px 20px; font-size: 0.875rem;">
                       <span style="display: inline-flex; align-items: center; gap: 4px;">
                          <i class="fab fa-<?= $tx['payment_method'] == 'stripe' ? 'stripe' : 'cc-visa' ?>" style="color: <?= $tx['payment_method'] == 'stripe' ? '#635bff' : '#64748b' ?>;"></i>
                          <?= ucfirst($tx['payment_method']) ?>
                       </span>
                    </td>
                    <td style="padding: 12px 20px; font-size: 0.875rem;">
                       <?php
                          $statusColor = [
                            'paid' => '#dcfce7', 'pending' => '#fef9c3', 'failed' => '#fee2e2', 'refunded' => '#f1f5f9'
                          ];
                          $textColor = [
                            'paid' => '#166534', 'pending' => '#854d0e', 'failed' => '#991b1b', 'refunded' => '#475569'
                          ];
                       ?>
                       <span style="display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: <?= $statusColor[$tx['status']] ?>; color: <?= $textColor[$tx['status']] ?>;">
                          <?= ucfirst($tx['status']) ?>
                       </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" style="padding: 40px; text-align: center; color: #94a3b8;">No transactions found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9;">
            <div style="font-size: 0.875rem; color: #64748b;">
              Showing page <?= $currentPage ?> of <?= $totalPages ?>
            </div>
            <div style="display: flex; gap: 8px;">
               <?php if ($currentPage > 1): ?>
                 <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" class="btn-pagination" style="padding: 5px 12px; border: 1px solid #e2e8f0; border-radius: 4px; text-decoration: none; color: #0f172a;">Previous</a>
               <?php endif; ?>
               
               <?php if ($currentPage < $totalPages): ?>
                 <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>" class="btn-pagination" style="padding: 5px 12px; border: 1px solid #e2e8f0; border-radius: 4px; text-decoration: none; color: #0f172a;">Next</a>
               <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>

  <script>
    // --- Chart Data Processing ---
    const dailyData = <?= json_encode($dailyRevenueData) ?>;
    const daysInMonth = new Date(<?= $selectedYear ?>, <?= $selectedMonth ?>, 0).getDate();
    
    // Create array for all days in month initialized to 0
    const labels = [];
    const values = [];
    for (let i = 1; i <= daysInMonth; i++) {
      labels.push(i);
      
      // Find if we have data for this day
      const dataPoint = dailyData.find(d => parseInt(d.day) === i);
      values.push(dataPoint ? parseFloat(dataPoint.revenue) : 0);
    }

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Platform Fee Revenue ($)',
          data: values,
          borderColor: '#3b82f6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: '#3b82f6',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            callbacks: {
              label: function(context) {
                return '$' + context.parsed.y.toFixed(2);
              },
              title: function(tooltipItems) {
                return '<?= $monthsList[$selectedMonth] ?> ' + tooltipItems[0].label + ', <?= $selectedYear ?>';
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            },
            title: {
              display: true,
              text: 'Day of Month',
              font: {
                size: 10
              }
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '$' + value;
              }
            }
          }
        }
      }
    });

    // Remove underlines globally (matching user preference)
    const style = document.createElement('style');
    style.innerHTML = `
      a, button { text-decoration: none !important; }
      .sidebar-link { text-decoration: none !important; }
      .btn-pagination:hover { background: #f8fafc; }
    `;
    document.head.appendChild(style);
  </script>
</body>

</html>