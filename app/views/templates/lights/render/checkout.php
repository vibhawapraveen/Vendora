<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyStore - Checkout</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/templates/lights.css">
</head>

<body data-storecode="<?= $storecode ?>">
    <!-- Navbar -->
    <nav class="navbar">
        <a href="<?= ROOT.$storecode ?>" style="text-decoration: none;">
            <div class="logo"><?=$content['navbar_text']?></div>
        </a>
        <div style="display: flex; align-items: center; gap: 20px;">
            <?php require "../app/views/templates/user.php"; ?>
            <a href="<?= ROOT . $storecode ?>/cart" class="cart-link">
                🛒 Your Cart
                <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
    </nav>

    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Checkout</h2>
            <form id="checkout-form" method="post" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input value="<?=Session::user()['customer_email'] ?? '' ?>" type="email" id="email" name="email" required <?= empty(Session::user()['customer_email']) ? '' : 'readonly' ?>>
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input value="<?=Session::user()['customer_name'] ?? '' ?>" type="text" id="name" name="name" required <?= empty(Session::user()['customer_name']) ? '' : 'readonly' ?>>
                </div>

                <div class="form-group">
                    <label for="address">Address line 1</label>
                    <input id="address" name="address1" required />
                </div>

                 <div class="form-group">
                    <label for="address">Address line 2</label>
                    <input id="address" name="address2" required />
                </div>

                 <div class="form-group">
                    <label for="address">City</label>
                    <input id="address" name="city" required />
                </div>
                <input type="text" name="cart" id="cart-input" hidden>
                <input type="text" name="storecode" value="<?=$storecode?>" hidden>
            </form>
        </div>

        <div class="checkout-summary">
            <h2>Order Summary</h2>
            <div class="summary-row summary-total">
                <span>Total Amount:</span>
                <span id="checkout-total">$0.00</span>
            </div>
            <button type="submit" form="checkout-form" class="pay-button">Pay Now</button>
        </div>
    </div>

    <script>
        const ROOT = '<?= ROOT ?>';
    </script>
    <script src="<?= ROOT ?>assets/js/pages/templates/base/index.js"></script>
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->

    <script>
        // Load total on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            const cart = getCart();
            console.log(cart);
            const total = calculateTotal(cart);
            document.getElementById('checkout-total').textContent = '$ ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('cart-input').value = JSON.stringify(cart);
        });
    </script>
    <script>
        const stylesFromDB = {
            primary: "<?=$content['primary_color']?>",
        };

        const root = document.documentElement; // <html> element
        root.style.setProperty('--primary', stylesFromDB.primary);
    </script>
</body>

</html>