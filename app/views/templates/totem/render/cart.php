<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyStore - Cart</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/templates/totem.css">
</head>

<body data-storecode="<?= $storecode ?>">
    <!-- Navbar -->
    <nav class="navbar">
        <a href="<?= ROOT . $storecode ?>" style="text-decoration: none;">
            <div class="logo"><?= $content['navbar_text_totem'] ?></div>
        </a>
        <div style="display: flex; align-items: center; gap: 20px;">
            <?php require "../app/views/templates/user.php"; ?>
            <a href="<?= ROOT . $storecode ?>/cart" class="cart-link">
                🛒 Your Cart
                <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
    </nav>

    <div class="cart-container">
        <div class="cart-items">
            <h2>Shopping Cart</h2>
            <div id="cart-items-list">
                <!-- Cart items will be inserted here by JavaScript -->
            </div>
        </div>

        <div class="cart-summary">
            <h2>Order Summary</h2>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="subtotal">$ 0.00</span>
            </div>
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span id="total">$ 0.00</span>
            </div>
            <a href="<?= ROOT . $storecode ?>/checkout" class="checkout-btn">Proceed to Checkout</a>
        </div>
    </div>

    <script>
        const ROOT = '<?= ROOT ?>';
    </script>
    <script src="<?= ROOT ?>assets/js/pages/templates/base/index.js"></script>
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->

    <script>
        // Load cart on page load
        document.addEventListener('DOMContentLoaded', displayCart);
    </script>
    <script>
        const stylesFromDB = {
            primary: "<?= $content['primary_color_totem'] ?>",
        };

        const root = document.documentElement; // <html> element
        root.style.setProperty('--primary', stylesFromDB.primary);
    </script>
</body>

</html>