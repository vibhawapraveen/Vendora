<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/orders/index.css">
</head>

<body data-storecode="<?= htmlspecialchars($storecode) ?>">
    <!-- Navbar -->
    <nav class="orders-navbar">
        <div class="navbar-left">
            <a href="<?= ROOT . $storecode ?>" class="back-link">
                <i class="fa-solid fa-angle-left"></i>
                <span>Back to Store</span>
            </a>
        </div>
        <div class="navbar-right flex items-center gap-3">
            <div class="grid">
                <div>
                    <?= Session::user()['customer_email'] ?? "" ?>
                </div>
                <a class="text-sm text-muted" style="text-align: end;" href="<?= ROOT ?>authcustomer/signout?redirect_store=<?= $storecode ?>">Signout</a>
            </div>
            <button class="profile-btn">
                <i class="fa-solid fa-user"></i>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="orders-container">
        <div class="orders-header">
            <h1>My Orders</h1>
            <p class="text-muted">Track and manage your orders with <?= $storecode ?></p>
        </div>

        <div class="mb-5">
            <form action="" method="get">
                <div class="flex gap-3">
                    <input value="<?= $_GET['start_date'] ?? "" ?>" name="start_date" class="input w-fit" type="date">
                    <input value="<?= $_GET['end_date'] ?? "" ?>" name="end_date" class="input w-fit" type="date">
                    <button type="submit" class="btn btn-secondary">Apply</button>
                </div>
            </form>
        </div>
        <hr>

        <!-- Orders List -->
        <div class="orders-list">
            <?php if (empty($orders)): ?>
                <div class="card">
                    <div class="card-content" style="text-align: center; padding: 2rem;">
                        <p class="text-muted">You haven't placed any orders yet.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="card order-card">
                        <div class="card-header">
                            <div class="order-header-info">
                                <div class="order-number">Order #<?= htmlspecialchars($order['order_number']) ?></div>
                                <span class="badge badge-<?= $order['status'] === 'pending' ? 'primary' : ($order['status'] === 'delivered' ? 'success' : 'warning') ?> badge-md">
                                    <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                </span>
                            </div>
                            <div class="order-date">
                                <i class="fa-solid fa-calendar"></i>
                                <span><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="card-content">
                            <!-- Order Items -->
                            <div class="order-items">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="order-item">
                                        <div class="item-details">
                                            <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <?php if ($item['variant_description']): ?>
                                                <div class="item-variant text-muted"><?= htmlspecialchars($item['variant_description']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-quantity">Qty: <?= $item['quantity'] ?></div>
                                        <div class="item-price">$ <?= number_format($item['unit_price'], 2) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Delivery Address -->
                            <div class="order-address">
                                <div class="address-title">
                                    <i class="fa-solid fa-map-pin"></i>
                                    <span>Delivery Address</span>
                                </div>
                                <div class="address-content">
                                    <?= htmlspecialchars($order['address_line1']) ?><br>
                                    <?php if ($order['address_line2']): ?>
                                        <?= htmlspecialchars($order['address_line2']) ?><br>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($order['city']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="order-total">
                                <span class="total-label">Total Amount:</span>
                                <span class="total-amount">$ <?= number_format($order['total_amount'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= ROOT ?>assets/js/components/toast.js"></script>
    <script src="<?= ROOT ?>assets/js/pages/templates/base/index.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                clearCart();
                showToast("Order placed successfully!");
            }
            if (urlParams.has('error')) {
                showToast(urlParams.get('error'), 'error');
            }
        })
    </script>

      <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <script src="<?= ROOT ?>assets/js/lucide.js"></script>
</body>

</html>