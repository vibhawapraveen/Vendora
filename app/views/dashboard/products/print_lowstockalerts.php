<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Export Low Stock Alerts - <?= htmlspecialchars($store_name) ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/print_lowstockalerts.css?v=<?= time() ?>">
</head>

<body>
    <div class="header">
        <h1>Low Stock Alerts Export</h1>
        <p><?= htmlspecialchars($store_name) ?></p>
        <p>Generated on <?= date('F j, Y, g:i a') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Variant</th>
                <th class="text-right">Current Stock</th>
                <th class="text-right">Threshold</th>
                <th>Severity</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($alerts)): ?>
                <?php foreach ($alerts as $alert): ?>
                    <?php
                    $severity = 'Restock Soon';
                    if ((int)$alert['threshold'] > 0) {
                        $ratio = (int)$alert['current_stock'] / (int)$alert['threshold'];
                        if ($ratio <= 0.30) {
                            $severity = 'Critical';
                        } elseif ($ratio <= 0.70) {
                            $severity = 'Warning';
                        }
                    }
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($alert['product_name']) ?></strong></td>
                        <td><?= !empty($alert['variant_id']) ? 'Multi' : 'Single' ?></td>
                        <td class="text-right"><?= number_format((int)$alert['current_stock']) ?></td>
                        <td class="text-right"><?= number_format((int)$alert['threshold']) ?></td>
                        <td><?= $severity ?></td>
                        <td><?= date('Y-m-d', strtotime($alert['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7280; padding: 20px;">
                        No open stock alerts available for export.
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