// ==================== CART MANAGEMENT ====================

/**
 * Get store code from page
 * @returns {string} Store code
 */
function getStoreCode() {
    return document.body.dataset.storecode || 'default';
}

/**
 * Get cart key for current store
 * @returns {string} Cart key
 */
function getCartKey() {
    return `cart-${getStoreCode()}`;
}

/**
 * Add product to cart (simple version - for single variant products)
 * @param {number} productId - Product ID
 * @param {string} productName - Product name
 * @param {number} productPrice - Product price
 * @param {number} quantity - Quantity to add (default: 1)
 * @param {string} imageUrl - Product image URL
 */
function addToCart(productId, productName, productPrice, quantity = 1, imageUrl = null) {
    let cart = getCartFromLocalStorage();
    
    // Check if product already exists in cart (single variant only)
    const existingItem = cart.find(item => item.id === productId && !item.variantId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: String(productPrice),
            quantity: quantity,
            variantId: null,
            variantSku: null,
            variantDescription: null,
            image: imageUrl
        });
    }
    
    saveCartToLocalStorage(cart);
    updateCartBadge();
    showNotification(`${productName} added to cart!`);
}

/**
 * Add product with variant to cart
 * @param {number} productId - Product ID
 * @param {string} productName - Product name
 * @param {number} productPrice - Product price
 * @param {number} quantity - Quantity to add
 * @param {number} variantId - Variant ID
 * @param {string} variantSku - Variant SKU
 * @param {string} variantDescription - Variant description (e.g., "Color: Red, Size: Large")
 * @param {string} variantImage - Variant image URL
 */
function addToCartWithVariant(productId, productName, productPrice, quantity, variantId, variantSku, variantDescription, variantImage) {
    let cart = getCartFromLocalStorage();
    
    // Check if this exact variant already exists in cart
    const existingItem = cart.find(item => 
        item.id === productId && item.variantId === variantId
    );
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: String(productPrice),
            quantity: quantity,
            variantId: variantId,
            variantSku: variantSku,
            variantDescription: variantDescription,
            image: variantImage
        });
    }
    
    saveCartToLocalStorage(cart);
    updateCartBadge();
}

/**
 * Get cart from localStorage
 * @returns {Array} Cart items
 */
function getCartFromLocalStorage() {
    return JSON.parse(localStorage.getItem(getCartKey())) || [];
}

/**
 * Save cart to localStorage
 * @param {Array} cart - Cart items
 */
function saveCartToLocalStorage(cart) {
    localStorage.setItem(getCartKey(), JSON.stringify(cart));
}

/**
 * Update cart badge count
 */
function updateCartBadge() {
    const cartBadge = document.getElementById('cartBadge');
    if (!cartBadge) return;
    
    const cart = getCartFromLocalStorage();
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    if (count > 0) {
        cartBadge.textContent = count;
        cartBadge.style.display = 'flex';
    } else {
        cartBadge.style.display = 'none';
    }
}

/**
 * Clear cart
 */
function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
        localStorage.removeItem(getCartKey());
        updateCartBadge();
        location.reload();
    }
}

// ==================== PRODUCT CAROUSEL ====================

/**
 * Change carousel image
 * @param {HTMLElement} element - Dot element clicked
 */
function changeImage(element) {
    const card = element.closest('.product-card');
    const imageIndex = parseInt(element.dataset.imageIndex);
    const images = card.querySelectorAll('.carousel-image');
    const dots = card.querySelectorAll('.carousel-dot');
    
    // Hide all images
    images.forEach(img => img.style.display = 'none');
    
    // Remove active class from all dots
    dots.forEach(dot => dot.classList.remove('active'));
    
    // Show selected image
    images[imageIndex].style.display = 'block';
    element.classList.add('active');
}

// ==================== NOTIFICATIONS ====================

/**
 * Show notification
 * @param {string} message - Notification message
 * @param {string} type - Notification type (success, error, info)
 */
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: ${getNotificationColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease;
        max-width: 400px;
        word-wrap: break-word;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Get notification color
 * @param {string} type - Notification type
 * @returns {string} Color value
 */
function getNotificationColor(type) {
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        info: '#0066cc',
        warning: '#ffc107'
    };
    return colors[type] || colors.info;
}

// ==================== FORM UTILITIES ====================

/**
 * Format card number input
 * @param {HTMLElement} input - Input element
 */
function formatCardNumber(input) {
    let value = input.value.replace(/\s/g, '');
    let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
    input.value = formattedValue;
}

/**
 * Format expiry date input
 * @param {HTMLElement} input - Input element
 */
function formatExpiryDate(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    input.value = value;
}

/**
 * Validate email
 * @param {string} email - Email address
 * @returns {boolean} Validation result
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number
 * @param {string} phone - Phone number
 * @returns {boolean} Validation result
 */
function validatePhone(phone) {
    const phoneRegex = /^[\d\s\-\+\(\)]{10,}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

// ==================== PAGE ANIMATIONS ====================

/**
 * Add CSS animations
 */
function addAnimations() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
}

// ==================== SEARCH AND FILTER ====================

/**
 * Search products
 * @param {string} query - Search query
 * @returns {Array} Filtered products
 */
function searchProducts(query) {
    const productCards = document.querySelectorAll('.product-card');
    query = query.toLowerCase();
    
    productCards.forEach(card => {
        const productName = card.querySelector('.product-name').textContent.toLowerCase();
        if (productName.includes(query)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

/**
 * Filter products by price
 * @param {number} minPrice - Minimum price
 * @param {number} maxPrice - Maximum price
 */
function filterByPrice(minPrice, maxPrice) {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const priceText = card.querySelector('.product-price').textContent;
        const price = parseFloat(priceText.replace('$', ''));
        
        if (price >= minPrice && price <= maxPrice) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// ==================== SMOOTH SCROLL ====================

/**
 * Smooth scroll to element
 * @param {string} elementId - Element ID
 */
function smoothScroll(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// ==================== LAZY LOADING ====================

/**
 * Setup lazy loading for images
 */
function setupLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));
    }
}

// ==================== PAGE INITIALIZATION ====================

/**
 * Initialize page on load
 */
function initializePage() {
    // Add animations
    addAnimations();
    
    // Update cart badge
    updateCartBadge();
    
    // Setup lazy loading
    setupLazyLoading();
    
    // Add click handlers for navigation
    setupNavigation();
}

/**
 * Setup navigation
 */
function setupNavigation() {
    // Cart button
    const cartBtn = document.getElementById('cartBtn');
    if (cartBtn) {
        cartBtn.addEventListener('click', function() {
            console.log(window.location.pathname.split("/")[3]);
            // window.location.href = '<?= ROOT.$storecode ?>/cart';
        });
    }
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Debounce function
 * @param {Function} func - Function to debounce
 * @param {number} delay - Delay in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func(...args), delay);
    };
}

/**
 * Throttle function
 * @param {Function} func - Function to throttle
 * @param {number} delay - Delay in milliseconds
 * @returns {Function} Throttled function
 */
function throttle(func, delay) {
    let lastCall = 0;
    return function(...args) {
        const now = Date.now();
        if (now - lastCall >= delay) {
            func(...args);
            lastCall = now;
        }
    };
}

/**
 * Format currency
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency
 */
function formatCurrency(amount) {
    return '$ ' + amount.toFixed(2);
}

/**
 * Get query parameter
 * @param {string} param - Parameter name
 * @returns {string} Parameter value
 */
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// ==================== EVENT LISTENERS ====================

// Initialize page when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePage);
} else {
    initializePage();
}

// Update cart badge on page visibility change
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        updateCartBadge();
    }
});
