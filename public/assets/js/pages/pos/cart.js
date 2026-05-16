document.addEventListener("DOMContentLoaded", () => {
  const cartItemsContainer = document.getElementById("cart-items");
  const subtotalEl = document.getElementById("cart-subtotal");
  const totalEl = document.getElementById("cart-total");
  const checkoutBtn = document.getElementById("checkout-btn");
  const clearBtn = document.getElementById("clear-cart");

  let cart = [];

  // Add to cart for non-variant products
  document.querySelectorAll(".add-to-cart-btn:not(.variant-btn)").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      const name = btn.dataset.name;
      const price = Number(btn.dataset.price);
      const maxStock = Number(btn.dataset.stock) || 0;
      const imgSrc = btn.closest(".pos-product-card")?.querySelector("img")?.src || "";

      if (!id || !name || isNaN(price)) {
        console.error("Invalid product data", btn.dataset);
        return;
      }

      const existingItem = cart.find((item) => item.id === id && !item.isVariant);

      if (existingItem) {
        if (existingItem.qty >= existingItem.maxStock) {
          alert(`Only ${existingItem.maxStock} item(s) available in stock.`);
          return;
        }
        existingItem.qty += 1;
      } else {
        if (maxStock <= 0) {
          alert("Item is out of stock.");
          return;
        }
        cart.push({ id, productId: id, name, price, qty: 1, img: imgSrc, isVariant: false, maxStock, variantDescription: null });
      }

      renderCart();
    });
  });

  // Listen for variant selection from variants.js
  document.addEventListener("variantSelected", (e) => {
    const variantData = e.detail;

    // For variants, use variant ID as the cart item ID
    const existingItem = cart.find(
      (item) => item.id === variantData.id && item.isVariant
    );

    if (existingItem) {
      if (existingItem.qty >= existingItem.maxStock) {
        alert(`Only ${existingItem.maxStock} item(s) available in stock.`);
        return;
      }
      existingItem.qty += 1;
    } else {
      if ((variantData.maxStock || 0) <= 0) {
        alert("Selected variant is out of stock.");
        return;
      }
      cart.push({
        id: variantData.id, // variant_id
        productId: variantData.productId, // original product_id
        name: variantData.name,
        price: variantData.price,
        qty: 1,
        img: variantData.img,
        variantDescription: variantData.variantDescription || null,
        maxStock: Number(variantData.maxStock) || 0,
        isVariant: true,
      });
    }

    renderCart();
  });

  function renderCart() {
    cartItemsContainer.innerHTML = "";

    if (cart.length === 0) {
      cartItemsContainer.innerHTML = `
        <div class="pos-cart-empty">
          <div class="pos-cart-empty-icon">🛒</div>
          <p>No items in cart</p>
          <span class="text-muted">Add products to get started</span>
        </div>
      `;
      checkoutBtn.disabled = true;
      updateTotals();
      return;
    }

    cart.forEach((item, index) => {
      const div = document.createElement("div");
      div.className = "cart-item";

      div.innerHTML = `
        <img src="${item.img}" alt="${item.name}" class="cart-item-img">
        <div class="cart-item-info">
          <strong>${item.name}</strong>
          <div class="cart-qty" role="group" aria-label="Quantity controls">
            <button class="decrease-btn" data-index="${index}" aria-label="Decrease quantity">−</button>
            <span aria-live="polite">${item.qty}</span>
            <button class="increase-btn" data-index="${index}" aria-label="Increase quantity" ${item.qty >= item.maxStock ? "disabled" : ""}>+</button>
          </div>
        </div>
        <div class="cart-item-right">
          <span class="item-price">$${(item.price * item.qty).toFixed(2)}</span>
          <button class="action-btn delete-btn remove-btn" data-index="${index}" aria-label="Remove item">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </div>
      `;

      cartItemsContainer.appendChild(div);
    });

    // Remove buttons
    document.querySelectorAll(".remove-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        const index = Number(btn.dataset.index); // ← Fixed: string → number
        cart.splice(index, 1);
        renderCart();
      });
    });

    // Increase quantity
    document.querySelectorAll(".increase-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        const index = Number(btn.dataset.index); // ← Fixed: string → number
        if (cart[index].qty >= cart[index].maxStock) {
          alert(`Only ${cart[index].maxStock} item(s) available in stock.`);
          return;
        }
        cart[index].qty += 1;
        renderCart();
      });
    });

    // Decrease quantity
    document.querySelectorAll(".decrease-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        const index = Number(btn.dataset.index); // ← Fixed: string → number
        if (cart[index].qty > 1) {
          cart[index].qty -= 1;
        } else {
          cart.splice(index, 1);
        }
        renderCart();
      });
    });

    checkoutBtn.disabled = false;
    updateTotals();
  }

  function updateTotals() {
    let subtotal = 0;
    cart.forEach((item) => {
      subtotal += item.price * item.qty;
    });

    subtotalEl.textContent = "$" + subtotal.toFixed(2);
    totalEl.textContent = "$" + subtotal.toFixed(2);
  }

  // Clear cart
  clearBtn.addEventListener("click", () => {
    cart = [];
    renderCart();
  });

  // Checkout
  checkoutBtn.addEventListener("click", (e) => {
    e.preventDefault();
    const form = checkoutBtn.closest("form"); // ← Fixed: null check
    if (!form) {
      console.error("Checkout button must be inside a <form> tag");
      return;
    }
    localStorage.setItem("posCart", JSON.stringify(cart));
    window.location.href = form.action;
  });
});