<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Checkout</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/pos/posCheckout.css">
    <script>
        window.ROOT = '<?= ROOT ?>';
    </script>
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>
    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>

        <main class="content">
            <div class="pos-container">
                <!-- Left Section: Customer Information -->
                <div class="pos-customer-section">
                    <div class="customer-card">
                        <div class="customer-header">

                            <h2>Checkout</h2>
                        </div>
                        <div class="form-subtitle">
                            <i class="fas fa-user-plus"></i>
                            Customer Type
                        </div>
                        <div class="form-group">
                            <select class="form-input" id="customer-type">
                                <option value="new">New Customer</option>
                                <option value="old">Existing Customer</option>
                            </select>

                        </div>
                        <div class="customer-section" id="new-customer-form">
                            <div class="form-group">
                                <span class="form-label"><i class="fas fa-phone"></i> Mobile Number *</span>
                                <input type="text" class="form-input" id="customer-mobile" placeholder=" " inputmode="numeric" pattern="[0-9]{10}" maxlength="10" />
                            </div>
                            <div class="form-group">
                                <span class="form-label"><i class="fas fa-user"></i> Name *</span>
                                <input type="text" class="form-input" id="customer-name" placeholder=" " />
                            </div>
                            <div class="form-group">
                                <span class="form-label"><i class="fas fa-map-marker-alt"></i> Address Line 1</span>
                                <input type="text" class="form-input" id="customer-address1" placeholder=" " />
                            </div>
                            <div class="form-group">
                                <span class="form-label"><i class="fas fa-map-marker"></i> Address Line 2</span>
                                <input type="text" class="form-input" id="customer-address2" placeholder=" " />
                            </div>
                            <div class="form-group">
                                <span class="form-label"><i class="fas fa-city"></i> City</span>
                                <input type="text" class="form-input" id="customer-city" placeholder=" " />
                            </div>
                        </div>
                        <div class="customer-section" id="old-customer-search" style="display:none;">
                            <div class="form-group">
                                <span class="form-label"><i class="fas fa-phone"></i> Mobile Number *</span>
                                <input type="text" class="form-input" id="search-customer" placeholder=" " inputmode="numeric" pattern="[0-9]{10}" maxlength="10" />
                            </div>
                            <div id="customer-results" class="customer-results" style="display:none;"></div>
                            <div id="selected-customer-details" class="customer-selected" style="display:none;"></div>
                        </div>
                        <button class="btn-main" id="confirm-checkout">
                            <i class="fas fa-credit-card"></i> Confirm Payment
                        </button>
                    </div>
                </div>

                <!-- Right Section: Cart -->
                <div class="pos-cart-section">
                    <div class="pos-cart-header">
                        <h3 class="font-semibold">Current Order</h3>
                        <button class="btn btn-sm btn-outline" id="clear-cart">Clear</button>
                    </div>

                    <div class="pos-cart-items" id="cart-items">
                        <!-- Cart items will be rendered here by JS -->
                    </div>
                    <div class="pos-cart-summary">
                        <div class="pos-summary-row">
                            <span>Subtotal</span>
                            <span id="cart-subtotal">$0.00</span>
                        </div>
                        <div class="pos-summary-row pos-summary-total">
                            <span>Total</span>
                            <span id="cart-total">$0.00</span>
                        </div>
                        <!-- Remove checkout-summary and checkout-cart here -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modern Confirm Payment Modal -->
    <div class="modal-overlay" id="payment-modal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="payment-modal-title">
            <div class="modal-header">
                <div class="modal-icon success" aria-hidden="true">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <h3 id="payment-modal-title">Payment confirmed</h3>
                    <p class="modal-subtitle">Receipt is ready to print.</p>
                </div>
                <button class="modal-close" type="button" id="payment-modal-close" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <!-- Receipt area (this block will be printed) -->
                <div class="receipt" id="receipt-print">
                    <div class="receipt-top">
                        <div class="receipt-brand">
                            <div class="receipt-logo">V</div>
                            <div>
                                <div class="receipt-title">Vendora POS</div>
                                <div class="receipt-sub">Payment Receipt</div>
                            </div>
                        </div>

                        <div class="receipt-meta">
                            <div class="receipt-meta-row">
                                <span>Receipt #</span>
                                <strong id="pm-receipt-no">—</strong>
                            </div>
                            <div class="receipt-meta-row">
                                <span>Date</span>
                                <strong id="pm-time">—</strong>
                            </div>
                            <div class="receipt-meta-row">
                                <span>Cashier</span>
                                <strong id="pm-cashier">—</strong>
                            </div>
                        </div>
                    </div>

                    <div class="receipt-section">
                        <div class="receipt-section-title">Customer</div>
                        <div class="receipt-kv">
                            <span>Name</span>
                            <strong id="pm-customer">—</strong>
                        </div>
                        <div class="receipt-kv">
                            <span>Mobile</span>
                            <strong id="pm-mobile">—</strong>
                        </div>
                    </div>

                    <div class="receipt-section">
                        <div class="receipt-section-title">Items</div>
                        <div class="receipt-items" id="pm-items">
                            <!-- JS renders rows -->
                        </div>
                    </div>

                    <div class="receipt-section">
                        <div class="receipt-section-title">Summary</div>
                        <div class="receipt-kv">
                            <span>Subtotal</span>
                            <strong id="pm-subtotal">$0.00</strong>
                        </div>
                        <div class="receipt-kv receipt-total">
                            <span>Total</span>
                            <strong id="pm-total">$0.00</strong>
                        </div>
                        <div class="receipt-kv">
                            <span>Payment</span>
                            <strong id="pm-payment-method">Cash</strong>
                        </div>
                    </div>

                    <div class="receipt-footer">
                        Thank you! Please come again.
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn btn-outline" type="button" id="payment-modal-print">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
                <button class="btn-main" type="button" id="payment-modal-done">
                    Done
                </button>
            </div>
        </div>
    </div>

    <script src="<?= ROOT ?>assets/js/pages/pos/poscheckout.js"></script>
</body>

</html>