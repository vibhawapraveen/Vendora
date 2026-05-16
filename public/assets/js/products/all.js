function deleteProduct(productId) {
  if (
    confirm(
      "Are you sure you want to delete this product? This action cannot be undone.",
    )
  ) {
    // Create form and submit
    const form = document.createElement("form");
    form.method = "POST";
    form.action = window.APP_CONFIG.BASE_URL + "delete";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "product_id";
    input.value = productId;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}

function editProduct(productId) {
  // Open modal and load product data
  openEditModal(productId);
}

// Auto-hide alerts after 5 seconds
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach(function (alert) {
    setTimeout(function () {
      alert.style.opacity = "0";
      alert.style.transition = "opacity 0.5s ease";
      setTimeout(function () {
        alert.remove();
      }, 500);
    }, 5000);
  });

  // Initialize product filtering
  initializeFilters();
});

// Product Filtering Functionality
function initializeFilters() {
  const searchInput = document.getElementById("search-input");
  const categoryFilter = document.getElementById("category-filter");
  const stockFilter = document.getElementById("stock-filter");
  const visibilityFilter = document.getElementById("visibility-filter");
  const clearFiltersBtn = document.getElementById("clear-filters-btn");
  const tableRows = document.querySelectorAll("tbody tr");

  // Store original row order
  const allRows = Array.from(tableRows);

  // Filter function
  function applyFilters() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const selectedCategory = categoryFilter.value.toLowerCase();
    const selectedStock = stockFilter.value.toLowerCase();
    const selectedVisibility = visibilityFilter.value.toLowerCase();

    let visibleCount = 0;

    allRows.forEach((row) => {
      // Skip the "no products" row if it exists
      if (row.querySelector("td[colspan]")) {
        return;
      }

      let show = true;

      // Search filter (product name)
      if (searchTerm) {
        const productName =
          row.querySelector("td:nth-child(1)")?.textContent.toLowerCase() || "";
        if (!productName.includes(searchTerm)) {
          show = false;
        }
      }

      // Category filter
      if (selectedCategory && show) {
        const rowCategory = (row.dataset.category || "").toLowerCase().trim();
        if (rowCategory !== selectedCategory) {
          show = false;
        }
      }

      // Stock status filter
      if (selectedStock && show) {
        const stockCell = row.querySelector("td:nth-child(6)");
        const stockQuantity = parseInt(
          stockCell?.textContent.replace(/,/g, "") || "0",
        );

        if (selectedStock === "in-stock" && stockQuantity <= 0) {
          show = false;
        } else if (
          selectedStock === "low-stock" &&
          (stockQuantity > 10 || stockQuantity <= 0)
        ) {
          show = false;
        } else if (selectedStock === "out-of-stock" && stockQuantity > 0) {
          show = false;
        }
      }

      // Visibility filter
      if (selectedVisibility && show) {
        const statusBadge = row.querySelector("td:nth-child(3) .badge");
        const isActive = statusBadge?.classList.contains("badge-success");

        if (selectedVisibility === "active" && !isActive) {
          show = false;
        } else if (selectedVisibility === "inactive" && isActive) {
          show = false;
        }
      }

      // Show or hide row
      if (show) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    });

    // Show "no results" message if no products match
    updateNoResultsMessage(visibleCount);

    // Show/hide clear filters button
    updateClearButtonVisibility();
  }

  // Update no results message
  function updateNoResultsMessage(visibleCount) {
    const tbody = document.querySelector("tbody");
    let noResultsRow = tbody.querySelector(".no-results-row");

    if (visibleCount === 0) {
      if (!noResultsRow) {
        noResultsRow = document.createElement("tr");
        noResultsRow.className = "no-results-row";
        noResultsRow.innerHTML = `
          <td colspan="8" class="px-6 py-4 text-center text-gray-500">
            <i class="fa-solid fa-search text-2xl mb-2" style="display: block;"></i>
            No products match your filters. Try adjusting your search criteria.
          </td>
        `;
        tbody.appendChild(noResultsRow);
      }
      noResultsRow.style.display = "";
    } else {
      if (noResultsRow) {
        noResultsRow.style.display = "none";
      }
    }
  }

  // Update clear button visibility
  function updateClearButtonVisibility() {
    const hasActiveFilters =
      searchInput.value.trim() !== "" ||
      categoryFilter.value !== "" ||
      stockFilter.value !== "" ||
      visibilityFilter.value !== "";

    clearFiltersBtn.style.display = hasActiveFilters ? "inline-flex" : "none";
  }

  // Clear all filters
  function clearFilters() {
    searchInput.value = "";
    categoryFilter.value = "";
    stockFilter.value = "";
    visibilityFilter.value = "";
    applyFilters();
  }

  // Event listeners
  searchInput.addEventListener("input", applyFilters);
  categoryFilter.addEventListener("change", applyFilters);
  stockFilter.addEventListener("change", applyFilters);
  visibilityFilter.addEventListener("change", applyFilters);
  clearFiltersBtn.addEventListener("click", clearFilters);

  // Initial state
  updateClearButtonVisibility();
}

// Quick Edit Modal Functions
function openEditModal(productId) {
  const modal = document.getElementById("edit-product-modal");
  const modalForm = document.getElementById("edit-product-form");
  const modalFooter = document.getElementById("modal-footer-actions");
  const loadingDiv = document.getElementById("edit-loading");
  const errorDiv = document.getElementById("edit-error");

  // Show modal
  modal.classList.add("active");

  // Show loading state
  loadingDiv.style.display = "block";
  modalForm.style.display = "none";
  modalFooter.style.display = "none";
  errorDiv.style.display = "none";

  // Fetch product data from server (for full variant info)
  fetch(`${window.APP_CONFIG.BASE_URL}${productId}/get`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Failed to fetch product data");
      }
      return response.json();
    })
    .then((data) => {
      if (data.error) {
        showEditError(data.error);
        return;
      }

      // Populate form with fetched data
      populateEditForm(productId, data);

      // Hide loading, show form
      loadingDiv.style.display = "none";
      modalForm.style.display = "block";
      modalFooter.style.display = "flex";
    })
    .catch((error) => {
      console.error("Error fetching product data:", error);
      showEditError("Failed to load product data. Please try again.");
    });
}

function populateEditForm(productId, data) {
  const product = data.product;
  const variants = data.variants || [];

  // Set product ID
  document.getElementById("edit-product-id").value = productId;
  document.getElementById("edit-is-variant").value = product.is_variant || "0";

  // Set basic fields
  document.getElementById("edit-name").value = product.name || "";
  document.getElementById("edit-visibility").checked = product.visibility == 1;

  // Handle single vs variant products
  const singleFields = document.getElementById("single-product-fields");
  const variantFields = document.getElementById("variant-product-fields");

  if (product.is_variant == 1 && variants.length > 0) {
    // Multi-variant product
    singleFields.style.display = "none";
    variantFields.style.display = "block";

    // Build variants HTML
    const variantsListDiv = document.getElementById("variants-list");
    let variantsHTML = "";

    variants.forEach((variant, index) => {
      // Build attribute display text
      let attributeText = "";
      if (variant.attributes && variant.attributes.length > 0) {
        attributeText = variant.attributes
          .map((attr) => `${attr.attribute_name}: ${attr.value_name}`)
          .join(" • ");
      } else if (variant.variant_name) {
        attributeText = variant.variant_name;
      }

      variantsHTML += `
        <div class="variant-item">
          <div class="variant-name">
            <i class="fa-solid fa-box"></i>
            <span>${attributeText || `Variant ${index + 1}`}</span>
          </div>
          <input type="hidden" name="variants[${variant.id}][id]" value="${variant.id}">
          <div class="variant-fields">
            <div>
              <label class="variant-field-label">
                <i class="fa-solid fa-dollar-sign" style="color: #16a34a;"></i> Price (USD)
              </label>
              <input 
                type="number" 
                name="variants[${variant.id}][price]" 
                class="input input-sm" 
                step="0.01" 
                min="0" 
                value="${variant.price || ""}" 
                placeholder="0.00"
              >
            </div>
            <div>
              <label class="variant-field-label">
                <i class="fa-solid fa-boxes-stacked" style="color: #f59e0b;"></i> Stock
              </label>
              <input 
                type="number" 
                name="variants[${variant.id}][stock]" 
                class="input input-sm" 
                min="0" 
                value="${variant.stock_quantity || 0}" 
                placeholder="0"
              >
            </div>
          </div>
        </div>
      `;
    });

    variantsListDiv.innerHTML = variantsHTML;
  } else {
    // Single product
    singleFields.style.display = "block";
    variantFields.style.display = "none";

    document.getElementById("edit-price").value = product.price || "";
    document.getElementById("edit-stock").value = product.stock_quantity || "";
  }

  // Setup full edit button
  const fullEditBtn = document.getElementById("full-edit-btn");
  fullEditBtn.onclick = function () {
    window.location.href = `${window.APP_CONFIG.BASE_URL}${productId}/edit`;
  };

  // Setup form submission
  const form = document.getElementById("edit-product-form");
  form.onsubmit = function (e) {
    e.preventDefault();
    handleQuickEdit(productId);
  };
}

function handleQuickEdit(productId) {
  const form = document.getElementById("edit-product-form");
  const formData = new FormData(form);

  // Show loading state on submit button
  const submitBtn = document.querySelector(
    "#modal-footer-actions .btn-primary",
  );
  const originalBtnText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML =
    '<i class=\"fa-solid fa-spinner fa-spin\"></i> Saving...';

  // Submit via fetch
  fetch(`${window.APP_CONFIG.BASE_URL}${productId}/edit`, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (response.redirected) {
        // If server redirected, follow it
        window.location.href = response.url;
      } else {
        return response.text();
      }
    })
    .then((data) => {
      // Reload page to show updated data
      window.location.reload();
    })
    .catch((error) => {
      console.error("Error updating product:", error);
      alert("Failed to update product. Please try again.");
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    });
}

function showEditError(message) {
  const errorDiv = document.getElementById("edit-error");
  const errorMessage = document.getElementById("edit-error-message");
  const loadingDiv = document.getElementById("edit-loading");

  errorMessage.textContent = message;
  errorDiv.style.display = "block";
  loadingDiv.style.display = "none";
}
