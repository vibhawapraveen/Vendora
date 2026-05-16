<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Export Products - <?= htmlspecialchars($store_name) ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/print_all.css?v=<?= time() ?>">
</head>

<body>
    <div class="header">
        <h1>Products Export</h1>
        <p><?= htmlspecialchars($store_name) ?></p>
        <p>Generated on <?= date('F j, Y, g:i a') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Status</th>
                <th>Last Updated</th>
                <th class="text-right">Price (LKR)</th>
                <th class="text-right">Stock</th>
                <th>Variant</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($product['name']) ?></strong>

                        </td>
                        <td><?= ((int)$product['visibility'] === 1) ? 'Active' : 'Inactive' ?></td>
                        <td><?= date('Y-m-d', strtotime($product['updated_at'])) ?></td>
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
                    <td colspan="6" style="text-align: center; color: #6b7280; padding: 20px;">
                        No products available for export.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        // Opens browser print dialog; user can choose "Save as PDF".
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        };
    </script>
</body>

</html>