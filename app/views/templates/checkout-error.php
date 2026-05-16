<?php
$storecode = $storecode ?? '';
$message = $message ?? 'An error occurred during payment processing';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Error - TechHub</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/customer/checkout.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <style>
        .error-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .error-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: #f44336;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }

        .error-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .error-subtitle {
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .error-message {
            background: #ffebee;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            color: #c62828;
            line-height: 1.6;
            border-left: 4px solid #f44336;
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
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>

        <h1 class="error-title">Payment Error</h1>
        <p class="error-subtitle">Something went wrong</p>

        <div class="error-message">
            <p>
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($message) ?>
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
