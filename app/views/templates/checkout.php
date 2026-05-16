<?php
// Use passed storecode from controller, fallback to BEAM-001
$storecode = $storecode ?? 'BEAM-001';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/customer/checkout.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body data-storecode="<?= $storecode ?>">
    <!-- NAVBAR -->
    <!-- <nav class="navbar">
        <div class="navbar-container">
            <a href="/" class="navbar-logo">
                <i class="fas fa-bolt" style="color: var(--primary-blue); font-size: 1.8rem;"></i>
                TechHub
            </a>
            <div class="navbar-icons">
                <button class="navbar-icon-btn" id="cartBtn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
                </button>
                <button class="navbar-icon-btn">
                    <i class="fas fa-user"></i>
                </button>
            </div>
        </div>
    </nav> -->

    <!-- CHECKOUT CONTAINER -->
    <div class="checkout-container">
        <h1 class="checkout-heading">
            <i class="fas fa-credit-card"></i> Checkout
        </h1>

        <div class="checkout-content">
            <!-- CHECKOUT FORM -->
            <form class="checkout-form" id="checkoutForm" method="POST" action="<?= ROOT ?><?= $storecode ?>/checkout">
                
                <!-- BILLING INFORMATION -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-map-marker-alt"></i> Delivery Information
                    </h3>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="name">Name </label>
                            <input value="<?= Session::user()['customer_name'] ?? "" ?>" type="text" id="name" name="name" required>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input disabled value="<?= Session::user()['customer_email'] ?? "" ?>" type="email" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="address1">Address line 1 *</label>
                            <input type="text" id="address1" name="address1" required>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="address2">Address line 2</label>
                            <input type="text" id="address2" name="address2">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                    </div>

                    <!-- <div class="form-row">
                        <div class="form-group">
                            <label for="zip">Zip Code *</label>
                            <input type="text" id="zip" name="zip" required>
                        </div>
                        <div class="form-group">
                            <label for="country">Country *</label>
                            <select id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="us">United States</option>
                                <option value="ca">Canada</option>
                                <option value="uk">United Kingdom</option>
                                <option value="au">Australia</option>
                            </select>
                        </div>
                    </div> -->
                </div>

                <!-- PAYMENT INFORMATION -->
                <!-- <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-credit-card"></i> Payment Information
                    </h3>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="cardName">Name on Card *</label>
                            <input type="text" id="cardName" name="cardName" required>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="cardNumber">Card Number *</label>
                            <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryDate">Expiry Date *</label>
                            <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV *</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                        </div>
                    </div>
                </div> -->

                <!-- TERMS AND CONDITIONS -->
                <!-- <div class="form-section">
                    <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer; font-size: 0.95rem;">
                        <input type="checkbox" id="terms" required style="margin-top: 0.25rem;">
                        <span>I agree to the <a href="#" style="color: var(--primary-blue); text-decoration: none;">Terms and Conditions</a> and <a href="#" style="color: var(--primary-blue); text-decoration: none;">Privacy Policy</a></span>
                    </label>
                </div> -->

                <!-- HIDDEN FIELDS -->
                <input type="hidden" id="hiddenCart" name="cart">
                <input type="hidden" id="storecode" name="storecode" value="<?= $storecode ?>">

                <!-- BACK AND SUBMIT BUTTONS -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="<?= ROOT.$storecode ?>/cart" style="flex: 1; padding: 1rem; border: 2px solid var(--border-color); border-radius: 8px; text-align: center; text-decoration: none; color: var(--text-dark); font-weight: 600; transition: var(--transition);" onmouseover="this.style.borderColor='var(--primary-blue)'" onmouseout="this.style.borderColor='var(--border-color)'">
                        <i class="fas fa-arrow-left"></i> Back to Cart
                    </a>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-check fa-1x mx-2"></i> Place Order
                    </button>
                </div>
            </form>

            <!-- ORDER SUMMARY SIDEBAR -->
            <div class="checkout-order-summary">
                <h3 class="order-summary-title">
                    <i class="fas fa-list"></i> Order Summary
                </h3>

                <div id="summaryItems" style="margin-bottom: 1rem;">
                    <!-- Items will be populated by JavaScript -->
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span id="checkoutTotal">$0.00</span>
                </div>

                <div style="background-color: var(--light-blue); padding: 1rem; border-radius: 8px; margin-top: 1.5rem; font-size: 0.9rem;">
                    <i class="fas fa-shield-alt" style="color: var(--primary-blue); margin-right: 0.5rem;"></i>
                    <strong>Your payment is secure</strong>
                    <p style="margin-top: 0.5rem; color: var(--text-light);">We use industry-standard security to protect your information.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="<?= ROOT ?>assets/js/pages/templates/beam/index.js"></script>
    <script src="<?= ROOT ?>assets/js/pages/templates/beam/checkout.js"></script> -->
    <script src="<?= ROOT ?>assets/js/pages/customer/checkout.js"></script>
    <script>
        const ROOT = '<?= ROOT ?>';
        // Initialize checkout page
        document.addEventListener('DOMContentLoaded', initCheckoutPage);
    </script>
</body>

</html>
