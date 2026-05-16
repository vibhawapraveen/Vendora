<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Export Orders - <?= htmlspecialchars($store_name) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            color: #111827;
        }
        .header p {
            margin: 0;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 600;
        }
        .items-list {
            margin: 0;
            padding-left: 20px;
            list-style-type: decimal;
            font-size: 13px;
        }
        .text-right {
            text-align: right;
        }
        .status-badge {
            font-weight: bold;
            text-transform: capitalize;
        }
        @media print {
            body {
                padding: 0;
            }
            @page {
                size: auto;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orders Export</h1>
        <p>Generated on <?= date('F j, Y, g:i a') ?></p>
        <?php if (!empty($filters['status']) || !empty($filters['from_date']) || !empty($filters['to_date'])): ?>
            <p><strong>Filters Applied:</strong> 
                <?= !empty($filters['status']) ? 'Status: ' . ucfirst(htmlspecialchars($filters['status'])) : '' ?>
                <?= !empty($filters['from_date']) ? '| From: ' . htmlspecialchars($filters['from_date']) : '' ?>
                <?= !empty($filters['to_date']) ? '| To: ' . htmlspecialchars($filters['to_date']) : '' ?>
            </p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Shipping Address</th>
                <th>Order Items</th>
                <th>Status</th>
                <th class="text-right">Total ($)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($order['order_number']) ?></strong></td>
                        <td><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <?= htmlspecialchars($order['customer_name']) ?><br>
                            <!-- <small style="color: #6b7280;"><?= htmlspecialchars($order['customer_email']) ?></small> -->
                        </td>
                        <td>
                            <?= htmlspecialchars($order['address_line1']) ?>
                            <?php if (!empty($order['address_line2'])) echo '<br>' . htmlspecialchars($order['address_line2']); ?>
                            <br><?= htmlspecialchars($order['city']) ?>
                        </td>
                        <td>
                            <?php if (!empty($order['items'])): ?>
                                <ul class="items-list">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?= htmlspecialchars($item['product_name']) ?> (Qty: <?= $item['quantity'] ?>)</li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <em style="color: #9ca3af;">No items found</em>
                            <?php endif; ?>
                        </td>
                        <td class="status-badge"><?= htmlspecialchars($order['status']) ?></td>
                        <td class="text-right"><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7280; padding: 20px;">
                        No orders match the current criteria.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($orders)): ?>
        <?php 
            $totalIncome = 0;
            
            foreach ($orders as $order) {
                if ($order['status'] === 'delivered' || $order['status'] === 'shipped') {
                    $totalIncome += $order['total_amount'];
                }
            }
        ?>
        <?php if (empty($filters['status']) || $filters['status'] === 'all'): ?>
        <tfoot>
            <tr style="background-color: #f3f4f6;">
                <td colspan="6" class="text-right"><strong>Total Income</strong></td>
                <td class="text-right">
                    <strong>$<?= number_format($totalIncome, 2) ?></strong>
                </td>
            </tr>
        </tfoot>
        <?php endif; ?>
        <?php endif; ?>
    </table>

    <script>
        // Trigger browser print dialog immediately when content loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500); // 500ms delay ensures fonts/styles render fully before printing
        }
    </script>
</body>
</html>
