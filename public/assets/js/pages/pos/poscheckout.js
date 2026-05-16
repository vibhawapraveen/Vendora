// Toggle customer forms
const customerType = document.getElementById("customer-type");
const newForm = document.getElementById("new-customer-form");
const oldForm = document.getElementById("old-customer-search");
const searchInput = document.getElementById("search-customer");
const resultsDiv = document.getElementById("customer-results");
const selectedCustomerDiv = document.getElementById(
  "selected-customer-details",
);
const confirmCheckoutBtn = document.getElementById("confirm-checkout");

const checkoutEndpoint = (window.ROOT || "") + "dashboard/pos/poscheckout";
const customerSearchEndpoint = checkoutEndpoint + "?action=search-customer";

let selectedExistingCustomer = null;
let activeSearchRequest = 0;

function sanitizeMobileInput(value) {
  return (value || "").replace(/\D/g, "").slice(0, 10);
}

function isValidMobile(value) {
  return /^\d{10}$/.test(value || "");
}

const newCustomerMobileInput = document.getElementById("customer-mobile");
if (newCustomerMobileInput) {
  newCustomerMobileInput.addEventListener("input", () => {
    const cleaned = sanitizeMobileInput(newCustomerMobileInput.value);
    if (newCustomerMobileInput.value !== cleaned) {
      newCustomerMobileInput.value = cleaned;
    }
  });
}

if (searchInput) {
  searchInput.addEventListener("input", () => {
    const cleaned = sanitizeMobileInput(searchInput.value);
    if (searchInput.value !== cleaned) {
      searchInput.value = cleaned;
    }
  });
}

customerType.addEventListener("change", () => {
  if (customerType.value === "new") {
    newForm.style.display = "block";
    oldForm.style.display = "none";
    if (searchInput) searchInput.value = "";
    if (resultsDiv) {
      resultsDiv.innerHTML = "";
      resultsDiv.style.display = "none";
    }
    if (selectedCustomerDiv) {
      selectedCustomerDiv.style.display = "none";
      selectedCustomerDiv.innerHTML = "";
    }
    selectedExistingCustomer = null;
  } else {
    newForm.style.display = "none";
    oldForm.style.display = "block";
    if (resultsDiv) {
      resultsDiv.innerHTML = "";
      resultsDiv.style.display = "none";
    }
  }
});

searchInput.addEventListener("input", async () => {
  const query = searchInput.value;
  resultsDiv.innerHTML = "";
  resultsDiv.style.display = "none";
  selectedCustomerDiv.style.display = "none";
  selectedCustomerDiv.innerHTML = "";
  selectedExistingCustomer = null;

  if (!query) {
    return;
  }

  const currentRequestId = ++activeSearchRequest;
  resultsDiv.style.display = "block";
  resultsDiv.innerHTML = "<p>Searching customers...</p>";

  try {
    const response = await fetch(
      `${customerSearchEndpoint}&q=${encodeURIComponent(query)}`,
      {
        method: "GET",
        headers: {
          Accept: "application/json",
        },
      },
    );

    const result = await response.json();

    if (currentRequestId !== activeSearchRequest) {
      return;
    }

    if (!response.ok || !result.success) {
      throw new Error(result.message || "Failed to search customers.");
    }

    const matches = Array.isArray(result.customers) ? result.customers : [];
    resultsDiv.innerHTML = "";

    matches.forEach((c) => {
      const div = document.createElement("div");
      div.className = "customer-result";
      div.textContent = `${c.mobile} - ${c.name}`;
      div.addEventListener("click", () => {
        selectedExistingCustomer = c;
        searchInput.value = c.mobile;
        selectedCustomerDiv.innerHTML = `
          <div class="customer-header">
            <div class="customer-avatar">${(c.name || "?").charAt(0)}</div>
            <h2>${c.name || "Customer"}</h2>
          </div>
          <div><strong>Mobile:</strong> ${c.mobile || "-"}</div>
          <div><strong>Address:</strong> ${c.address1 || "-"}${c.city ? `, ${c.city}` : ""}</div>
        `;
        selectedCustomerDiv.style.display = "block";
        resultsDiv.style.display = "none";
      });
      resultsDiv.appendChild(div);
    });

    if (matches.length === 0) {
      resultsDiv.innerHTML =
        "<p>No matching customers found for this mobile number.</p>";
    }
  } catch (error) {
    if (currentRequestId !== activeSearchRequest) {
      return;
    }
    resultsDiv.innerHTML = `<p>${error.message || "Unable to search customers."}</p>`;
  }
});

// Load cart from localStorage
let cart = JSON.parse(localStorage.getItem("posCart")) || [];
const cartItemsContainer = document.getElementById("cart-items");
const subtotalEl = document.getElementById("cart-subtotal");
const totalEl = document.getElementById("cart-total");
const clearBtn = document.getElementById("clear-cart");

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
    subtotalEl.textContent = "$0.00";
    totalEl.textContent = "$0.00";
    clearBtn.disabled = true; // Disable clear button
    return;
  }

  let subtotal = 0;

  cart.forEach((item, index) => {
    subtotal += item.price * item.qty;
    const div = document.createElement("div");
    div.className = "cart-item";
    div.innerHTML = `
      <img src="${item.img || ""}" alt="${item.name}" class="cart-item-img">
      <div class="cart-item-info">
        <strong>${item.name}</strong>
        <div class="cart-qty" role="group" aria-label="Quantity controls">
          <button class="decrease-btn" data-index="${index}" aria-label="Decrease quantity">−</button>
          <span aria-live="polite">${item.qty}</span>
          <button class="increase-btn" data-index="${index}" aria-label="Increase quantity">+</button>
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
  cartItemsContainer.querySelectorAll(".remove-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const index = btn.dataset.index;
      cart.splice(index, 1);
      localStorage.setItem("posCart", JSON.stringify(cart));
      renderCart();
    });
  });

  // Increase quantity
  cartItemsContainer.querySelectorAll(".increase-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const index = btn.dataset.index;
      cart[index].qty += 1;
      localStorage.setItem("posCart", JSON.stringify(cart));
      renderCart();
    });
  });

  // Decrease quantity
  cartItemsContainer.querySelectorAll(".decrease-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const index = btn.dataset.index;
      if (cart[index].qty > 1) {
        cart[index].qty -= 1;
      } else {
        cart.splice(index, 1);
      }
      localStorage.setItem("posCart", JSON.stringify(cart));
      renderCart();
    });
  });

  subtotalEl.textContent = "$" + subtotal.toFixed(2);
  totalEl.textContent = "$" + subtotal.toFixed(2);
  clearBtn.disabled = false; // Enable clear button
}

// Clear cart functionality
clearBtn.addEventListener("click", () => {
  cart = [];
  localStorage.setItem("posCart", JSON.stringify(cart));
  renderCart();
});

// Call on load
if (cartItemsContainer) {
  renderCart();
}

/** Simple modern toast (replaces alert/localhost says) */
function showToast(message, type = "info") {
  const toast = document.createElement("div");
  toast.className = `pos-toast pos-toast--${type}`;
  toast.setAttribute("role", "status");
  toast.setAttribute("aria-live", "polite");
  toast.textContent = message;

  document.body.appendChild(toast);

  requestAnimationFrame(() => toast.classList.add("is-show"));

  const t = setTimeout(() => {
    toast.classList.remove("is-show");
    setTimeout(() => toast.remove(), 200);
  }, 2600);

  toast.addEventListener("click", () => {
    clearTimeout(t);
    toast.classList.remove("is-show");
    setTimeout(() => toast.remove(), 200);
  });
}

(function () {
  const modalOverlay = document.getElementById("payment-modal");
  if (!modalOverlay) return;

  const closeBtn = document.getElementById("payment-modal-close");
  const doneBtn = document.getElementById("payment-modal-done");
  const printBtn = document.getElementById("payment-modal-print");

  const pmCustomer = document.getElementById("pm-customer");
  const pmTotal = document.getElementById("pm-total");
  const pmTime = document.getElementById("pm-time");

  // NEW receipt fields
  const pmMobile = document.getElementById("pm-mobile");
  const pmSubtotal = document.getElementById("pm-subtotal");
  const pmItems = document.getElementById("pm-items");
  const pmReceiptNo = document.getElementById("pm-receipt-no");
  const pmCashier = document.getElementById("pm-cashier");
  const pmPaymentMethod = document.getElementById("pm-payment-method");

  let afterDoneRedirectUrl = null;

  function money(n) {
    const num = Number(n || 0);
    return `$${num.toFixed(2)}`;
  }

  function openPaymentModal({
    customerName = "Walk-in",
    customerMobile = "—",
    total = null, // number
    subtotal = null, // number
    totalText = null, // fallback string
    items = [], // [{ name, qty, price, variantDescription }]
    receiptNo = "—",
    cashier = "—",
    paymentMethod = "Cash",
    redirectUrl = null,
  } = {}) {
    pmCustomer.textContent = customerName || "Walk-in";
    if (pmMobile) pmMobile.textContent = customerMobile || "—";

    pmTime.textContent = new Date().toLocaleString();
    if (pmReceiptNo) pmReceiptNo.textContent = receiptNo || "—";
    if (pmCashier) pmCashier.textContent = cashier || "—";
    if (pmPaymentMethod) pmPaymentMethod.textContent = paymentMethod || "Cash";

    // totals
    if (typeof subtotal === "number" && pmSubtotal)
      pmSubtotal.textContent = money(subtotal);
    if (typeof total === "number") {
      pmTotal.textContent = money(total);
    } else {
      pmTotal.textContent = totalText || "$0.00";
    }

    // items
    if (pmItems) {
      pmItems.innerHTML = "";
      const safeItems = Array.isArray(items) ? items : [];

      if (safeItems.length === 0) {
        pmItems.innerHTML = `<div class="receipt-item-row">
          <div>
            <div class="receipt-item-name">—</div>
            <div class="receipt-item-sub">No items</div>
          </div>
          <div class="receipt-item-price">$0.00</div>
        </div>`;
      } else {
        safeItems.forEach((it) => {
          const lineTotal =
            Number(it.price || 0) * Number(it.qty || it.quantity || 0);
          const qty = Number(it.qty || it.quantity || 0);

          const row = document.createElement("div");
          row.className = "receipt-item-row";
          row.innerHTML = `
            <div>
              <div class="receipt-item-name">${it.name || "Item"}</div>
              <div class="receipt-item-sub">
                ${qty} × ${money(it.price)}${it.variantDescription ? ` • ${it.variantDescription}` : ""}
              </div>
            </div>
            <div class="receipt-item-price">${money(lineTotal)}</div>
          `;
          pmItems.appendChild(row);
        });
      }
    }

    afterDoneRedirectUrl = redirectUrl;

    modalOverlay.classList.add("is-open");
    modalOverlay.setAttribute("aria-hidden", "false");
    doneBtn?.focus();
  }

  function closePaymentModal() {
    modalOverlay.classList.remove("is-open");
    modalOverlay.setAttribute("aria-hidden", "true");

    if (afterDoneRedirectUrl) {
      window.location.href = afterDoneRedirectUrl;
    }
  }

  closeBtn?.addEventListener("click", closePaymentModal);
  doneBtn?.addEventListener("click", closePaymentModal);

  modalOverlay.addEventListener("click", (e) => {
    if (e.target === modalOverlay) closePaymentModal();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modalOverlay.classList.contains("is-open")) {
      closePaymentModal();
    }
  });

  // Print: prints receipt only because of @media print CSS
  printBtn?.addEventListener("click", () => window.print());

  window.openPaymentModal = openPaymentModal;
})();

// Confirm Payment Button (ONLY handler)
confirmCheckoutBtn.addEventListener("click", async (event) => {
  event.preventDefault();

  if (cart.length === 0) {
    showToast("Cart is empty!", "error");
    return;
  }

  if (customerType.value === "new") {
    const mobile = sanitizeMobileInput(
      document.getElementById("customer-mobile").value.trim(),
    );
    const name = document.getElementById("customer-name").value.trim();

    if (!mobile || !name) {
      showToast(
        "For new customers, Mobile Number and Name are required.",
        "error",
      );
      return;
    }

    if (!isValidMobile(mobile)) {
      showToast("Mobile Number must contain exactly 10 digits.", "error");
      return;
    }
  } else {
    const mobile = sanitizeMobileInput(searchInput.value.trim());

    if (!mobile) {
      showToast("For existing customers, Mobile Number is required.", "error");
      return;
    }

    if (!isValidMobile(mobile)) {
      showToast("Mobile Number must contain exactly 10 digits.", "error");
      return;
    }

    if (
      !selectedExistingCustomer ||
      selectedExistingCustomer.mobile !== mobile
    ) {
      showToast(
        "Please select an existing customer from the search results.",
        "error",
      );
      return;
    }
  }

  const customerPayload =
    customerType.value === "new"
      ? {
          type: "new",
          mobile: sanitizeMobileInput(
            document.getElementById("customer-mobile").value.trim(),
          ),
          name: document.getElementById("customer-name").value.trim(),
          address1: document.getElementById("customer-address1").value.trim(),
          address2: document.getElementById("customer-address2").value.trim(),
          city: document.getElementById("customer-city").value.trim(),
        }
      : {
          type: "existing",
          mobile: sanitizeMobileInput(searchInput.value.trim()),
          name: selectedExistingCustomer?.name || "",
        };

  const payloadCart = cart.map((item) => ({
    id: item.productId || item.id,
    productId: item.productId || item.id,
    variantId: item.isVariant ? item.id : null,
    name: item.name,
    variantDescription: item.variantDescription || null,
    price: item.price,
    quantity: item.qty,
  }));

  confirmCheckoutBtn.disabled = true;

  try {
    const response = await fetch(checkoutEndpoint, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        customer_type: customerType.value,
        customer: customerPayload,
        cart: payloadCart,
      }),
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || "Failed to create POS order.");
    }

    // Success: clear cart + show modal
    localStorage.removeItem("posCart");

    const customerName =
      customerType.value === "new"
        ? document.getElementById("customer-name")?.value?.trim() || "Walk-in"
        : selectedExistingCustomer?.name || "Walk-in";

    const customerMobile =
      customerType.value === "new"
        ? sanitizeMobileInput(
            document.getElementById("customer-mobile")?.value?.trim(),
          )
        : selectedExistingCustomer?.mobile ||
          sanitizeMobileInput(searchInput?.value?.trim());

    // compute totals from cart
    const subtotal = cart.reduce(
      (sum, it) => sum + Number(it.price || 0) * Number(it.qty || 0),
      0,
    );

    window.openPaymentModal?.({
      customerName,
      customerMobile,
      items: cart.map((it) => ({
        name: it.name,
        qty: it.qty,
        price: it.price,
        variantDescription: it.variantDescription || null,
      })),
      subtotal,
      total: subtotal,
      receiptNo:
        result?.order_no || result?.orderId || result?.receipt_no || "—",
      cashier: result?.cashier || "—",
      paymentMethod: result?.payment_method || "Cash",
      redirectUrl: (window.ROOT || "") + "dashboard/pos",
    });

    // Also re-render cart UI immediately
    cart = [];
    localStorage.setItem("posCart", JSON.stringify(cart));
    renderCart();
  } catch (error) {
    showToast(error.message || "Unable to complete checkout.", "error");
  } finally {
    confirmCheckoutBtn.disabled = false;
  }
});
