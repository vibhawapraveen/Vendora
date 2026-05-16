<!-- <!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Customer</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/new.css">
  <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script>
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>

    <main class="content">
      <!-- Title Section -->
      <div class="flex items-center">
        <div>
          <div>
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-user-plus text-xl"></i>
              <h2 class="font-semibold">Add New Customer</h2>
            </div>
            <p class="text-muted text-sm">Create a new customer listing for your store.</p>
          </div>
        </div>
      </div>

      <div class="card gap-3 mt-5">
        <div class="card-header mb-3">
          <div class="card-subtitle">Customer Information</div>
        </div>

        <!-- ✅ Success or Error Message -->
        <?php if (!empty($error)): ?>
          <div style="color: red; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
          <div style="color: green; margin-bottom: 15px;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- ✅ Add Customer Form -->
        <form method="POST" action="">
          <!-- Customer Name -->
          <div class="mb-5">
            <p class="mb-1">Customer Name</p>
            <input type="text" name="name" class="input" placeholder="Enter customer name" required />
          </div>

          <!-- Email -->
          <div class="mb-5">
            <p class="mb-1">Email</p>
            <input type="email" name="email" class="input" placeholder="Enter customer email" required />
          </div>

          <!-- Password -->
          <div class="mb-5">
            <p class="mb-1">Password</p>
            <input type="password" name="password" class="input" placeholder="Enter password" required />
          </div>

          <!-- Confirm Password -->
          <div class="mb-5">
            <p class="mb-1">Confirm Password</p>
            <input type="password" name="confirm_password" class="input" placeholder="Re-enter password" required />
          </div>

          <!-- Address Line 1 -->
          <div class="mb-5">
            <p class="mb-1">Address Line 1</p>
            <input type="text" name="address1" class="input" placeholder="Enter address line 1" required />
          </div>

          <!-- Address Line 2 -->
          <div class="mb-5">
            <p class="mb-1">Address Line 2</p>
            <input type="text" name="address2" class="input" placeholder="Enter address line 2" />
          </div>

          <!-- City -->
          <div class="mb-5">
            <p class="mb-1">City</p>
            <input type="text" name="city" class="input" placeholder="Enter city" required />
          </div>

          <!-- Submit Button -->
          <button type="submit" name="add_customer" class="btn btn-primary">Add Customer</button>
        </form>
      </div>
    </main>
  </div>

  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>
</html> -->
