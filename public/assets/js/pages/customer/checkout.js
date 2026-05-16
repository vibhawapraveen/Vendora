async function initCheckoutPage() {
  const isValid = await validateCheckoutItems();

  if (!isValid) return;

  displayCheckoutSummary();
  setupCheckoutEventListeners();
}

async function validateCheckoutItems() {
  const res = await fetch(ROOT + getStoreCode() + "/cart/validate", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(getCartFromLocalStorage()),
  });

  if (!res.ok) {
    console.error("Validation failed:", res.status);
    return false;
  } else {
    const data = await res.json();
    console.log(data);
    updateCart(data.cart);
    return true;
  }
}

function getStoreCode() {
  return document.body.dataset.storecode;
}

function getCartFromLocalStorage() {
  return (
    JSON.parse(
      localStorage.getItem(`cart-${document.body.dataset.storecode}`),
    ) || []
  );
}

function updateCart($newCart) {
  localStorage.setItem(`cart-${getStoreCode()}`, JSON.stringify($newCart));
}

function displayCheckoutSummary() {
  const cart = getCartFromLocalStorage();

  const summaryItems = document.getElementById("summaryItems");
  summaryItems.innerHTML = "";

  cart.forEach((item) => {
    const itemElement = document.createElement("div");
    itemElement.style.marginBottom = "0.75rem";

    // Main item summary
    const summaryItem = document.createElement("div");
    summaryItem.className = "summary-item";
    summaryItem.innerHTML = `
            <span>${item.name} x${item.quantity}</span>
            <span>$ ${(item.price * item.quantity).toFixed(2)}</span>
        `;
    itemElement.appendChild(summaryItem);

    // Variant description if exists
    if (item.variantDescription) {
      const variantDesc = document.createElement("div");
      variantDesc.style.fontSize = "0.85rem";
      variantDesc.style.color = "var(--text-light)";
      variantDesc.style.marginTop = "0.25rem";
      variantDesc.style.marginLeft = "0";
      variantDesc.textContent = item.variantDescription;
      itemElement.appendChild(variantDesc);
    }

    // SKU if exists
    if (item.variantSku) {
      const skuElement = document.createElement("div");
      skuElement.style.fontSize = "0.75rem";
      skuElement.style.color = "var(--text-light)";
      skuElement.style.fontFamily = "monospace";
      skuElement.style.marginTop = "0.25rem";
      skuElement.textContent = `SKU: ${item.variantSku}`;
      itemElement.appendChild(skuElement);
    }

    summaryItems.appendChild(itemElement);
  });
  updateCheckoutTotal();
  updateHidenInput();
}

function updateHidenInput(){
  const hiddenCart = document.getElementById("hiddenCart");
  hiddenCart.value = JSON.stringify(getCartFromLocalStorage());
}

function updateCheckoutTotal() {
    const cart = getCartFromLocalStorage();
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    document.getElementById('checkoutTotal').textContent = '$ ' + total.toFixed(2);
}

function setupCheckoutEventListeners() {
  const form = document.getElementById('checkoutForm');
  if (form) {
    form.addEventListener('submit', handleCheckoutSubmit);
  }
}

async function handleCheckoutSubmit(event) {
  event.preventDefault();

  // Show loading state
  const submitBtn = event.target.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

  try {
    const formData = new FormData(event.target);
    
    const response = await fetch(event.target.action, {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.error) {
      throw new Error(data.error);
    }

    if (data.checkout_url) {
      // Redirect to Stripe checkout
      window.location.href = data.checkout_url;
    }

  } catch (error) {
    console.error('Checkout error:', error);
    alert('Payment error: ' + error.message);
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  }
}

function destroyCart() {}
