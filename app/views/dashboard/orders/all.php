<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Orders - Vendora</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/all.css">
  <style>
    .search-container {
      position: relative;
      flex: 1;
      max-width: 300px;
    }
    
    .search-input {
      width: 100%;
      padding: 8px 12px 8px 35px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 14px;
      outline: none;
    }
    
    .search-input:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .search-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #6b7280;
      font-size: 14px;
    }
    
    .filter-select {
      padding: 8px 12px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 14px;
      outline: none;
      background: white;
    }
    
    .date-input {
      padding: 8px 12px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 14px;
      outline: none;
    }
    
    .status-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 500;
      text-transform: capitalize;
    }
    
    .status-pending {
      background-color: #fef3c7;
      color: #f59e0b;
    }
    
    .status-shipped {
      background-color: #dbeafe;
      color: #3b82f6;
    }
    
    .status-delivered {
      background-color: #d1fae5;
      color: #10b981;
    }
    
    .status-cancelled {
      background-color: #fee2e2;
      color: #ef4444;
    }
    
    .customer-info {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .customer-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background-color: #e5e7eb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      color: #6b7280;
      font-size: 12px;
    }
    
    .customer-details h4 {
      margin: 0;
      font-weight: 500;
      color: #111827;
    }
    
    .customer-details p {
      margin: 0;
      font-size: 12px;
      color: #6b7280;
    }
  </style>
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>

    <main class="content">
      <!-- Alert Messages -->
      <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'order_updated'): ?>
          <div class="alert alert-success" style="margin-bottom: 1rem">
            <div class="alert-icon">✅</div>
            <div class="alert-content">
              <div class="alert-title">Order Updated</div>
              <div class="alert-description">
                The order has been successfully updated.
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error" style="margin-bottom: 1rem">
          <div class="alert-icon">❌</div>
          <div class="alert-content">
            <div class="alert-title">Error</div>
            <div class="alert-description">
              <?php
              switch ($_GET['error']) {
                case 'update_failed':
                  echo 'Failed to update the order. Please try again.';
                  break;
                case 'order_not_found':
                  echo 'Order not found or access denied.';
                  break;
                case 'invalid_data':
                  echo 'Invalid order data provided.';
                  break;
                default:
                  echo 'An unexpected error occurred.';
              }
              ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Order Dashboard Header -->
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold">All Orders</h2>
          <p class="text-muted text-sm">Manage and track all customer orders</p>
        </div>
        <div class="flex gap-2">
          <!-- Export Button -->
          <div class="btn btn-secondary" onclick="window.open('?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>', '_blank')" style="cursor: pointer;">
            <i class="fa-solid fa-download pr-2"></i> Export Orders
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="card mt-5 gap-2 p-3">
        <div class="card-header mb-3">
          <div class="card-subtitle">Filter Orders</div>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
          <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; width: 100%;">
            <!-- Search Bar -->
            <div class="card flex items-center px-2 py-1 searchBar">
              <i class="fa-solid fa-magnifying-glass mr-2 text-gray-500 text-sm"></i>
              <input type="text" name="search" placeholder="Search by order ID, customer..." class="outline-none text-sm border-0 w-full" value="<?= htmlspecialchars($filters['search']) ?>">
            </div>

            <!-- Status Dropdown -->
            <div class="card flex items-center px-2 py-1 searchBar">
              <i class="fa-solid fa-tags mr-2 text-purple-600 text-sm"></i>
              <select name="status" class="outline-none text-sm border-0 w-full">
                <option value="">All Statuses</option>
                <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="shipped" <?= $filters['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="delivered" <?= $filters['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="cancelled" <?= $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
              </select>
            </div>

            <!-- From Date -->
            <div class="card flex items-center px-2 py-1 searchBar">
              <i class="fa-solid fa-calendar mr-2 text-sm"></i>
              <input type="date" name="from_date" class="outline-none text-sm border-0 w-full" value="<?= htmlspecialchars($filters['from_date']) ?>">
            </div>

            <!-- To Date -->
            <div class="card flex items-center px-2 py-1 searchBar">
              <i class="fa-solid fa-calendar mr-2 text-sm"></i>
              <input type="date" name="to_date" class="outline-none text-sm border-0 w-full" value="<?= htmlspecialchars($filters['to_date']) ?>">
            </div>

            <!-- Filter Buttons -->
            <div class="btn btn-secondary">
              <button type="submit" style="background: none; border: none; display: flex; align-items: center;">
                <i class="fa-solid fa-filter pr-3"></i>Apply Filters
              </button>
            </div>
            
            <div class="btn btn-secondary">
              <a href="<?= ROOT ?>dashboard/orders/all" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                <i class="fa-solid fa-times pr-3"></i>Clear
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="card mt-5 gap-3 p-0">
        <table class="table w-full text-sm mb-0 rounded-lg">
          <thead>
            <tr class="text-left">
              <th class="px-6 py-3">Order ID</th>
              <th class="px-6 py-3">Customer</th>
              <th class="px-6 py-3">Date</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Total</th>
              <th class="px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($orders)): ?>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td class="px-6 py-4">
                    <strong style="color: #111827;"><?= htmlspecialchars($order['order_number']) ?></strong>
                  </td>
                  <td class="px-6 py-4">
                    <div class="customer-info">
                      <div class="customer-avatar">
                        <?= strtoupper(substr($order['customer_name'], 0, 1)) ?>
                      </div>
                      <div class="customer-details">
                        <h4><?= htmlspecialchars($order['customer_name']) ?></h4>
                        <!-- <p><?= htmlspecialchars($order['customer_email']) ?></p> -->
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <?= date('M d, Y', strtotime($order['created_at'])) ?>
                  </td>
                  <td class="px-6 py-4">
                    <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                      <?= ucfirst(htmlspecialchars($order['status'])) ?>
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <strong>$<?= number_format($order['total_amount'], 2) ?></strong>
                  </td>
                  <td class="px-6 py-4">
                    <button onclick="viewOrder('<?= $order['id'] ?>')" class="action-btn view-btn mr-3" title="View Order">
                      <i class="fa-solid fa-eye"></i>
                    </button>
                    <button onclick="editOrder('<?= $order['id'] ?>')" class="action-btn edit-btn mr-3" title="Edit Order">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button onclick="printOrder('<?= $order['id'] ?>')" class="action-btn print-btn" title="Print Invoice">
                      <i class="fa-solid fa-print"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  No orders found. Try adjusting your filters or check back later.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- Table Footer with Pagination -->
        <div class="tableFooter">
          <div class="text-sm text-gray-500">
            <?php
            $start = ($currentPage - 1) * $limit + 1;
            $end = min($currentPage * $limit, $totalOrders);
            ?>
            Showing <?= $start ?> to <?= $end ?> of <?= $totalOrders ?> results
          </div>
          <div class="flex items-center gap-2 text-sm">
            <?php if ($currentPage > 1): ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" class="pageBtn" style="text-decoration: none;">Previous</a>
            <?php endif; ?>

            <?php
            // Calculate which page numbers to show
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);

            // Show first page if we're not starting from 1
            if ($startPage > 1): ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="pageBtn" style="text-decoration: none;">1</a>
              <?php if ($startPage > 2): ?>
                <span class="pageBtn">...</span>
              <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
              <?php if ($i == $currentPage): ?>
                <span class="currentPage"><?= $i ?></span>
              <?php else: ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="pageBtn" style="text-decoration: none;"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>

            <?php
            // Show last page if we're not ending at the last page
            if ($endPage < $totalPages): ?>
              <?php if ($endPage < $totalPages - 1): ?>
                <span class="pageBtn">...</span>
              <?php endif; ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>" class="pageBtn" style="text-decoration: none;"><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>" class="pageBtn" style="text-decoration: none;">Next</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Edit Order Modal -->
  <div class="modal-overlay" id="edit-order-modal">
    <div class="modal">
      <div class="modal-header">Edit Order</div>
      <div class="modal-body">
        <form id="edit-order-form" action="<?= ROOT ?>dashboard/orders/edit" method="POST">
          <input type="hidden" id="edit-order-id" name="order_id" value="">

          <div style="margin-bottom: 1rem;">
            <label for="edit-order-number" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Order Number</label>
            <input type="text" id="edit-order-number" name="order_number" class="input" readonly style="background-color: #f3f4f6;">
          </div>

          <div style="margin-bottom: 1rem;">
            <label for="edit-customer-name" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Customer</label>
            <input type="text" id="edit-customer-name" name="customer_name" class="input" readonly style="background-color: #f3f4f6;">
          </div>

          <div style="margin-bottom: 1rem;">
            <label for="edit-status" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Status *</label>
            <select id="edit-status" name="status" class="input" required>
              <option value="pending">Pending</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div style="margin-bottom: 1rem;">
            <label for="edit-total-amount" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Total Amount ($) *</label>
            <input type="number" id="edit-total-amount" name="total_amount" class="input" step="0.01" min="0" placeholder="0.00" required>
          </div>

          <div style="margin-bottom: 1rem;">
            <label for="edit-address-line1" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Address Line 1 *</label>
            <input type="text" id="edit-address-line1" name="address_line1" class="input" placeholder="Enter street address" required>
          </div>

          <div style="margin-bottom: 1rem;">
            <label for="edit-address-line2" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Address Line 2</label>
            <input type="text" id="edit-address-line2" name="address_line2" class="input" placeholder="Apartment, suite, etc. (optional)">
          </div>

          <div style="margin-bottom: 1rem;">
            <label for="edit-city" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">City *</label>
            <input type="text" id="edit-city" name="city" class="input" placeholder="Enter city" required>
          </div>

          <div id="edit-loading" style="display: none; text-align: center; padding: 1rem;">
            <span>Loading order data...</span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('edit-order-modal')">
          Cancel
        </button>
        <button type="submit" form="edit-order-form" class="btn btn-primary btn-sm">
          Update Order
        </button>
      </div>
    </div>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
  <script src="<?= ROOT ?>assets/js/components/modal.js"></script>
  
  <script>
    // Pass PHP variables to JavaScript
    window.APP_CONFIG = {
      ROOT: '<?= ROOT ?>',
      BASE_URL: '<?= ROOT ?>dashboard/orders/'
    };

    // Order action functions
    function viewOrder(orderId) {
      console.log('View order:', orderId);
      // TODO: Implement order view functionality
    }
    
    function editOrder(orderId) {
      console.log('Edit order:', orderId);
      
      // Show loading state
      document.getElementById('edit-loading').style.display = 'block';
      
      // Open the modal
      openModal('edit-order-modal');
      
      // Fetch order data using the same controller with AJAX parameter
      const fetchUrl = `${window.APP_CONFIG.ROOT}dashboard/orders/all?ajax=get_order&order_id=${orderId}`;
      console.log('Fetching from:', fetchUrl);
      
      fetch(fetchUrl)
        .then(response => {
          console.log('Response status:', response.status);
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          if (data.success) {
            const order = data.order;
            
            // Populate form fields
            document.getElementById('edit-order-id').value = order.id;
            document.getElementById('edit-order-number').value = order.order_number;
            document.getElementById('edit-customer-name').value = order.customer_name;
            document.getElementById('edit-status').value = order.status;
            document.getElementById('edit-total-amount').value = order.total_amount;
            document.getElementById('edit-address-line1').value = order.address_line1;
            document.getElementById('edit-address-line2').value = order.address_line2 || '';
            document.getElementById('edit-city').value = order.city;
            
            // Hide loading state
            document.getElementById('edit-loading').style.display = 'none';
          } else {
            console.error('API returned error:', data.message);
            alert('Error loading order data: ' + (data.message || 'Unknown error'));
            closeModal('edit-order-modal');
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          alert('Error loading order data. Please try again.');
          closeModal('edit-order-modal');
        });
    }

    function viewOrder(orderId) {
      console.log('View order:', orderId);
      // Navigate to View Order page
      window.location.href = `${window.APP_CONFIG.ROOT}dashboard/orders/view?id=${orderId}`;
    }
    
    function printOrder(orderId) {
      console.log('Print order:', orderId);
      // Open the order view page in a new window for printing
      const printWindow = window.open(`${window.APP_CONFIG.ROOT}dashboard/orders/view?id=${orderId}`, '_blank');
      
      // Wait for the page to load, then trigger print
      printWindow.onload = function() {
        setTimeout(function() {
          printWindow.print();
        }, 500); // Small delay to ensure page is fully rendered
      };
    }

    // Handle form submission
    document.getElementById('edit-order-form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      formData.append('ajax', '1'); // Add AJAX flag
      
      fetch(`${window.APP_CONFIG.ROOT}dashboard/orders/edit`, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close modal and reload page to show updated data
          closeModal('edit-order-modal');
          location.reload();
        } else {
          alert('Error updating order: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error updating order. Please try again.');
      });
    });
  </script>
</body>

</html>
