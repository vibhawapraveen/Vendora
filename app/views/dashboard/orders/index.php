<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
  <script src="<?= ROOT ?>assets/chartjs/chart.umd.min.js"></script>

  
  <style>
    .filters-container {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    
    .filters-container > div:hover {
      background: #ffffff !important;
      border-color: #dee2e6 !important;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transform: translateY(-1px);
    }
    
    /* Action buttons styling */
    .action-btn {
      background: none !important;
      border: none !important;
      padding: 8px !important;
      cursor: pointer;
      transition: all 0.2s ease;
      border-radius: 4px;
    }

    .action-btn:hover {
      background-color: rgba(0, 0, 0, 0.05) !important;
      transform: scale(1.1);
    }

    .action-btn i {
      font-size: 16px;
      transition: color 0.2s ease;
    }

    .action-btn.view-btn {
      color: #6b7280 !important;
    }

    .action-btn.view-btn:hover {
      color: #374151 !important;
    }

    .action-btn.edit-btn {
      color: #3b82f6 !important;
    }

    .action-btn.edit-btn:hover {
      color: #1e40af !important;
    }
    
    /* Custom badge classes for orders */
    .badge-warning {
      background-color: #fef3c7;
      color: #f59e0b;
    }
    
    .badge-primary {
      background-color: #dbeafe;
      color: #3b82f6;
    }
    
    .badge-success {
      background-color: #d1fae5;
      color: #10b981;
    }
    
    .badge-danger {
      background-color: #fee2e2;
      color: #ef4444;
    }
    
    .badge-secondary {
      background-color: #f3f4f6;
      color: #6b7280;
    }
    
    /* When Quick Filters section wraps below (smaller screens) */
    @media (max-width: 1200px) {
      .filters-container {
        display: flex;
        flex-direction: row;
        gap: 12px;
        flex-wrap: wrap;
      }
      
      .filters-container > div {
        flex: 1;
        min-width: 200px;
      }
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
      .filters-container {
        flex-direction: column;
      }
      
      .filters-container > div {
        min-width: auto;
      }
    }
  </style>
  
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>

    <main class="content">
      <h2 class="font-semibold "><i class="fa-solid fa-1x fa-box-archive mr-2"></i> Order Dashboard</h2>
      <!-- Cards Section -->
      <div class="grid grid-cols-4 gap-3 mt-5">
        <!-- Pending Orders -->
        <div class="card flex justify-between items-center" onclick="filterOrders('pending')" style="cursor: pointer;">
          <div class="w-3/4">
            <div class="text-sm text-muted">Pending orders</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= $stats['pending_orders'] ?? 0 ?></div>
            </div>
          </div>
          <i class="fa-solid fa-clock text-3xl" style="color:#f59e0b;"></i>
        </div>

        <!-- Shipped Orders -->
        <div class="card flex justify-between items-center" onclick="filterOrders('shipped')" style="cursor: pointer;">
          <div class="w-3/4">
            <div class="text-sm text-muted">Shipped</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= $stats['shipped_orders'] ?? 0 ?></div>
            </div>
          </div>
          <i class="fa-solid fa-truck text-3xl" style="color:#3b82f6;"></i>
        </div>

        <!-- Delivered Orders -->
        <div class="card flex justify-between items-center" onclick="filterOrders('delivered')" style="cursor: pointer;">
          <div class="w-3/4">
            <div class="text-sm text-muted">Delivered</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= $stats['delivered_orders'] ?? 0 ?></div>
            </div>
          </div>
          <i class="fa-solid fa-check text-3xl" style="color:#10b981;"></i>
        </div>

        <!-- Cancelled Orders -->
        <div class="card flex justify-between items-center" onclick="filterOrders('cancelled')" style="cursor: pointer;">
          <div class="w-3/4">
            <div class="text-sm text-muted">Cancelled</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= $stats['cancelled_orders'] ?? 0 ?></div>
            </div>
          </div>
          <i class="fa-solid fa-xmark text-3xl" style="color:#ef4444;"></i>
        </div>
      </div>

      
  
  <!-- middle section -->
 <div class="grid grid-cols-3" style="gap: 20px; padding: 1px; margin: 0 auto;">
 
  <div class="card mt-5 col-span-2" style="height:420px; min-width:739px; flex: 1; padding: 20px; background: white; border-radius: 8px;">
    <div class = "flex" style="justify-content:space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
      <h1 class="card-subtitle font-semibold" style="margin: 0; font-size: 20px;">Orders Over Time</h1>
      <div style="position: relative; display: inline-block;">
        <select id="timeRangeSelector" onchange="updateChartRange(this.value)" style="padding: 8px 30px 8px 15px; border: 1px solid #ddd; border-radius: 4px; background-color: white; font-size: 14px; appearance: none; -webkit-appearance: none;">
          <option value="7">Last 7 days</option>
          <option value="30" selected>Last 30 days</option>
          <option value="90">Last 90 days</option>
        </select>
      </div>
    </div>
    <div style="height: 310px; position: relative;">
      <canvas id="ordersChart"></canvas>
    </div>
  </div>

  <!-- Right -->
  <div class="card mt-5" style="height:420px; min-width:380px; flex: 0 0 380px; padding: 20px; background: white; border-radius: 8px;">
    <div class = "flex" style="align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom:1px solid #eee;">
      <h1 class="card-subtitle font-semibold" style="margin: 0; font-size: 20px;">Quick Filters</h1>
    </div>
    
    <!-- Responsive filter layout -->
    <div class="filters-container">
      <!-- Filter Items -->
      <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; cursor: pointer; transition: all 0.3s; border: 1px solid #e9ecef;" onclick="filterOrders('all')">
           <div class="flex items-center justify-between">
             <div class="flex items-center gap-3">
               <i class="fa-solid fa-list text-lg" style="color: #6c757d;"></i>
               <span style="font-weight: 500; color: #495057;">All orders</span>
             </div>
             <span style="font-weight: 600; color: #6c757d; background: #e9ecef; padding: 4px 8px; border-radius: 12px; font-size: 12px;"><?= $stats['total_orders'] ?? 0 ?></span>
           </div>
        </div>

        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; cursor: pointer; transition: all 0.3s; border: 1px solid #e9ecef;" onclick="filterTodaysOrders()">
           <div class="flex items-center justify-between">
             <div class="flex items-center gap-3">
               <i class="fa-solid fa-calendar-day text-lg" style="color: #3b82f6;"></i>
               <span style="font-weight: 500; color: #495057;">Today's orders</span>
             </div>
             <span style="font-weight: 600; color: #3b82f6; background: #dbeafe; padding: 4px 8px; border-radius: 12px; font-size: 12px;">0</span>
           </div>
        </div>

        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; cursor: pointer; transition: all 0.3s; border: 1px solid #e9ecef;" onclick="filterHighPriority()">
           <div class="flex items-center justify-between">
             <div class="flex items-center gap-3">
               <i class="fa-solid fa-exclamation-triangle text-lg" style="color: #f59e0b;"></i>
               <span style="font-weight: 500; color: #495057;">High priority</span>
             </div>
             <span style="font-weight: 600; color: #f59e0b; background: #fef3c7; padding: 4px 8px; border-radius: 12px; font-size: 12px;"><?= $stats['pending_orders'] ?? 0 ?></span>
           </div>
        </div>

        <div style="padding: 12px; background: #f8f9fa; border-radius: 8px; cursor: pointer; transition: all 0.3s; border: 1px solid #e9ecef;" onclick="filterOverdue()">
           <div class="flex items-center justify-between">
             <div class="flex items-center gap-3">
               <i class="fa-solid fa-clock-rotate-left text-lg" style="color: #ef4444;"></i>
               <span style="font-weight: 500; color: #495057;">Overdue</span>
             </div>
             <span style="font-weight: 600; color: #ef4444; background: #fee2e2; padding: 4px 8px; border-radius: 12px; font-size: 12px;"><?= $stats['cancelled_orders'] ?? 0 ?></span>
           </div>
        </div>
      
      
    </div>
  </div>
</div>

      <!-- Recent Orders -->
        <div class="card mt-5 gap-3 p-0">
          <h2 class="card-subtitle font-semibold mb-4 px-6 pt-5">Recent Orders</h2>
          <table class="table w-full text-sm mb-10 rounded-lg">
            <thead>
              <tr class="text-left">
                <th class="py-3 " style ="padding-left:6%;">ORDER ID</th>
                <th class="px-6 py-3">CUSTOMER</th>
                <th class="px-6 py-3" style ="padding-left:2%;"> DATE</th>
                <th class="px-6 py-3">AMOUNT</th>
                <th class="px-6 py-3"style ="padding-left:2%;">STATUS</th>
                <th class="px-6 py-3" style ="padding-left:2%;">ACTION</th>

              </tr>
            </thead>
            <tbody>
              <?php if (!empty($recentOrders)): ?>
                <?php foreach ($recentOrders as $order): ?>
                <tr>
                  <td class="px-6 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg"></div>
                    <div>
                      <p class="font-medium"><?= htmlspecialchars($order['order_number']) ?></p>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <i class="fa-regular fa-id-badge" ></i>
                    <span><?= htmlspecialchars($order['customer_name']) ?></span>
                    <!-- <p class="text-muted"><?= htmlspecialchars($order['customer_email']) ?></p> -->
                  </td>
                  <td class="px-6 py-4">
                    <?php 
                      $date = new DateTime($order['created_at']);
                      echo $date->format('M j, Y');
                    ?>
                  </td>
                  <td class="px-6 py-4">$<?= number_format($order['total_amount'], 2) ?></td>
                  <td class="px-6 py-4">
                    <?php
                      $statusClass = '';
                      $statusText = ucfirst($order['status']);
                      switch($order['status']) {
                        case 'pending':
                          $statusClass = 'badge-warning';
                          break;
                        case 'shipped':
                          $statusClass = 'badge-primary';
                          $statusText = 'Shipped';
                          break;
                        case 'delivered':
                          $statusClass = 'badge-success';
                          break;
                        case 'cancelled':
                          $statusClass = 'badge-danger';
                          break;
                        default:
                          $statusClass = 'badge-secondary';
                      }
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      <button class="action-btn view-btn" title="View Order" onclick="viewOrder('<?= $order['id'] ?>')">
                        <i class="fa-solid fa-eye"></i>
                      </button>
                      <button class="action-btn edit-btn" title="Edit Order" onclick="editOrder('<?= $order['id'] ?>')">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="px-6 py-8 text-center text-muted">
                    <i class="fa-solid fa-inbox text-3xl mb-3" style="color: #d1d5db;"></i>
                    <p>No recent orders found</p>
                  </td>
                </tr>
              <?php endif; ?>
             </tbody>
          </table>


        </div>
    </main>
  </div>

  


  <script>
    // Configuration for the app
    window.APP_CONFIG = {
      ROOT: '<?= ROOT ?>'
    };
  </script>
  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
  <script src="<?= ROOT ?>assets/js/components/modal.js"></script>
  
  <script>
    function editOrder(orderId) {
      console.log('Edit order:', orderId);
      // Navigate to All Orders page with edit functionality
      window.location.href = `${window.APP_CONFIG.ROOT}dashboard/orders/all`;
    }

    function viewOrder(orderId) {
      console.log('View order:', orderId);
      // Navigate to View Order page
      window.location.href = `${window.APP_CONFIG.ROOT}dashboard/orders/view?id=${orderId}`;
    }

    // Quick filter functions
    function filterOrders(status) {
      if (status === 'all') {
        window.location.href = `${window.APP_CONFIG.ROOT}dashboard/orders/all`;
      } else {
        window.location.href = `${window.APP_CONFIG.ROOT}dashboard/orders/all?status=${status}`;
      }
    }

    function filterTodaysOrders() {
      const today = new Date().toISOString().split('T')[0];
      window.location.href = `${window.APP_CONFIG.ROOT}dashboard/orders/all?from_date=${today}&to_date=${today}`;
    }

    function filterHighPriority() {
      window.location.href = `${window.APP_CONFIG.ROOT}dashboard/orders/all?status=pending`;
    }

    function filterOverdue() {
      window.location.href = `${window.APP_CONFIG.ROOT}dashboard/orders/all?status=cancelled`;
    }
  </script>

  <script>
    // Live data from database
    const liveData = <?= json_encode($graphData) ?>;
    
    // Function to get last N days as an array of date strings (YYYY-MM-DD)
    function getDateRange(days) {
        const dates = [];
        for (let i = days - 1; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            dates.push(`${year}-${month}-${day}`);
        }
        return dates;
    }

    // Transform live data for Chart.js
    const sampleData = {};
    [7, 30, 90].forEach(range => {
        const fullRange = getDateRange(range);
        const dataMap = {};
        
        // Fill map with zeros initially
        fullRange.forEach(date => dataMap[date] = 0);
        
        // Update with actual counts from database
        if (liveData[range]) {
            liveData[range].forEach(item => {
                if (dataMap.hasOwnProperty(item.date)) {
                    dataMap[item.date] = parseInt(item.count);
                }
            });
        }
        
        sampleData[range] = {
            labels: fullRange.map(date => {
                const parts = date.split('-');
                // Create date in local time to avoid UTC shift
                const d = new Date(parts[0], parts[1] - 1, parts[2]);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }),
            data: fullRange.map(date => dataMap[date])
        };
    });

    let ordersChart = null;

    function initChart(range = 30) {
      const ctx = document.getElementById('ordersChart').getContext('2d');
      const data = sampleData[range];

      if (ordersChart) {
        ordersChart.destroy();
      }

      ordersChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Total Orders',
            data: data.data,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#3b82f6',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 2
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
              backgroundColor: '#1f2937',
              titleColor: '#fff',
              bodyColor: '#fff',
              padding: 12,
              cornerRadius: 8,
              displayColors: false,
              callbacks: {
                label: function(context) {
                  return 'Orders: ' + context.parsed.y;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                precision: 0,
                color: '#6b7280',
                font: {
                  size: 12
                },
                callback: function(value) {
                  if (Math.floor(value) === value) {
                    return value;
                  }
                }
              },
              grid: {
                color: '#f3f4f6',
                drawBorder: false
              }
            },
            x: {
              ticks: {
                color: '#6b7280',
                font: {
                  size: 12
                },
                maxRotation: 0,
                autoSkip: true,
                maxTicksLimit: 8
              },
              grid: {
                display: false,
                drawBorder: false
              }
            }
          },
          interaction: {
            intersect: false,
            mode: 'index'
          }
        }
      });
    }

    function updateChartRange(range) {
      initChart(parseInt(range));
    }

    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', function() {
      initChart(30);
    });
  </script>
</body>

</html>