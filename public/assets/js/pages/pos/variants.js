document.addEventListener("DOMContentLoaded", () => {
  const variantModal = document.getElementById("variant-modal");
  const variantModalClose = document.getElementById("variant-modal-close");
  const variantModalCancel = document.getElementById("variant-modal-cancel");
  const variantModalConfirm = document.getElementById("variant-modal-confirm");
  const variantModalTitle = document.getElementById("variant-modal-title");
  const variantModalBody = document.getElementById("variant-modal-body");

  let currentProductData = null;
  let selectedVariant = null;

  function buildVariantImageUrl(variantImagePath, fallbackImage) {
    if (!variantImagePath || !variantImagePath.trim()) {
      return fallbackImage;
    }

    const cleaned = variantImagePath.trim();

    // If already an absolute URL, use it directly.
    if (/^https?:\/\//i.test(cleaned)) {
      return cleaned;
    }

    // ROOT already points to app base path, image path is usually relative (e.g., uploads/...).
    return (window.ROOT || "") + cleaned;
  }

  // Open variant selection modal
  document.querySelectorAll(".variant-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const productId = btn.dataset.id;
      const productName = btn.dataset.name;
      const variants = JSON.parse(btn.dataset.variants);
      const attributes = JSON.parse(btn.dataset.attributes);

      currentProductData = {
        id: productId,
        name: productName,
        variants: variants,
        attributes: attributes,
        imgSrc: btn.closest(".pos-product-card")?.querySelector("img")?.src || "",
      };

      selectedVariant = null;
      variantModalConfirm.disabled = true;

      // Set modal title
      variantModalTitle.textContent = `${productName} - Select Options`;

      // Build variant selection UI
      buildVariantUI(attributes, variants);

      // Show modal
      variantModal.style.display = "flex";
    });
  });

  function buildVariantUI(attributes, variants) {
    variantModalBody.innerHTML = "";

    if (!attributes || attributes.length === 0) {
      variantModalBody.innerHTML = "<p>No options available</p>";
      return;
    }

    // Create container for attribute selectors
    const selectorsContainer = document.createElement("div");
    selectorsContainer.className = "variant-selectors-container";

    // Create attribute selectors
    attributes.forEach((attr) => {
      const values = attr.attribute_values ? attr.attribute_values.split(",") : [];
      const valueIds = attr.value_ids ? attr.value_ids.split(",") : [];

      const attrGroup = document.createElement("div");
      attrGroup.className = "variant-attr-group";

      const label = document.createElement("label");
      label.className = "variant-attr-label";
      label.textContent = attr.name;

      const optionsContainer = document.createElement("div");
      optionsContainer.className = "variant-attr-options";

      values.forEach((value, index) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "variant-option-btn";
        btn.textContent = value;
        btn.dataset.attrName = attr.name;
        btn.dataset.attrValue = value;
        btn.dataset.attrId = valueIds[index];

        btn.addEventListener("click", (e) => {
          e.preventDefault();
          // Remove previous selection in this group
          optionsContainer
            .querySelectorAll(".variant-option-btn.active")
            .forEach((b) => b.classList.remove("active"));
          btn.classList.add("active");

          // Check variant availability
          checkVariantSelection();
        });

        optionsContainer.appendChild(btn);
      });

      attrGroup.appendChild(label);
      attrGroup.appendChild(optionsContainer);
      selectorsContainer.appendChild(attrGroup);
    });

    variantModalBody.appendChild(selectorsContainer);

    // Create selected variant info container (hidden by default)
    const selectedInfoContainer = document.createElement("div");
    selectedInfoContainer.id = "selected-variant-info";
    selectedInfoContainer.className = "selected-variant-info";
    selectedInfoContainer.style.display = "none";
    selectedInfoContainer.innerHTML = `
      <div class="variant-info-image">
        <img id="variant-preview-img" src="" alt="Variant image" onerror="this.src = this.dataset.fallback || ''; console.log('Image failed to load:', this.src);">
      </div>
      <div class="variant-info-details">
        <p><strong>Selected:</strong> <span id="variant-label"></span></p>
        <p><strong>Price:</strong> $<span id="variant-price"></span></p>
        <p><strong>Stock Available:</strong> <span id="variant-stock"></span></p>
      </div>
    `;
    variantModalBody.appendChild(selectedInfoContainer);
  }

  function checkVariantSelection() {
    const selectedOptions = {};
    const allButtons = variantModalBody.querySelectorAll(".variant-option-btn.active");
    
    allButtons.forEach((btn) => {
      selectedOptions[btn.dataset.attrName] = btn.dataset.attrValue;
    });

    const selectedInfoContainer = document.getElementById("selected-variant-info");

    // If no options selected, hide info
    if (Object.keys(selectedOptions).length === 0) {
      selectedInfoContainer.style.display = "none";
      variantModalConfirm.disabled = true;
      selectedVariant = null;
      return;
    }

    // Match selected options to find the correct variant
    selectedVariant = null;
    
    for (let variant of currentProductData.variants) {
      if (!variant.attributes) continue;
      
      // Parse variant attributes: "Storage:512GB|Color:Silver"
      const variantAttrs = {};
      const attrPairs = variant.attributes.split("|");
      
      attrPairs.forEach((pair) => {
        const [name, value] = pair.split(":");
        if (name && value) {
          variantAttrs[name.trim()] = value.trim();
        }
      });

      // Check if this variant matches all selected options
      let matches = true;
      for (let [attrName, attrValue] of Object.entries(selectedOptions)) {
        if (variantAttrs[attrName] !== attrValue) {
          matches = false;
          break;
        }
      }

      // If matches and has stock, use this variant
      if (matches && variant.stock_quantity > 0) {
        selectedVariant = variant;
        break;
      }
    }

    if (selectedVariant) {
      // Update variant info display
      document.getElementById("variant-label").textContent = 
        selectedVariant.variant_label || selectedVariant.sku || "N/A";
      document.getElementById("variant-price").textContent = 
        parseFloat(selectedVariant.price).toFixed(2);
      document.getElementById("variant-stock").textContent = 
        selectedVariant.stock_quantity;

      // Update image if variant has its own image
      const variantImg = document.getElementById("variant-preview-img");
      
      // Set the fallback to product image
      variantImg.dataset.fallback = currentProductData.imgSrc;

      const previewImage = buildVariantImageUrl(selectedVariant.image, currentProductData.imgSrc);
      variantImg.src = previewImage;

      // Show info container
      selectedInfoContainer.style.display = "block";
      variantModalConfirm.disabled = false;
    } else {
      // No matching variant with stock found
      selectedInfoContainer.style.display = "none";
      variantModalConfirm.disabled = true;
      selectedVariant = null;
    }
  }

  // Close modal handlers
  variantModalClose.addEventListener("click", () => {
    variantModal.style.display = "none";
  });

  variantModalCancel.addEventListener("click", () => {
    variantModal.style.display = "none";
  });

  // Confirm variant selection
  variantModalConfirm.addEventListener("click", () => {
    if (!selectedVariant || !currentProductData) return;

    const selectedVariantImage = buildVariantImageUrl(
      selectedVariant.image,
      currentProductData.imgSrc,
    );

    // Dispatch custom event to cart.js with variant data
    const event = new CustomEvent("variantSelected", {
      detail: {
        id: selectedVariant.id,
        productId: currentProductData.id,
        name:
          currentProductData.name +
          " (" +
          (selectedVariant.variant_label || selectedVariant.sku) +
          ")",
        price: parseFloat(selectedVariant.price),
        img: selectedVariantImage,
        variantDescription: selectedVariant.variant_label || selectedVariant.sku || "",
        maxStock: Number(selectedVariant.stock_quantity) || 0,
        isVariant: true,
      },
    });

    document.dispatchEvent(event);
    variantModal.style.display = "none";
  });

  // Close modal when clicking outside
  variantModal.addEventListener("click", (e) => {
    if (e.target === variantModal) {
      variantModal.style.display = "none";
    }
  });
});
