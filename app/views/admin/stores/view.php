<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Store Details - Vendora Admin</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/sellers.css">
  <style>
    a, .filter-btn, .action-btn {
      text-decoration: none !important;
    }
  </style>
</head>
<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/admin-sidebar.php' ?>

    <main class="content">
      <!-- Header Section -->
      <div class="page-header" style="display:flex; justify-content: space-between; margin-bottom: 30px;">
        <div class="page-header-left">
          <h2 class="font-semibold">Store Details</h2>
          <p class="text-muted">Viewing catalog for <?= htmlspecialchars($store['store_name']) ?></p>
        </div>
        <div>
            <a href="<?= ROOT ?>admin/dashboard/stores" class="btn btn-primary" style="text-decoration: none;">
             <i class="fas fa-arrow-left mr-2"></i>Back to Stores
            </a>
        </div>
      </div>

      <!-- Products List -->
      <div class="table-container" style="padding: 30px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 20px; color: #111827;">Store Products Catalog</h3>
        <?php if (!empty($products)): ?>
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                        <th style="padding: 12px; font-weight: 600; color: #374151;">Product Name</th>
                        <th style="padding: 12px; font-weight: 600; color: #374151;">Variant</th>
                        <th style="padding: 12px; font-weight: 600; color: #374151;">Status</th>
                        <th style="padding: 12px; font-weight: 600; color: #374151;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px; color: #1f2937;">
                            <div style="font-weight: 500;"><?= htmlspecialchars($product['name']) ?></div>
                        </td>
                        <td style="padding: 12px;">
                            <?php if ($product['is_variant'] == 1): ?>
                                <button onclick="toggleVariants('<?= $product['id'] ?>')" class="badge badge-multi" style="cursor: pointer; border: none; padding: 4px 10px;">
                                    <i class="fa-solid fa-layer-group"></i> Multi <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 5px;"></i>
                                </button>
                            <?php else: ?>
                                <button onclick="toggleVariants('<?= $product['id'] ?>')" class="badge badge-single" style="border: none; padding: 4px 10px; cursor: pointer;">
                                    <i class="fa-solid fa-box"></i> Single <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 5px;"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;">
                            <?php if ($product['visibility']): ?>
                                <span class="badge badge-primary badge-sm">Active</span>
                            <?php else: ?>
                                <span class="badge badge-secondary badge-sm">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;">
                            <div class="action-buttons" style="display:flex; gap:8px;">
                              <!-- Toggle Action -->
                              <a href="<?= ROOT ?>admin/dashboard/stores?toggle_product_id=<?= $product['id'] ?>&store_id=<?= $store['id'] ?>&current_visibility=<?= $product['visibility'] ?>" class="action-btn suspend" title="Toggle Status" style="text-decoration: none;">
                                <i class="fas <?= $product['visibility'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                              </a>
                              <!-- Delete Action -->
                              <a href="<?= ROOT ?>admin/dashboard/stores?delete_product_id=<?= $product['id'] ?>&store_id=<?= $store['id'] ?>" class="action-btn delete" title="Delete Product" onclick="return confirm('WARNING: Are you sure you want to completely delete this product from the storefront? This will cascade delete any attributes and variants attached to this internal product item!');" style="text-decoration: none;">
                                <i class="fas fa-trash"></i>
                              </a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Hidden Variants Row -->
                    <tr id="variants_<?= $product['id'] ?>" style="display:none; background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <td colspan="4" style="padding: 15px 30px;">
                            <h4 style="font-size: 0.9rem; font-weight: bold; margin-bottom: 10px; color: #475569;">Detailed Product Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                <thead>
                                    <tr style="text-align: left; border-bottom: 1px solid #cbd5e1; color: #64748b;">
                                        <th style="padding: 6px;">Configuration</th>
                                        <th style="padding: 6px;">Price</th>
                                        <th style="padding: 6px;">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($product['is_variant'] == 1 && !empty($product['variants'])): ?>
                                        <?php foreach ($product['variants'] as $v): ?>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 8px; color: #334155;"><?= htmlspecialchars($v['variant_name']) ?></td>
                                            <td style="padding: 8px; color: #334155;">$<?= number_format($v['price'], 2) ?></td>
                                            <td style="padding: 8px; color: #334155;"><?= htmlspecialchars($v['stock_quantity']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr style="border-bottom: 1px solid #e2e8f0;">
                                            <td style="padding: 8px; color: #334155;">Base Standard Configuration</td>
                                            <td style="padding: 8px; color: #334155;">$<?= number_format($product['price'], 2) ?></td>
                                            <td style="padding: 8px; color: #334155;"><?= htmlspecialchars($product['stock_quantity']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #6b7280; text-align: center; padding: 30px; border: 1px dashed #d1d5db; border-radius: 8px;">
                <i class="fas fa-box-open" style="font-size: 32px; color: #9ca3af; margin-bottom: 15px; display: block;"></i>
                This store doesn't have any products yet!
            </p>
        <?php endif; ?>
      </div>

    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>

  <script>
      function toggleVariants(productId) {
          const row = document.getElementById('variants_' + productId);
          if (row.style.display === 'none') {
              row.style.display = 'table-row';
          } else {
              row.style.display = 'none';
          }
      }
  </script>
</body>
</html>
