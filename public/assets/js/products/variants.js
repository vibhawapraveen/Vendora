const ROOT = document.querySelector("#variantsForm").dataset.root;
const PRODUCT_ID = document.querySelector("#variantsForm").dataset.productId;

function regenerateVariants() {
  if (
    confirm(
      "This will reload the variants from attributes. Any unsaved changes will be lost. Continue?",
    )
  ) {
    window.location.reload();
  }
}

function applyBulkPrice() {
  const bulkPrice = document.getElementById("bulkPrice").value;
  if (bulkPrice) {
    const priceInputs = document.querySelectorAll(".price-input");
    priceInputs.forEach((input) => {
      input.value = bulkPrice;
    });
  }
}

function applyBulkStock() {
  const bulkStock = document.getElementById("bulkStock").value;
  if (bulkStock) {
    const stockInputs = document.querySelectorAll(".stock-input");
    stockInputs.forEach((input) => {
      input.value = bulkStock;
    });
  }
}

function applyBulkStockAlert() {
  const bulkStockAlert = document.getElementById("bulkStockAlert").value;
  if (bulkStockAlert) {
    const stockAlertInputs = document.querySelectorAll(".stock_alert-input");
    stockAlertInputs.forEach((input) => {
      input.value = bulkStockAlert;
    });
  }
}

function collectVariantsData() {
  const variants = [];
  const rows = document.querySelectorAll(".variant-row");

  rows.forEach((row) => {
    const index = row.dataset.index;
    const enabled = row.querySelector(".variant-enabled").checked;
    const sku = row.querySelector(".sku-input").value.trim();
    const price = parseFloat(row.querySelector(".price-input").value) || 0;
    const stock = parseInt(row.querySelector(".stock-input").value) || 0;
    const lowStockAlert =
      parseInt(row.querySelector(".stock_alert-input").value) || null;
    const attributeValueIds = JSON.parse(
      row.querySelector(".attribute-value-ids").value,
    );

    variants.push({
      enabled: enabled,
      sku: sku,
      price: price,
      stock: stock,
      low_stock_alert: lowStockAlert,
      attribute_value_ids: attributeValueIds,
    });
  });

  return variants;
}

function submitVariants(event) {
  event.preventDefault();

  // Collect variants data
  const variants = collectVariantsData();

  // Validate - at least one variant should be enabled
  const enabledVariants = variants.filter((v) => v.enabled);
  if (enabledVariants.length === 0) {
    alert("Please enable at least one variant");
    return;
  }

  // Validate prices and stock for enabled variants
  for (let variant of enabledVariants) {
    if (!variant.sku) {
      alert("Please enter SKU for all enabled variants");
      return;
    }
    if (variant.price <= 0) {
      alert(
        "Please enter valid price (greater than 0) for all enabled variants",
      );
      return;
    }
  }

  const data = { variants: variants };

  // Show loading state
  const submitBtn = document.querySelector(".btn-primary");
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

  // Send AJAX request
  fetch(ROOT + "dashboard/products/newproduct/" + PRODUCT_ID + "/variants", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Redirect to media page
        window.location.href = data.redirect_url;
      } else {
        alert("Error: " + data.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while saving variants");
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
}

document
  .getElementById("variantsForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    submitVariants(e);
  });
