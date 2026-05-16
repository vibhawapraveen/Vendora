<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Customers</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">

  <style>
    /* Scoped modern filter row styles (keep search input styles unchanged) */
    .customers-filters {
      flex-wrap: nowrap;
      align-items: center;
      gap: .75rem;
    }

    .customers-filters__controls {
      display: inline-flex;
      align-items: center;
      gap: .6rem;
      flex: 0 0 auto;
      white-space: nowrap;
    }

    .customers-filters select {
      height: 44px;
      padding: 0 1rem;
      border-radius: 12px;
      border: 1px solid rgba(0, 0, 0, .10);
      background: #fff;
      box-shadow: 0 1px 0 rgba(0, 0, 0, .02);
      font-weight: 600;
      color: rgba(0, 0, 0, .78);
      outline: none;
      transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
      appearance: none;
      -webkit-appearance: none;
      background-image:
        linear-gradient(45deg, transparent 50%, rgba(0, 0, 0, .55) 50%),
        linear-gradient(135deg, rgba(0, 0, 0, .55) 50%, transparent 50%);
      background-position:
        calc(100% - 18px) 18px,
        calc(100% - 13px) 18px;
      background-size: 5px 5px, 5px 5px;
      background-repeat: no-repeat;
      padding-right: 2.2rem;
    }

    .customers-filters select:focus {
      border-color: rgba(156, 0, 240, .55);
      box-shadow: 0 0 0 4px rgba(156, 0, 240, .12);
    }

    .customers-filters__iconBtn {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      border: 1px solid rgba(156, 0, 240, .22);
      background: rgba(156, 0, 240, .06);
      color: #2b2b2b;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: transform .15s ease, box-shadow .15s ease, background .15s ease, border-color .15s ease;
      user-select: none;
      text-decoration: none;
    }

    .customers-filters__iconBtn:hover {
      background: rgba(156, 0, 240, .12);
      border-color: rgba(156, 0, 240, .45);
      box-shadow: 0 10px 18px rgba(0, 0, 0, .08);
      transform: translateY(-1px);
    }

    .customers-filters__iconBtn:active {
      transform: translateY(0);
      box-shadow: none;
    }

    /* Keep single line; allow horizontal scroll on smaller screens */
    .customers-filters {
      overflow-x: auto;
      padding-bottom: .25rem;
      scrollbar-width: thin;
    }

    .customers-filters::-webkit-scrollbar {
      height: 8px;
    }

    .customers-filters::-webkit-scrollbar-thumb {
      background: rgba(0, 0, 0, .15);
      border-radius: 999px;
    }

    /* Search input hover/focus border-line fix (scoped; keeps base input style) */
    .customers-filters .input {
      box-sizing: border-box;
      outline: none;
      box-shadow: none;
    }

    .customers-filters .input:hover {
      border-color: rgba(0, 0, 0, .18);
    }

    .customers-filters .input:focus {
      border-color: var(--ring);
      box-shadow: 0 0 0 2px rgba(156, 0, 240, .35);
    }

    /* Local modern pagination styles (scoped by class names) */
    .pagination {
      display: flex;
      justify-content: center;
      gap: .6rem;
      padding: 1rem 0;
      border-top: 1px solid rgba(0, 0, 0, .08);
    }

    .pagination__btn {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      height: 40px;
      padding: 0 .9rem;
      border-radius: 999px;
      border: 1px solid rgba(156, 0, 240, .25);
      background: rgba(156, 0, 240, .06);
      color: #2b2b2b;
      text-decoration: none;
      font-weight: 600;
      font-size: .9rem;
      transition: transform .15s ease, box-shadow .15s ease, background .15s ease, border-color .15s ease;
      user-select: none;
    }

    .pagination__btn:hover {
      background: rgba(156, 0, 240, .12);
      border-color: rgba(156, 0, 240, .45);
      box-shadow: 0 8px 18px rgba(0, 0, 0, .08);
      transform: translateY(-1px);
    }

    .pagination__btn:active {
      transform: translateY(0);
      box-shadow: none;
    }

    .pagination__status {
      display: inline-flex;
      align-items: center;
      height: 40px;
      padding: 0 1rem;
      border-radius: 999px;
      border: 1px solid rgba(0, 0, 0, .10);
      background: rgba(0, 0, 0, .03);
      color: rgba(0, 0, 0, .7);
      font-weight: 600;
      font-size: .9rem;
    }
  </style>
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>
    <main class="content">
      <h2 class="font-semibold">All Customers</h2>
      <p class="text-muted">Welcome back! Here's what's happening with your business.</p>

      <div class="grid grid-cols-3 gap-5 mt-5">
        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Customers</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= $totalCustomers['total_customers'] ?></div>
            </div>
          </div>
          <div>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:rgba(59,130,246,.12);">
              <i class="fa-solid fa-users" style="font-size:22px;color:#3b82f6"></i>
            </span>
          </div>
        </div>

        <!-- <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Active Customers</div>
            <div class="card-content">
              <div class="text-2xl font-bold">346</div>
            </div>
          </div>
          <div><i class="fa-solid fa-user-check mr-5" style="font-size:25px"></i></div>
        </div> -->

        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">New This Month</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= $newCustomersThisMonth['new_customers'] ?></div>
            </div>
          </div>
          <div>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:rgba(16,185,129,.12);">
              <i class="fa-solid fa-user-plus" style="font-size:22px;color:#10b981"></i>
            </span>
          </div>
        </div>

        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Revenue</div>
            <div class="card-content">
              <div class="text-2xl font-bold">$ <?= number_format($totalRevenue['total_revenue'], 2) ?></div>
            </div>
          </div>
          <div>
            <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:rgba(245,158,11,.14);">
              <i class="fa-solid fa-dollar-sign" style="font-size:22px;color:#f59e0b"></i>
            </span>
          </div>
        </div>
      </div>



      <form method="GET" action="" class="customers-filters flex mb-4 mt-10">
        <input type="text" name="search" class="input input-lg w-full"
          placeholder="Search customers by name or email..."
          value="<?= htmlspecialchars($search) ?>" />

        <div class="customers-filters__controls">
          <button type="submit" class="customers-filters__iconBtn" aria-label="Search" title="Search">
            <i class="fa-solid fa-magnifying-glass" style="color:#9c00f0"></i>
          </button>

          <select name="status">
            <option value="" <?= empty($status) ? 'selected' : '' ?>>All Status</option>
            <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>

          </select>

          <select name="order_sort">
            <option value="" <?= empty($orderSort) ? 'selected' : '' ?>>Order Count</option>
            <option value="orders_desc" <?= ($orderSort ?? '') === 'orders_desc' ? 'selected' : '' ?>>Most orders</option>
            <option value="orders_asc" <?= ($orderSort ?? '') === 'orders_asc' ? 'selected' : '' ?>>Least orders</option>
          </select>

          <select name="spent_sort">
            <option value="" <?= empty($spentSort) ? 'selected' : '' ?>>Total Spent</option>
            <option value="spent_desc" <?= ($spentSort ?? '') === 'spent_desc' ? 'selected' : '' ?>>High to low</option>
            <option value="spent_asc" <?= ($spentSort ?? '') === 'spent_asc' ? 'selected' : '' ?>>Low to high</option>
          </select>

          <button type="submit" class="customers-filters__iconBtn" aria-label="Apply filters" title="Apply filters">
            <i class="fa-solid fa-filter"></i>
          </button>

          <a href="<?= ROOT ?>dashboard/customers/all" class="customers-filters__iconBtn" aria-label="Refresh / clear filters" title="Refresh / clear filters">
            <i class="fa-solid fa-rotate-right"></i>
          </a>
        </div>
      </form>

      <div class="table-wrapper mt-10" style="margin-bottom: 2rem">
        <table class="table">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Mobile</th>
              <th>Email</th>
              <th>Orders</th>
              <th>Total Spent</th>
              <th>Last Order</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody style="min-height:400px">
            <?php if (!empty($customers)): ?>
              <?php foreach ($customers as $customer): ?>
                <tr>
                  <td><?= $customer['customer_name'] ?></td>
                  <td><?= $customer['mobile_number']??'N/A' ?></td>
                  <td><?= $customer['email'] ??'N/A'?></td>
                  <td><?= $customer['total_orders'] ?></td>
                  <td>$<?= number_format($customer['total_spent'], 2) ?></td>
                  <td>
                    <?php
                    if ($customer['last_order_date'] && $customer['last_order_time']) {
                      echo $customer['last_order_date'] . '<br>' . $customer['last_order_time'];
                    } else {
                      echo "No orders";
                    }
                    ?>
                  </td>
                  <td><span class="badge badge-primary badge-sm " style="background-color: #007bff; color: white;">Active</span></td>
                  <td>
                    <div class="flex gap-2 text-sm  ml-3">
                      <!-- View Customer -->
                      <a href="<?= ROOT ?>dashboard/customers/view?id=<?= $customer['customer_id'] ?>"
                        class="text-purple-600 hover:text-purple-800" title="View Customer">
                        <i class="fa-solid fa-eye cursor-pointer"></i>
                      </a>

                      <!-- Message Customer -->
                      <!-- <a href="#" class="text-blue-500 hover:text-blue-700" title="Message Customer">
       <i class="fa-solid fa-message cursor-pointer"></i>
    </a>

    Block Customer 
    <a href="#" class="text-red-500 hover:text-red-700" title="Block Customer">
       <i class="fa-solid fa-ban cursor-pointer"></i>
    </a> -->
                    </div>
                  </td>

                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center">No customers found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
          <!-- Previous Page -->
          <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status ?? '') ?>&order_sort=<?= urlencode($orderSort ?? '') ?>&spent_sort=<?= urlencode($spentSort ?? '') ?>"
              class="pagination__btn" aria-label="Previous page">
              <span>&lt;</span>
              <span class="text-sm">Previous</span>
            </a>
          <?php endif; ?>


          <span class="pagination__status" aria-label="Pagination status"><?= $currentPage ?> / <?= $totalPages ?></span>

          <!-- Next Page -->
          <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status ?? '') ?>&order_sort=<?= urlencode($orderSort ?? '') ?>&spent_sort=<?= urlencode($spentSort ?? '') ?>"
              class="pagination__btn" aria-label="Next page">
              <span class="text-sm">Next</span>
              <span>&gt;</span>
            </a>
          <?php endif; ?>
        </div>

      </div>

    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>