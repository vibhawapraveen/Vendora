<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Customer</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/new.css">
  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <style>
    .header {
      background: linear-gradient(135deg, #7b5cf7 0%, #764ba2 100%);
      color: #fff;
      border-radius: 0.5rem;
      padding: 2rem;
      margin-bottom: 2rem;
    }

    .card {
      background: #fff;
      border-radius: 0.5rem;
      padding: 1.5rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      margin-bottom: 1.5rem;
    }

    .card-subtitle {
      font-weight: 600;
      margin-bottom: 1rem;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn {
      display: inline-block;
      padding: 0.5rem 1rem;
      background: #7b5cf7;
      color: #fff;
      border-radius: 0.3rem;
      text-decoration: none;
      text-align: center;
    }

    .btn-secondary {
      background: #6b7280;
    }

    .text-muted {
      color: #6b7280;
    }

    .text-lg {
      font-size: 1rem;
    }

    .grid {
      display: grid;
      gap: 1.5rem;
    }

    @media (min-width: 1024px) {
      .grid-cols-3 {
        grid-template-columns: repeat(3, 1fr);
      }

      .col-span-2 {
        grid-column: span 2 / span 2;
      }
    }

    .item-border {
      border-bottom: 1px solid #e5e7eb;
      padding-bottom: 1rem;
      margin-bottom: 1rem;
    }

    .item-border:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
  </style>
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php'; ?>

    <main class="content">

      <!-- Header -->
      <div class="header">
        <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($customer['name']) ?></h1>
        <p class="text-lg">Customer since <?= date('F Y', strtotime($customer['created_at'])) ?></p>
      </div>

      <div class="grid grid-cols-3 gap-6">

        <!-- Left Column - Customer Details -->
        <div class="col-span-2">
          <div class="card">
            <h2 class="card-subtitle">
              <i class="fa-solid fa-user text-lg"></i> Customer Information
            </h2>

            <div class="item-border">
              <label class="text-sm font-medium text-muted">Full Name</label>
              <div class="text-lg"><?= htmlspecialchars($customer['name']) ?></div>
            </div>

            <div class="item-border">
              <label class="text-sm font-medium text-muted">Email</label>
              <div class="text-lg"><?= htmlspecialchars($customer['email'] ?? 'N/A') ?></div>
            </div>

            <div class="item-border">
              <label class="text-sm font-medium text-muted">Phone</label>
              <div class="text-lg"><?= htmlspecialchars($customer['mobile_number'] ?? 'N/A') ?></div>
            </div>

            <div class="item-border">
              <label class="text-sm font-medium text-muted">Customer Since</label>
              <div class="text-lg"><?= date('F Y', strtotime($customer['created_at'])) ?></div>
            </div>

            <div class="item-border">
              <label class="text-sm font-medium text-muted">Status</label>
              <div class="text-lg"><?= htmlspecialchars($customer['status'] ?? 'Active') ?></div>
            </div>
          </div>
        </div>

        <!-- Right Column - Address & Actions -->
        <div>
          <!-- Address -->
          <div class="card">
            <h2 class="card-subtitle">
              <i class="fa-solid fa-truck text-lg"></i> Address
            </h2>
            <div class="item-border">
              <div class="text-lg"><?= htmlspecialchars($customer['address_line1'] ?? 'N/A') ?></div>
              <div class="text-lg"><?= htmlspecialchars($customer['address_line2'] ?? '') ?></div>
              <div class="text-lg"><?= htmlspecialchars($customer['city'] ?? '') ?></div>
              <div class="text-lg"><?= htmlspecialchars($customer['country'] ?? 'Sri Lanka') ?></div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card">
            <h2 class="card-subtitle">Actions</h2>
            <div class="flex  gap-3">
              <a href="<?= ROOT ?>dashboard/customers/edit?id=<?= $customer['id'] ?>" class="btn">Edit Customer</a>
              <button class="btn btn-secondary" onclick="window.print()">Print Info</button>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>
</html>
