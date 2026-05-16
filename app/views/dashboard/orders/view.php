<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Order - <?= htmlspecialchars($order['order_number']) ?></title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">

  <style>
    .order-header {
      background: linear-gradient(135deg, var(--primary) 0%, #764ba2 100%);
      color: var(--primary-foreground);
      border-radius: var(--radius);
    }

    .timeline-item {
      position: relative;
      padding-left: 2rem;
    }

    .timeline-item:before {
      content: '';
      position: absolute;
      left: 0.5rem;
      top: 0.5rem;
      width: 0.75rem;
      height: 0.75rem;
      background: var(--primary);
      border-radius: 50%;
      transform: translateX(-50%);
    }

    .timeline-item:after {
      content: '';
      position: absolute;
      left: 0.5rem;
      top: 1.25rem;
      width: 2px;
      height: calc(100% - 1rem);
      background: var(--border);
      transform: translateX(-50%);
    }

    .timeline-item:last-child:after {
      display: none;
    }

    .item-border {
      border-bottom: 1px solid var(--border);
    }

    .item-border:last-child {
      border-bottom: none;
    }

    /* Remove underlines from button links */
    .btn {
      text-decoration: none !important;
    }

    .btn:hover {
      text-decoration: none !important;
    }
  </style>
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>

    <main class="content">
      <!-- Back Navigation -->
      <div class="mb-6">
        <a href="<?= ROOT ?>dashboard/orders/all" class="btn btn-outline">
          <i class="fa-solid fa-arrow-left mr-2"></i>
          Back to Orders
        </a>
      </div>

      <!-- Order Header -->
      <div class="order-header p-8 mb-8">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($order['order_number']) ?></h1>
            <p class="text-lg">
              Order placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
            </p>
          </div>
          <div class="text-right">
            <?php
            $statusClass = '';
            $statusIcon = '';
            switch ($order['status']) {
              case 'pending':
                $statusClass = 'badge-default';
                $statusIcon = 'fa-clock';
                break;
              case 'shipped':
                $statusClass = 'badge-primary';
                $statusIcon = 'fa-truck';
                break;
              case 'delivered':
                $statusClass = 'badge-success';
                $statusIcon = 'fa-check-circle';
                break;
              case 'cancelled':
                $statusClass = 'badge-destructive';
                $statusIcon = 'fa-times-circle';
                break;
            }
            ?>
            <div class="badge badge-lg <?= $statusClass ?>">
              <i class="fa-solid <?= $statusIcon ?>"></i>
              <?= ucfirst($order['status']) ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column - Order Details -->
        <div class="lg:col-span-2">

          <!-- Order Items -->
          <div class="card">
            <h2 class="card-subtitle mb-4 flex items-center gap-2">
              <i class="fa-solid fa-box text-lg"></i>
              Order Items
            </h2>

            <div>
              <?php foreach ($orderItems as $item): ?>
                <div class="item-border py-4">
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <h3 class="font-medium text-lg"><?= htmlspecialchars($item['product_name']) ?></h3>
                      <?php if ($item['variant_description']): ?>
                        <p class="text-sm text-muted mt-1"><?= htmlspecialchars($item['variant_description']) ?></p>
                      <?php endif; ?>
                      <div class="flex items-center gap-4 mt-2 text-sm text-muted">
                        <span>Quantity: <strong><?= $item['quantity'] ?></strong></span>
                        <span>Unit Price: <strong>$<?= number_format($item['unit_price'], 2) ?></strong></span>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-lg font-semibold">
                        $<?= number_format($item['unit_price'] * $item['quantity'], 2) ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>

              <!-- Order Total -->
              <div class="pt-4 mt-4">
                <div class="flex justify-between items-center">
                  <div class="text-xl font-semibold">Total Amount</div>
                  <div class="text-2xl font-bold" style="color: var(--primary)">
                    $<?= number_format($order['total_amount'], 2) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Customer & Shipping Info -->
        <div class="grid grid-cols-3 gap-5">

          <!-- Customer Information -->
          <div class="card mb-6">
            <h2 class="card-subtitle mb-4 flex items-center gap-2">
              <i class="fa-solid fa-user text-lg"></i>
              Customer Information
            </h2>

            <div>
              <div class="mb-3">
                <label class="text-sm font-medium text-muted">Name</label>
                <div class="text-lg"><?= htmlspecialchars($customer['name']) ?></div>
              </div>

              <!-- <div class="mb-3">
                <label class="text-sm font-medium text-muted">Email</label>
                <div class="text-lg"><?= htmlspecialchars($customer['email']) ?></div>
              </div> -->

              <div class="mb-3">
                <label class="text-sm font-medium text-muted">Customer Since</label>
                <div class="text-lg"><?= date('F Y', strtotime($customer['created_at'])) ?></div>
              </div>
            </div>
          </div>

          <!-- Shipping Information -->
          <div class="card mb-6 col-span-2">
            <h2 class="card-subtitle mb-4 flex items-center gap-2">
              <i class="fa-solid fa-truck text-lg"></i>
              Shipping Address
            </h2>

            <div>
              <div class="text-lg"><?= htmlspecialchars($order['address_line1']) ?></div>
              <?php if ($order['address_line2']): ?>
                <div class="text-lg"><?= htmlspecialchars($order['address_line2']) ?></div>
              <?php endif; ?>
              <div class="text-lg"><?= htmlspecialchars($order['city']) ?></div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="card">
            <h2 class="card-subtitle mb-4 flex items-center gap-2">
              <i class="fa-solid fa-receipt text-lg"></i>
              Order Summary
            </h2>

            <div>
              <div class="flex justify-between mb-3">
                <span class="text-muted">Order ID</span>
                <span class="font-medium"><?= htmlspecialchars($order['order_number']) ?></span>
              </div>

              <div class="flex justify-between mb-3">
                <span class="text-muted">Items</span>
                <span class="font-medium"><?= count($orderItems) ?> item(s)</span>
              </div>

              <div class="flex justify-between mb-3">
                <span class="text-muted">Status</span>
                <span class="font-medium"><?= ucfirst($order['status']) ?></span>
              </div>

              <div class="flex justify-between pt-3" style="border-top: 1px solid var(--border)">
                <span class="text-lg font-semibold">Total</span>
                <span class="text-lg font-bold" style="color: var(--primary)">$<?= number_format($order['total_amount'], 2) ?></span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="card">
            <h2 class="card-subtitle mb-4">Actions</h2>
            <div class="flex flex-col gap-3">
              <a href="<?= ROOT ?>dashboard/orders/all?edit=<?= $order['id'] ?>" class="btn btn-primary">
                <i class="fa-solid fa-edit mr-2"></i>
                Edit Order
              </a>

              <button class="btn btn-secondary" onclick="window.print()">
                <i class="fa-solid fa-print mr-2"></i>
                Print Order
              </button>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>