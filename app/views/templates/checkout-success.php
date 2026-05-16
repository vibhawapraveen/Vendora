<?php
$order_id = $order_id ?? '';
$storecode = $storecode ?? '';
$customer_email = $customer_email ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - TechHub</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/customer/checkout.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }

        .success-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .success-subtitle {
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .order-details {
            background: #f5f5f5;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-blue, #007BFF);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--border-color, #e0e0e0);
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .email-notice {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1.5rem;
            color: #1565c0;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-subtitle">Thank you for your purchase</p>

        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Order ID:</span>
                <span class="detail-value"><?= htmlspecialchars($order_id) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer Email:</span>
                <span class="detail-value"><?= htmlspecialchars($customer_email) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value" style="color: #4CAF50;"><i class="fas fa-check-circle"></i> Confirmed</span>
            </div>
        </div>

        <div class="email-notice">
            <i class="fas fa-envelope"></i> A confirmation email has been sent to <?= htmlspecialchars($customer_email) ?>
        </div>

        <div class="action-buttons">
            <a href="<?= ROOT ?><?= $storecode ?>/orders" class="btn btn-primary">
                <i class="fas fa-box"></i> View Orders
            </a>
            <a href="<?= ROOT ?>store/<?= htmlspecialchars($storecode) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    </div>
</body>

</html>
