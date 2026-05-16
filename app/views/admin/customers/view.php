<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details - Vendora Admin</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/sellers.css">
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/admin-sidebar.php' ?>

        <main class="content">
            <!-- Header Section -->
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="page-header-left">
                    <h2 class="font-semibold">Customer Details</h2>
                    <p class="text-muted">Viewing data for <?= htmlspecialchars($customer['name']) ?></p>
                </div>
                <div class="page-header-right">
                    <a href="<?= ROOT ?>admin/dashboard/customers" class="btn btn-primary" style="text-decoration: none;">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Customers
                    </a>
                </div>
            </div>

            <!-- Data Tables (Stacked) -->
            <div style="display: flex; flex-direction: column; gap: 20px;">

                <!-- Left Column: Customer Info Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Customer Information</h3>
                    </div>
                    <div class="table-wrapper">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="text-align: left; background: #f8fafc; width: 40%;">Name</th>
                                    <td><?= htmlspecialchars($customer['name']) ?></td>
                                </tr>
                                <tr>
                                    <th style="text-align: left; background: #f8fafc;">Email</th>
                                    <td><?= htmlspecialchars($customer['email']) ?></td>
                                </tr>
                                <tr>
                                    <th style="text-align: left; background: #f8fafc;">Address</th>
                                    <td>
                                        <?php if (!empty($customer['address_line1'])): ?>
                                            <?= htmlspecialchars($customer['address_line1']) ?><br>
                                            <?= !empty($customer['address_line2']) ? htmlspecialchars($customer['address_line2']) : '' ?>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-style: italic;">Not provided</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="text-align: left; background: #f8fafc;">City</th>
                                    <td><?= !empty($customer['city']) ? htmlspecialchars($customer['city']) : '<span style="color: #94a3b8; font-style: italic;">Not provided</span>' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Column: Recent Orders History -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Order Details</h3>
                    </div>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $o): ?>
                                        <tr>
                                            <td><span style="font-weight: 600; color: var(--primary);">#<?= htmlspecialchars($o['order_number']) ?></span></td>
                                            <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = 'badge-default';
                                                $statusLabel = ucfirst($o['status']);
                                                if ($o['status'] === 'delivered') {
                                                    $statusClass = 'badge-primary';
                                                    $statusLabel = 'Complete';
                                                } elseif ($o['status'] === 'cancelled') {
                                                    $statusClass = 'badge-destructive';
                                                } elseif ($o['status'] === 'shipped') {
                                                    $statusClass = 'badge-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>" style="font-size: 0.75rem;"><?= $statusLabel ?></span>
                                            </td>
                                            <td style="font-weight: 600;">$<?= number_format($o['total_amount'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 20px;">No historical orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>
