// ==================== PRODUCT PAGE LOGIC ====================

let productData = null;
let selectedVariant = null;
let selectedAttributes = {};

/**
 * Initialize product page
 */
function initProductPage(data) {
    productData = data;
    updateCartBadge();
    
    if (productData.is_variant) {
        // Hide price/stock section initially
        const priceSection = document.getElementById('productPrice')?.closest('.product-detail-price-section');
        if (priceSection) {
            priceSection.style.display = 'none';
        }
        initializeFirstVariant();
    }
}

/**
 * Initialize with first variant
 */
function initializeFirstVariant() {
    const firstVariant = productData.variants[0];
    
    // Set selected attributes based on first variant
    firstVariant.attributes.forEach(attrValueId => {
        productData.attributes.forEach(attr => {
            const value = attr.values.find(v => v.id === attrValueId);
            if (value) {
                selectedAttributes[attr.id] = value.id;
                // Update UI
                const selectedSpan = document.getElementById(`selected-${attr.name.toLowerCase()}`);
                if (selectedSpan) {
                    selectedSpan.textContent = value.value;
                }
                // Mark button as active
                const buttons = document.querySelectorAll(`[data-attribute-id="${attr.id}"]`);
                buttons.forEach(btn => {
                    if (btn.dataset.valueId === value.id) {
                        btn.classList.add('active');
                    }
                });
            }
        });
    });

    updateSelectedVariant();
}

/**
 * Select variant option
 */
function selectVariantOption(attributeId, valueId, valueName) {
    selectedAttributes[attributeId] = valueId;
    
    // Update selected label
    const attribute = productData.attributes.find(a => a.id === attributeId);
    if (attribute) {
        const selectedSpan = document.getElementById(`selected-${attribute.name.toLowerCase()}`);
        if (selectedSpan) {
            selectedSpan.textContent = valueName;
        }
    }

    // Update active state
    const buttons = document.querySelectorAll(`[data-attribute-id="${attributeId}"]`);
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.valueId === valueId) {
            btn.classList.add('active');
        }
    });

    updateSelectedVariant();
}

/**
 * Update selected variant
 */
function updateSelectedVariant() {
    // Find matching variant
    const selectedAttrIds = Object.values(selectedAttributes);
    
    const variant = productData.variants.find(v => {
        return selectedAttrIds.every(id => v.attributes.includes(id)) && 
               v.attributes.length === selectedAttrIds.length;
    });

    if (variant) {
        selectedVariant = variant;
        
        // Show price section
        const priceSection = document.getElementById('productPrice')?.closest('.product-detail-price-section');
        if (priceSection) {
            priceSection.style.display = 'block';
        }
        
        // Update price
        document.getElementById('productPrice').textContent = '$ ' + variant.price.toFixed(2);
        
        // Update stock
        document.getElementById('stockQuantity').textContent = variant.stock;
        
        // Update stock status color
        const stockElement = document.getElementById('productStock');
        if (variant.stock > 0) {
            stockElement.style.color = 'var(--success-green)';
        } else {
            stockElement.style.color = 'var(--danger-red)';
            stockElement.querySelector('span').textContent = 'Out of';
        }
        
        // Update main image
        document.getElementById('mainProductImage').src = variant.image;
        
        // Update variant info display
        const variantInfo = productData.attributes.map(attr => {
            const valueId = selectedAttributes[attr.id];
            const value = attr.values.find(v => v.id === valueId);
            return value ? value.value : '';
        }).join(' • ');
        
        document.getElementById('selectedVariantInfo').textContent = variantInfo;
        
        // Update quantity max
        const quantityInput = document.getElementById('productQuantity');
        if (quantityInput) {
            quantityInput.max = variant.stock;
        }
        
        // Update add to cart button state
        const addBtn = document.getElementById('addToCartBtn');
        if (addBtn) {
            if (variant.stock > 0) {
                addBtn.disabled = false;
                addBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
            } else {
                addBtn.disabled = true;
                addBtn.innerHTML = '<i class="fas fa-times"></i> Out of Stock';
            }
        }

        // Update thumbnails
        updateThumbnails();
    } else {
        // Variant combination not available
        selectedVariant = null;
        
        // Hide price section
        const priceSection = document.getElementById('productPrice')?.closest('.product-detail-price-section');
        if (priceSection) {
            priceSection.style.display = 'none';
        }
        
        // Disable add to cart button
        const addBtn = document.getElementById('addToCartBtn');
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.innerHTML = '<i class="fas fa-ban"></i>Not Available';
        }
        
        // Show message or visual feedback
        const variantInfo = document.getElementById('selectedVariantInfo');
        if (variantInfo) {
            variantInfo.textContent = 'This combination is not available';
        }
    }
}

/**
 * Update thumbnails
 */
function updateThumbnails() {
    const container = document.getElementById('thumbnailContainer');
    if (!container) return;

    container.innerHTML = '';
    
    // Add general product images
    productData.images.forEach((image, index) => {
        const thumb = document.createElement('img');
        thumb.src = image;
        thumb.alt = 'Product image';
        thumb.className = 'product-thumbnail';
        
        // Mark as active if it matches the selected variant image
        if (selectedVariant && selectedVariant.image === image) {
            thumb.classList.add('active');
        }
        
        thumb.onclick = function() {
            changeMainImage(image, this);
        };
        container.appendChild(thumb);
    });

    // Add all variant-specific images
    if (productData.variants && productData.variants.length > 0) {
        const addedImages = new Set(productData.images);
        
        productData.variants.forEach(variant => {
            if (variant.image && !addedImages.has(variant.image)) {
                addedImages.add(variant.image);
                const thumb = document.createElement('img');
                thumb.src = variant.image;
                thumb.alt = 'Variant image';
                thumb.className = 'product-thumbnail';
                
                // Mark as active if it's the selected variant
                if (selectedVariant && selectedVariant.id === variant.id) {
                    thumb.classList.add('active');
                }
                
                thumb.onclick = function() {
                    changeMainImage(variant.image, this);
                };
                container.appendChild(thumb);
            }
        });
    }
}

/**
 * Change main image
 */
function changeMainImage(imageSrc, thumbnailElement) {
    document.getElementById('mainProductImage').src = imageSrc;
    
    // Update active thumbnail
    document.querySelectorAll('.product-thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    if (thumbnailElement) {
        thumbnailElement.classList.add('active');
    }
}

/**
 * Increment quantity
 */
function incrementQuantity() {
    const input = document.getElementById('productQuantity');
    const max = parseInt(input.max) || 999;
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

/**
 * Decrement quantity
 */
function decrementQuantity() {
    const input = document.getElementById('productQuantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

/**
 * Add product to cart (with variant)
 */
function addProductToCart() {
    if (!selectedVariant) {
        showNotification('Please select product options', 'error');
        return;
    }

    if (selectedVariant.stock <= 0) {
        showNotification('This variant is out of stock', 'error');
        return;
    }

    const quantity = parseInt(document.getElementById('productQuantity').value);
    
    if (quantity > selectedVariant.stock) {
        showNotification(`Only ${selectedVariant.stock} items available`, 'error');
        return;
    }

    // Get variant description
    const variantDescription = productData.attributes.map(attr => {
        const valueId = selectedAttributes[attr.id];
        const value = attr.values.find(v => v.id === valueId);
        return value ? `${attr.name}: ${value.value}` : '';
    }).filter(v => v).join(', ');

    addToCartWithVariant(
        productData.id,
        productData.name,
        selectedVariant.price,
        quantity,
        selectedVariant.id,
        selectedVariant.sku,
        variantDescription,
        selectedVariant.image
    );

    showNotification(`${productData.name} (${variantDescription}) added to cart!`, 'success');
}

/**
 * Add single product to cart (no variant)
 */
function addSingleProductToCart() {
    const quantity = parseInt(document.getElementById('productQuantity').value);
    
    if (quantity > productData.stock) {
        showNotification(`Only ${productData.stock} items available`, 'error');
        return;
    }

    const mainImage = productData.images && productData.images.length > 0 ? productData.images[0] : null;
    addToCart(productData.id, productData.name, productData.price, quantity, mainImage);
}
