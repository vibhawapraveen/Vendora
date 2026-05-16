<?php
$storecode = $storecode ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - TechHub</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/customer/checkout.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <style>
        .cancel-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .cancel-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: #ff9800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }

        .cancel-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .cancel-subtitle {
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .cancel-message {
            background: #fff3e0;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            color: #e65100;
            line-height: 1.6;
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
    </style>
</head>

<body>
    <div class="cancel-container">
        <div class="cancel-icon">
            <i class="fas fa-times"></i>
        </div>

        <h1 class="cancel-title">Payment Cancelled</h1>
        <p class="cancel-subtitle">Your payment was not completed</p>

        <div class="cancel-message">
            <p>
                <i class="fas fa-info-circle"></i> Your payment was cancelled. You can try again or return to your cart to review your items.
            </p>
        </div>

        <div class="action-buttons">
            <a href="<?= ROOT ?><?= $storecode ?>/checkout" class="btn btn-primary">
                <i class="fas fa-redo"></i> Try Again
            </a>
            <a href="<?= ROOT ?>shop/<?= htmlspecialchars($storecode) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Store
            </a>
        </div>
    </div>
</body>

</html>
