function getCart() {
  const storecode = document.body.dataset.storecode;

  const cart = localStorage.getItem("cart-" + storecode);
  return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
  const storecode = document.body.dataset.storecode;
  localStorage.setItem("cart-" + storecode, JSON.stringify(cart));
  updateCartCount();
}

function clearCart() {
  const storecode = document.body.dataset.storecode;
  localStorage.removeItem("cart-" + storecode);
  updateCartCount();
}

// Increase quantity on home page
function increaseQuantity(productId, maxStock) {
  const quantityInput = document.getElementById(`qty-${productId}`);
  let currentValue = parseInt(quantityInput.value) || 1;

  if (currentValue < maxStock) {
    quantityInput.value = currentValue + 1;
  }
}

// Decrease quantity on home page
function decreaseQuantity(productId) {
  const quantityInput = document.getElementById(`qty-${productId}`);
  let currentValue = parseInt(quantityInput.value) || 1;

  if (currentValue > 1) {
    quantityInput.value = currentValue - 1;
  }
}

function addToCart(productId, productName, unitPrice) {
  const storecode = document.body.dataset.storecode;

  let cart = getCart();

  const quantityInput = document.getElementById(`qty-${productId}`);
  const quantity = parseInt(quantityInput.value) || 1;

  const existingItem = cart.find((item) => item.id === productId);

  if (existingItem) {
    existingItem.quantity += quantity;
  } else {
    cart.push({
      id: productId,
      name: productName,
      price: unitPrice,
      quantity: quantity,
    });
  }

  saveCart(cart);
  quantityInput.value = 1;
}

// Update cart count in navbar
function updateCartCount() {
  const storecode = document.body.dataset.storecode;
  const cart = getCart();
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  const cartCountElement = document.getElementById("cart-count");
  console.log("Cart Count Element:", totalItems);
  if (cartCountElement) {
    cartCountElement.textContent = totalItems;
  }
}

function displayCart() {
  const storeCode = document.body.dataset.storecode;
  const cart = getCart();
  const cartItemsList = document.getElementById("cart-items-list");

  if (!cartItemsList) return;

  if (cart.length === 0) {
    cartItemsList.innerHTML =
      '<div class="empty-cart"><p>Your cart is empty</p></div>';
    document.getElementById("subtotal").textContent = "$ 0.00";
    document.getElementById("total").textContent = "$ 0.00";
    return;
  }

  let html = "";
  cart.forEach((item) => {
    const itemTotal = item.price * item.quantity;
    html += `
            <div class="cart-item">
                <div class="item-details">
                    <h3>${item.name} <span>x${item.quantity}</span></h3>
                    <p>$ ${(parseFloat(item.price)).toFixed(2)} each</p>
                <button class="remove-btn" onclick="removeFromCart('${
                  item.id
                }')">Remove</button>
                </div>
                <div>
                <div class="item-price">$ ${(parseFloat(itemTotal)).toFixed(2)}</div>
                </div>
            </div>
        `;
  });

  cartItemsList.innerHTML = html;

  const total = calculateTotal(cart);
  document.getElementById("subtotal").textContent =
    "$ " + (parseFloat(total)).toFixed(2);
  document.getElementById("total").textContent = "$ " + parseFloat(total).toFixed(2);
}

function removeFromCart(productId) {
  const storecode = document.body.dataset.storecode;
  let cart = getCart();
  cart = cart.filter((item) => item.id !== productId);
  saveCart(cart);
  displayCart();
}

function calculateTotal(cart) {
  return cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
}

document.addEventListener("DOMContentLoaded", updateCartCount);
