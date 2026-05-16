<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details - Vendora Admin</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/sellers.css">
  <style>
    .order-status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }
    .status-pending { background: #fef9c3; color: #854d0e; }
    .status-shipped { background: #e0f2fe; color: #0369a1; }
    .status-delivered { background: #dcfce7; color: #166534; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
  </style>
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
          <h2 class="font-semibold">Order Details</h2>
          <p class="text-muted">Viewing details for Order #<?= htmlspecialchars($order['order_number']) ?></p>
        </div>
        <div class="page-header-right">
          <a href="<?= ROOT ?>admin/dashboard/orders" class="btn btn-primary" style="text-decoration: none;">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
          </a>
        </div>
      </div>

      <!-- Data Tables (Stacked as requested) -->
      <div style="display: flex; flex-direction: column; gap: 30px;">

        <!-- General Information Table (Top) -->
        <div class="table-container shadow-sm">
          <div class="table-header">
            <h3 style="font-size: 1.1rem; color: #1e293b; margin: 0;">General Information</h3>
          </div>
          <div class="table-wrapper">
            <table class="table">
              <tbody>
                <tr>
                  <th style="text-align: left; background: #f8fafc; width: 30%;">Order Number</th>
                  <td style="font-weight: 600; color: var(--primary);">#<?= htmlspecialchars($order['order_number']) ?></td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Order Date</th>
                  <td><?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Status</th>
                  <td>
                    <span class="order-status-badge status-<?= $order['status'] ?>">
                      <?= ucfirst($order['status'] === 'delivered' ? 'Completed' : $order['status']) ?>
                    </span>
                  </td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Store Name</th>
                  <td><?= htmlspecialchars($order['store_name'] ?? 'N/A') ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Customer Information Table (Second) -->
        <div class="table-container shadow-sm">
          <div class="table-header">
            <h3 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Customer Information</h3>
          </div>
          <div class="table-wrapper">
            <table class="table">
              <tbody>
                <tr>
                  <th style="text-align: left; background: #f8fafc; width: 30%;">Full Name</th>
                  <td><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Email Address</th>
                  <td><?= htmlspecialchars($order['customer_email'] ?? 'N/A') ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Shipping Information (Bottom 1) -->
        <div class="table-container shadow-sm">
          <div class="table-header">
            <h3 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Shipping Address</h3>
          </div>
          <div class="table-wrapper">
            <table class="table">
              <tbody>
                <tr>
                  <th style="text-align: left; background: #f8fafc; width: 30%;">Address Line 1</th>
                  <td><?= htmlspecialchars($order['address_line1']) ?></td>
                </tr>
                <?php if (!empty($order['address_line2'])): ?>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">Address Line 2</th>
                  <td><?= htmlspecialchars($order['address_line2']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                  <th style="text-align: left; background: #f8fafc;">City</th>
                  <td><?= htmlspecialchars($order['city']) ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Order Items (Bottom 2) -->
        <div class="table-container shadow-sm" style="margin-bottom: 30px;">
          <div class="table-header">
            <h3 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Order Items</h3>
          </div>
          <div class="table-wrapper">
            <table class="table">
              <thead>
                <tr style="background: #f8fafc;">
                  <th style="padding: 12px 20px; width: 50%;">Product Name</th>
                  <th style="padding: 12px 20px; text-align: center;">Price</th>
                  <th style="padding: 12px 20px; text-align: center;">Quantity</th>
                  <th style="padding: 12px 20px; text-align: right;">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td style="padding: 15px 20px;">
                      <div style="font-weight: 500; color: #334155;"><?= htmlspecialchars($item['product_name']) ?></div>
                      <?php if (!empty($item['variant_description'])): ?>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px;"><?= htmlspecialchars($item['variant_description']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td style="padding: 15px 20px; text-align: center;">$<?= number_format($item['unit_price'], 2) ?></td>
                    <td style="padding: 15px 20px; text-align: center;"><?= $item['quantity'] ?></td>
                    <td style="padding: 15px 20px; text-align: right; font-weight: 600;">$<?= number_format($item['subtotal'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr style="background: #f8fafc; font-weight: 700;">
                  <td colspan="3" style="padding: 15px 20px; text-align: right; font-size: 1rem;">Grand Total</td>
                  <td style="padding: 15px 20px; text-align: right; font-size: 1.1rem; color: var(--primary);">$<?= number_format($order['total_amount'], 2) ?></td>
                </tr>
              </tfoot>
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
