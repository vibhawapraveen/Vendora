// ==================== CHECKOUT PAGE LOGIC ====================

/**
 * Initialize checkout page
 */
function initCheckoutPage() {
    displayCheckoutSummary();
    updateCartBadge();
    setupCheckoutEventListeners();
}

/**
 * Display checkout summary
 */
function displayCheckoutSummary() {
    const cart = getCartFromLocalStorage();
    const summaryItems = document.getElementById('summaryItems');

    summaryItems.innerHTML = '';

    cart.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.style.marginBottom = '0.75rem';
        
        // Main item summary
        const summaryItem = document.createElement('div');
        summaryItem.className = 'summary-item';
        summaryItem.innerHTML = `
            <span>${item.name} x${item.quantity}</span>
            <span>$ ${(item.price * item.quantity).toFixed(2)}</span>
        `;
        itemElement.appendChild(summaryItem);
        
        // Variant description if exists
        if (item.variantDescription) {
            const variantDesc = document.createElement('div');
            variantDesc.style.fontSize = '0.85rem';
            variantDesc.style.color = 'var(--text-light)';
            variantDesc.style.marginTop = '0.25rem';
            variantDesc.style.marginLeft = '0';
            variantDesc.textContent = item.variantDescription;
            itemElement.appendChild(variantDesc);
        }
        
        // SKU if exists
        if (item.variantSku) {
            const skuElement = document.createElement('div');
            skuElement.style.fontSize = '0.75rem';
            skuElement.style.color = 'var(--text-light)';
            skuElement.style.fontFamily = 'monospace';
            skuElement.style.marginTop = '0.25rem';
            skuElement.textContent = `SKU: ${item.variantSku}`;
            itemElement.appendChild(skuElement);
        }
        
        summaryItems.appendChild(itemElement);
    });

    updateCheckoutTotal();
}

/**
 * Update checkout total
 */
function updateCheckoutTotal() {
    const cart = getCartFromLocalStorage();
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = subtotal * 0.1;
    const total = subtotal + tax;

    document.getElementById('checkoutSubtotal').textContent = '$ ' + subtotal.toFixed(2);
    document.getElementById('checkoutTax').textContent = '$ ' + tax.toFixed(2);
    document.getElementById('checkoutTotal').textContent = '$ ' + total.toFixed(2);
}

/**
 * Setup checkout event listeners
 */
function setupCheckoutEventListeners() {
    // Card number formatting
    const cardNumber = document.getElementById('cardNumber');
    if (cardNumber) {
        cardNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
            e.target.value = formattedValue;
        });
    }

    // Expiry date formatting
    const expiryDate = document.getElementById('expiryDate');
    if (expiryDate) {
        expiryDate.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }
}

/**
 * Handle checkout form submit
 */
function handleSubmit(event) {
    event.preventDefault();

    // Validate form
    const termsCheckbox = document.getElementById('terms');
    if (!termsCheckbox.checked) {
        alert('Please agree to the terms and conditions');
        return;
    }

    // Get form data
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const cart = getCartFromLocalStorage();

    // Combine first and last name for backend
    document.getElementById('name').value = `${firstName} ${lastName}`;
    
    // Convert cart to JSON and set as hidden field
    document.getElementById('cart').value = JSON.stringify(cart);

    // Submit form to backend
    document.getElementById('checkoutForm').submit();
}
