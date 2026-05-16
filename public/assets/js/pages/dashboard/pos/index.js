// Sample products data (replace with API call in production)
const products = [
  { id: 1, name: "Wireless Mouse", sku: "WM001", price: 29.99, stock: 15, category: "electronics", image: "https://images.unsplash.com/photo-1527814050087-3793815479db?w=300" },
  { id: 2, name: "USB Cable", sku: "UC001", price: 9.99, stock: 50, category: "electronics", image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300" },
  { id: 3, name: "T-Shirt", sku: "TS001", price: 19.99, stock: 30, category: "clothing", image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=300" },
  { id: 4, name: "Coffee Mug", sku: "CM001", price: 12.99, stock: 0, category: "accessories", image: "https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=300" },
  { id: 5, name: "Notebook", sku: "NB001", price: 5.99, stock: 100, category: "accessories", image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300" },
  { id: 6, name: "Headphones", sku: "HP001", price: 79.99, stock: 20, category: "electronics", image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300" },
  { id: 7, name: "Water Bottle", sku: "WB001", price: 15.99, stock: 45, category: "accessories", image: "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=300" },
  { id: 8, name: "Jeans", sku: "JN001", price: 49.99, stock: 25, category: "clothing", image: "https://images.unsplash.com/photo-1542272604-787c3835535d?w=300" },
];

let cart = [];
const TAX_RATE = 0.05;

// Initialize
document.addEventListener("DOMContentLoaded", () => {
  renderProducts(products);
  setupEventListeners();
  updateCart();
});

// Render products
function renderProducts(productsToRender) {
  const grid = document.getElementById("product-grid");
  grid.innerHTML = productsToRender.map(product => `
    <div class="pos-product-card ${product.stock === 0 ? 'out-of-stock' : ''}" data-id="${product.id}">
      <img src="${product.image}" alt="${product.name}" class="pos-product-image" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
      <div class="pos-product-name">${product.name}</div>
      <div class="pos-product-price">$${product.price.toFixed(2)}</div>
      <div class="pos-product-stock ${product.stock > 0 ? 'in-stock' : 'out-of-stock'}">
        ${product.stock > 0 ? `${product.stock} in stock` : 'Out of stock'}
      </div>
      <button class="btn btn-outline" onclick="addToCart(${product.id})" ${product.stock === 0 ? 'disabled' : ''}>
        ${product.stock === 0 ? 'Out of Stock' : 'Add to Cart'}
      </button>
    </div>
  `).join('');
}

// Add to cart
function addToCart(productId) {
  const product = products.find(p => p.id === productId);
  if (!product || product.stock === 0) return;

  const cartItem = cart.find(item => item.id === productId);
  
  if (cartItem) {
    if (cartItem.quantity < product.stock) {
      cartItem.quantity++;
      showToast(`Added another ${product.name} to cart`);
    } else {
      showToast(`Maximum stock reached for ${product.name}`, 'error');
      return;
    }
  } else {
    cart.push({ ...product, quantity: 1 });
    showToast(`${product.name} added to cart`);
  }
  
  updateCart();
}

// Remove from cart
function removeFromCart(productId) {
  cart = cart.filter(item => item.id !== productId);
  updateCart();
  showToast('Item removed from cart');
}

// Update quantity
function updateQuantity(productId, delta) {
  const cartItem = cart.find(item => item.id === productId);
  if (!cartItem) return;

  const product = products.find(p => p.id === productId);
  const newQuantity = cartItem.quantity + delta;

  if (newQuantity <= 0) {
    removeFromCart(productId);
  } else if (newQuantity <= product.stock) {
    cartItem.quantity = newQuantity;
    updateCart();
  } else {
    showToast('Maximum stock reached', 'error');
  }
}

// Update cart UI
function updateCart() {
  const cartItemsEl = document.getElementById("cart-items");
  const checkoutBtn = document.getElementById("checkout-btn");

  if (cart.length === 0) {
    cartItemsEl.innerHTML = `
      <div class="pos-cart-empty">
        <div class="pos-cart-empty-icon">🛒</div>
        <p>No items in cart</p>
        <span class="text-muted">Add products to get started</span>
      </div>
    `;
    checkoutBtn.disabled = true;
  } else {
    cartItemsEl.innerHTML = cart.map(item => `
      <div class="pos-cart-item">
        <img src="${item.image}" alt="${item.name}" class="pos-cart-item-image" onerror="this.src='https://via.placeholder.com/60?text=No+Image'">
        <div class="pos-cart-item-details">
          <div class="pos-cart-item-name">${item.name}</div>
          <div class="pos-cart-item-price">$${item.price.toFixed(2)} each</div>
          <div class="pos-cart-item-controls">
            <button class="pos-qty-btn" onclick="updateQuantity(${item.id}, -1)">−</button>
            <span class="pos-qty-value">${item.quantity}</span>
            <button class="pos-qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
          </div>
        </div>
        <div class="pos-cart-item-actions">
          <div class="pos-cart-item-subtotal">$${(item.price * item.quantity).toFixed(2)}</div>
          <button class="pos-remove-btn" onclick="removeFromCart(${item.id})">🗑️</button>
        </div>
      </div>
    `).join('');
    checkoutBtn.disabled = false;
  }

  updateTotals();
}

// Update totals
function updateTotals() {
  const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const tax = subtotal * TAX_RATE;
  const total = subtotal + tax;

  document.getElementById("cart-subtotal").textContent = `$${subtotal.toFixed(2)}`;
  document.getElementById("cart-tax").textContent = `$${tax.toFixed(2)}`;
  document.getElementById("cart-total").textContent = `$${total.toFixed(2)}`;
}

// Setup event listeners
function setupEventListeners() {
  // Search
  document.getElementById("product-search").addEventListener("input", (e) => {
    const searchTerm = e.target.value.toLowerCase();
    const category = document.getElementById("category-filter").value;
    filterProducts(searchTerm, category);
  });

  // Category filter
  document.getElementById("category-filter").addEventListener("change", (e) => {
    const searchTerm = document.getElementById("product-search").value.toLowerCase();
    const category = e.target.value;
    filterProducts(searchTerm, category);
  });

  // Clear cart
  document.getElementById("clear-cart").addEventListener("click", () => {
    if (cart.length === 0) return;
    if (confirm("Are you sure you want to clear the cart?")) {
      cart = [];
      updateCart();
      showToast("Cart cleared");
    }
  });

  // Checkout
  document.getElementById("checkout-btn").addEventListener("click", openCheckout);

  // Payment method selection
  document.querySelectorAll('.pos-payment-option').forEach(option => {
    option.addEventListener('click', function() {
      document.querySelectorAll('.pos-payment-option').forEach(opt => opt.classList.remove('selected'));
      this.classList.add('selected');
    });
  });

  // Complete sale
  document.getElementById("complete-sale-btn").addEventListener("click", completeSale);
}

// Filter products
function filterProducts(searchTerm, category) {
  let filtered = products;

  if (category !== "all") {
    filtered = filtered.filter(p => p.category === category);
  }

  if (searchTerm) {
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(searchTerm) || 
      p.sku.toLowerCase().includes(searchTerm)
    );
  }

  renderProducts(filtered);
}

// Open checkout modal
function openCheckout() {
  const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
  const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0) * (1 + TAX_RATE);
  
  document.getElementById("modal-payment-method").textContent = paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1);
  document.getElementById("modal-items-count").textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
  document.getElementById("modal-total-amount").textContent = `$${total.toFixed(2)}`;
  
  openModal("checkout-modal");
}

// Complete sale
function completeSale() {
  const buyerName = document.getElementById("buyer-name").value;
  const buyerContact = document.getElementById("buyer-contact").value;
  const paymentMethod = document.querySelector('input[name="payment"]:checked').value;

  // In production, send this data to backend
  const saleData = {
    items: cart,
    buyerName,
    buyerContact,
    paymentMethod,
    subtotal: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
    tax: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0) * TAX_RATE,
    total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0) * (1 + TAX_RATE),
    timestamp: new Date().toISOString()
  };

  console.log("Sale completed:", saleData);

  // Clear cart and close modal
  cart = [];
  updateCart();
  closeModal("checkout-modal");
  document.getElementById("buyer-name").value = "";
  document.getElementById("buyer-contact").value = "";
  
  showToast("Sale completed successfully! 🎉");
}
