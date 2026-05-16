<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - TechHub</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/templates/beam.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body data-storecode="<?= $storecode ?>">
    <!-- NAVBAR -->
     <?php require_once "../public/assets/components/templates/beam/navbar.php" ?>

    <!-- <nav class="navbar">
        <div class="navbar-container">
            <a href="/" class="navbar-logo">
                <i class="fas fa-bolt" style="color: var(--primary); font-size: 1.8rem;"></i>
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

    <!-- CART CONTAINER -->
    <div class="cart-container">
        <h1 class="cart-heading">
            <i class="fas fa-shopping-cart"></i> Shopping Cart
        </h1>

        <div id="cartContent">
            <div class="cart-content">
                <!-- CART ITEMS -->
                <div class="cart-items-list" id="cartItems">
                    <!-- Items will be populated by JavaScript -->
                </div>

                <!-- CART SUMMARY -->
                <div class="cart-summary">
                    <h3 style="font-size: 1.2rem; margin-bottom: 1.5rem; color: var(--primary-blue);">Order Summary</h3>
                    <div class="summary-total">
                        <span>Total:</span>
                        <span id="total">$ 0.00</span>
                    </div>
                    <button class="checkout-btn" onclick="goToCheckout()">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>

        <!-- EMPTY CART MESSAGE -->
        <div id="emptyCart" style="display: none;">
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="empty-cart-text">Your cart is empty</p>
                <a href="<?= ROOT.$storecode ?>" class="continue-shopping-btn">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <script src="<?= ROOT ?>assets/js/pages/templates/beam/index.js"></script>
    <script>
        // Set up event delegation for remove buttons (before DOMContentLoaded)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-btn')) {
                const itemId = e.target.closest('.remove-btn').dataset.itemId;
                removeFromCart(itemId);
            }
        });

        /**
         * Display cart items on the page
         */
        function displayCartItems() {
            const cart = getCartFromLocalStorage();
            const cartItemsContainer = document.getElementById('cartItems');
            const cartContent = document.getElementById('cartContent');
            const emptyCart = document.getElementById('emptyCart');

            if (cart.length === 0) {
                cartContent.style.display = 'none';
                emptyCart.style.display = 'block';
                return;
            }

            cartContent.style.display = 'grid';
            emptyCart.style.display = 'none';
            cartItemsContainer.innerHTML = '';

            cart.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item';
                
                // Determine image source
                const imageUrl = item.image || `https://via.placeholder.com/120x120/0066cc/ffffff?text=${encodeURIComponent(item.name.split(' ')[0])}`;
                
                // Create unique item identifier (for variants)
                const itemIdentifier = item.variantId ? `${item.id}|${item.variantId}` : item.id;
                
                itemElement.innerHTML = `
                    <img src="${imageUrl}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        ${item.variantDescription ? `<div class="cart-item-variant">${item.variantDescription}</div>` : ''}
                        ${item.variantSku ? `<div class="cart-item-sku">SKU: ${item.variantSku}</div>` : ''}
                        <div class="cart-item-price">$ ${parseFloat(item.price).toFixed(2)}</div>
                        <div class="quantity-display">Quantity: ${item.quantity}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 600; margin-bottom: 1rem;">$ ${(parseFloat(item.price) * item.quantity).toFixed(2)}</div>
                        <button class="remove-btn" data-item-id="${itemIdentifier}">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                `;
                cartItemsContainer.appendChild(itemElement);
            });

            updateCartSummary();
        }

        /**
         * Update cart summary totals
         */
        function updateCartSummary() {
            const cart = getCartFromLocalStorage();
            const total = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);

            document.getElementById('total').textContent = 'USD ' + total.toFixed(2);
        }

        /**
         * Update item quantity
         */
        function updateQuantity(itemIdentifier, change, newValue) {
            let cart = getCartFromLocalStorage();
            let item;

            if (itemIdentifier.includes('|')) {
                // Variant product
                const [productId, variantId] = itemIdentifier.split('|');
                item = cart.find(i => i.id === productId && i.variantId === variantId);
            } else {
                // Single product
                item = cart.find(i => i.id === itemIdentifier && !i.variantId);
            }

            if (item) {
                if (newValue !== undefined) {
                    item.quantity = Math.max(1, parseInt(newValue) || 1);
                } else {
                    item.quantity += change;
                }

                if (item.quantity <= 0) {
                    removeFromCart(itemIdentifier);
                } else {
                    saveCartToLocalStorage(cart);
                    displayCartItems();
                }
            }
        }

        /**
         * Remove item from cart
         */
        function removeFromCart(itemIdentifier) {
            let cart = getCartFromLocalStorage();
            const originalLength = cart.length;
            
            if (itemIdentifier.includes('|')) {
                // Variant product
                const [productId, variantId] = itemIdentifier.split('|');
                cart = cart.filter(item => !(item.id === productId && item.variantId === variantId));
            } else {
                // Single product
                cart = cart.filter(item => !(item.id === itemIdentifier && !item.variantId));
            }
            
            if (cart.length < originalLength) {
                saveCartToLocalStorage(cart);
                displayCartItems();
                updateCartBadge();
                showNotification('Item removed from cart', 'success');
            }
        }

        /**
         * Navigate to checkout
         */
        function goToCheckout() {
            const cart = getCartFromLocalStorage();
            if (cart.length > 0) {
                window.location.href = '<?= ROOT.$storecode ?>/checkout';
            } else {
                showNotification('Please add items to your cart first', 'error');
            }
        }

        /**
         * Initialize cart page on load
         */
        document.addEventListener('DOMContentLoaded', function() {
            displayCartItems();
            updateCartBadge();
        });

        // Cart button navigation
        const cartBtn = document.getElementById('cartBtn');
        if (cartBtn) {
            cartBtn.addEventListener('click', function() {
                window.location.href = '<?= ROOT ?>cart';
            });
        }
    </script>

     <script>
        const stylesFromDB = {
            primary: "<?= $content['primary_color'] ?>",
        };
        
        const root = document.documentElement; // <html> element
        root.style.setProperty('--primary', stylesFromDB.primary);
    </script>
</body>

</html>
