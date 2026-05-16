<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics - Vendora Admin</title>
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
      <div class="page-header">
        <div class="page-header-left">
          <h2 class="font-semibold">Analytics</h2>
          <p class="text-muted">Platform-wide performance insights and reports.</p>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="charts-row">
        <!-- Sales by Category Chart -->
        <div class="chart-card">
          <h3 class="chart-title">Sales by Category</h3>
          <div class="chart-container">
            <canvas id="salesByCategoryChart"></canvas>
          </div>
        </div>

        <!-- Top Selling Stores Chart -->
        <div class="chart-card">
          <h3 class="chart-title">Top Selling Stores</h3>
          <div class="chart-container">
            <canvas id="topSellingStoresChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="charts-row">
        <!-- New Seller Signups Chart -->
        <div class="chart-card">
          <h3 class="chart-title">New Seller Signups</h3>
          <div class="chart-container">
            <canvas id="sellerSignupsChart"></canvas>
          </div>
        </div>

        <!-- Online vs Offline Orders Chart -->
        <div class="chart-card">
          <h3 class="chart-title">Online vs Offline Orders</h3>
          <div class="chart-container">
            <canvas id="orderMethodChart"></canvas>
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
    // Data injected from PHP
    const analyticsData = {
        salesByCategory: <?= json_encode($salesByCategory) ?>,
        topSellingStores: <?= json_encode($topSellingStores) ?>,
        sellerSignups: <?= json_encode($sellerSignups) ?>,
        monthlyOrders: <?= json_encode($monthlyOrders) ?>
    };

    // Helper to get labels and data
    const getLabelsData = (arr, labelKey, dataKey) => ({
        labels: arr.map(item => item[labelKey]),
        data: arr.map(item => item[dataKey])
    });

    // Sales by Category - Pie Chart
    const salesByCategoryCtx = document.getElementById('salesByCategoryChart');
    if (salesByCategoryCtx) {
      const data = getLabelsData(analyticsData.salesByCategory, 'category_name', 'revenue');
      new Chart(salesByCategoryCtx, {
        type: 'pie',
        data: {
          labels: data.labels,
          datasets: [{
            data: data.data,
            backgroundColor: [
              'rgba(99, 102, 241, 0.8)',
              'rgba(34, 197, 94, 0.8)',
              'rgba(251, 191, 36, 0.8)',
              'rgba(239, 68, 68, 0.8)',
              'rgba(168, 85, 247, 0.8)',
              'rgba(236, 72, 153, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 15,
                usePointStyle: true
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.label || '';
                  if (label) {
                    label += ': ';
                  }
                  label += '$' + parseFloat(context.parsed).toLocaleString();
                  return label;
                }
              }
            }
          }
        }
      });
    }

    // Top Selling Stores - Horizontal Bar Chart
    const topSellingStoresCtx = document.getElementById('topSellingStoresChart');
    if (topSellingStoresCtx) {
      const data = getLabelsData(analyticsData.topSellingStores, 'store_name', 'revenue');
      new Chart(topSellingStoresCtx, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Sales',
            data: data.data,
            backgroundColor: 'rgba(99, 102, 241, 0.8)',
            borderRadius: 6
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return '$' + context.parsed.x.toLocaleString();
                }
              }
            }
          },
          scales: {
            x: {
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
    }

    // New Seller Signups - Line Chart
    const sellerSignupsCtx = document.getElementById('sellerSignupsChart');
    if (sellerSignupsCtx) {
      const data = getLabelsData(analyticsData.sellerSignups, 'month', 'count');
      new Chart(sellerSignupsCtx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'New Signups',
            data: data.data,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6
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
                stepSize: 1,
                precision: 0
              }
            }
          }
        }
      });
    }

    // Orders Trend - Bar Chart
    const ordersTrendCtx = document.getElementById('orderMethodChart'); // Re-purposing this container for 'Monthly Orders'
    if (ordersTrendCtx) {
      const data = getLabelsData(analyticsData.monthlyOrders, 'month', 'count');
      new Chart(ordersTrendCtx, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [
            {
              label: 'Orders',
              data: data.data,
              backgroundColor: 'rgba(99, 102, 241, 0.8)',
              borderRadius: 4
            }
          ]
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
                precision: 0
              }
            }
          }
        }
      });
    }
  </script>
</body>

</html>