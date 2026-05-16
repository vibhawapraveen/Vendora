<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Dashboard</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/products_index.css">
  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>

  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>


  <div class="sidebar-backdrop"></div>

  <div class="layout">

    <?php require 'assets/components/sidebar.php' ?>


    <main class="content">

      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold">Product Dashboard</h2>
          <p class="text-muted text-sm">Welcome back! Here's what's happening with your business.</p>
        </div>
        <div class="flex gap-2">
          <!-- Add Product Button -->
          <a href="<?= ROOT ?>dashboard/products/newproduct" class="btn btn-primary" style="text-decoration: none;">
            <i class="fa-solid fa-plus pr-2"></i> Add New Product
          </a>
        </div>
      </div>


      <div class="grid grid-cols-4 gap-3 mt-5">

        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Total Products</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['total_products'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-box-archive text-3xl gray"></i>
        </div>


        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Active Products</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['active_products'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-check text-3xl text-success"></i>
        </div>


        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Inactive Products</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['inactive_products'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-pause text-3xl text-amber"></i>
        </div>


        <div class="card flex justify-between items-center">
          <div class="w-3/4">
            <div class="text-sm text-muted">Low Stock Alert</div>
            <div class="card-content">
              <div class="text-2xl font-bold"><?= number_format($stats['low_stock'] ?? 0) ?></div>
            </div>
          </div>
          <i class="fa-solid fa-circle-exclamation text-3xl text-destructive"></i>
        </div>
      </div>

      <div class="card mt-5 gap-3">
        <div class="card-header mb-3">
          <div class="card-subtitle">Quick Actions</div>
        </div>
        <div class="flex flex-wrap gap-3 mb-2">
          <a href="<?= ROOT ?>dashboard/products/newproduct" class="btn btn-secondary" style="text-decoration: none;">
            <i class="fa-solid fa-plus pr-2 text-sky"></i> Add New Product
          </a>

          <a href="<?= ROOT ?>dashboard/products/managecategories" class="btn btn-secondary" style="text-decoration: none;">
            <i class="fa-solid fa-tags pr-2 text-primary"></i> Manage Categories
          </a>
          <!-- Export Button -->
          <div class="btn btn-secondary" onclick="window.open('<?= ROOT ?>dashboard/products/all?export=pdf', '_blank')" style="cursor: pointer;">
            <i class="fa-solid fa-download pr-2"></i> Export Products
          </div>
        </div>
      </div>

      <div class="card mt-5 gap-3 p-0">
        <div class="card-header px-6 py-3">
          <div class="card-subtitle">Recent Activity</div>
        </div>
        <table class="table w-full text-sm mb-10 rounded-lg p-0">
          <thead>
            <tr class="text-left">
              <th class="px-6 py-3">Product</th>
              <th class="px-6 py-3">Action</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Last Updated</th>
              <th class="px-6 py-3">Variant</th>
              <th class="px-6 py-3">Stock</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recentProducts)): ?>
              <?php foreach ($recentProducts as $product): ?>
                <tr>
                  <!-- Name -->
                  <td class="px-6 py-4 flex items-center gap-3">
                    <?php if (!empty($product['first_image'])): ?>
                      <img src="<?= ROOT . $product['first_image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-10 h-10 rounded-lg object-cover">
                    <?php else: ?>
                      <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                        <i class="fa-solid fa-image text-gray-400 text-sm"></i>
                      </div>
                    <?php endif; ?>
                    <div>
                      <a href="<?= ROOT ?>dashboard/products/<?= $product['id'] ?>/edit" class="font-medium hover:opacity-70 transition-all duration-200" style="text-decoration: none; color: #000;">
                        <?= htmlspecialchars($product['name']) ?>
                      </a>
                    </div>
                  </td>

                  <!-- Created or Updated -->
                  <td class="px-6 py-4">
                    <?= $product['created_at'] == $product['updated_at'] ? 'Created' : 'Updated' ?>
                  </td>

                  <!-- Status -->
                  <td class="px-6 py-4">
                    <?php if ($product['visibility'] == 1): ?>
                      <span class="badge badge-success">Active</span>
                    <?php else: ?>
                      <span class="badge badge-destructive">Inactive</span>
                    <?php endif; ?>
                  </td>

                  <!-- Updated time -->
                  <td class="px-6 py-4">
                    <?= date('M d, Y', strtotime($product['updated_at'])) ?>
                  </td>

                  <!-- Variant -->
                  <td class="px-6 py-4">
                    <?php if ($product['is_variant'] == 1): ?>
                      <span class="badge badge-multi">
                        <i class="fa-solid fa-layer-group"></i> Multi
                      </span>
                    <?php else: ?>
                      <span class="badge badge-single">
                        <i class="fa-solid fa-box"></i> Single
                      </span>
                    <?php endif; ?>
                  </td>

                  <!-- Stock -->
                  <td class="px-6 py-4">
                    <?= $product['stock_quantity'] ?? 'N/A' ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                  No products found. <a href="<?= ROOT ?>dashboard/products/newproduct" class="text-primary">Add your first product</a>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

    </main>
  </div>
  </div>

  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>