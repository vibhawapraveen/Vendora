<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Export Products - <?= htmlspecialchars($report_name) ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/print_all.css?v=<?= time() ?>">
</head>

<body>
    <div class="header">
        <h1>Products Export</h1>
        <p><?= htmlspecialchars($report_name) ?></p>
        <p>Generated on <?= date('F j, Y, g:i a') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Store</th>
                <th>Category</th>
                <th>Status</th>
                <th class="text-right">Price</th>
                <th class="text-right">Stock</th>
                <th>Variant</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($product['name']) ?></strong></td>
                        <td><?= htmlspecialchars($product['store_name'] ?? '-') ?></td>
                        <td><?= !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Uncategorized' ?></td>
                        <td>
                            <?php
                            if ((int)($product['is_banned'] ?? 0) === 1) {
                                echo 'Banned';
                            } elseif ((int)($product['delete_flag'] ?? 0) === 1) {
                                echo 'Deleted';
                            } else {
                                echo ((int)($product['visibility'] ?? 0) === 1) ? 'Active' : 'Inactive';
                            }
                            ?>
                        </td>
                        <td class="text-right">
                            <?php
                            if ((int)$product['is_variant'] === 1 && isset($product['price_range'])) {
                                if ($product['price_range']['min'] == $product['price_range']['max']) {
                                    echo number_format($product['price_range']['min'], 2);
                                } else {
                                    echo number_format($product['price_range']['min'], 2) . ' - ' . number_format($product['price_range']['max'], 2);
                                }
                            } else {
                                echo isset($product['price']) && $product['price'] !== null ? number_format((float)$product['price'], 2) : '-';
                            }
                            ?>
                        </td>
                        <td class="text-right"><?= number_format((int)($product['stock_quantity'] ?? 0)) ?></td>
                        <td><?= ((int)$product['is_variant'] === 1) ? 'Multi' : 'Single' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7280; padding: 20px;">
                        No products available for export.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        // Open print dialog so user can choose Save as PDF.
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        };
    </script>
</body>

</html>