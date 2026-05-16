// ==================== SHOP PAGE LOGIC ====================

// Configuration
const PRODUCTS_PER_PAGE = 12;
let currentPage = 1;
let filteredProducts = [];
let currentSort = 'newest';
let searchQuery = '';

/**
 * Initialize shop page
 */
function initShopPage() {
    filteredProducts = [...allProducts];
    updateCartBadge();
    renderProducts();
    renderPagination();
    setupEventListeners();
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleSearch, 300));
    }

    // Sort dropdown
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', handleSort);
    }
}

/**
 * Handle search
 */
function handleSearch(e) {
    searchQuery = e.target.value.toLowerCase().trim();
    currentPage = 1; // Reset to first page
    filterAndSort();
}

/**
 * Handle sort
 */
function handleSort(e) {
    currentSort = e.target.value;
    currentPage = 1; // Reset to first page
    filterAndSort();
}

/**
 * Filter and sort products
 */
function filterAndSort() {
    // Filter by search
    if (searchQuery) {
        filteredProducts = allProducts.filter(product => 
            product.name.toLowerCase().includes(searchQuery) ||
            (product.category && product.category.toLowerCase().includes(searchQuery))
        );
    } else {
        filteredProducts = [...allProducts];
    }

    // Sort
    switch (currentSort) {
        case 'price-low':
            filteredProducts.sort((a, b) => a.price - b.price);
            break;
        case 'price-high':
            filteredProducts.sort((a, b) => b.price - a.price);
            break;
        case 'name-asc':
            filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
            break;
        case 'name-desc':
            filteredProducts.sort((a, b) => b.name.localeCompare(a.name));
            break;
        case 'newest':
        default:
            // Keep original order (assuming newest first)
            break;
    }

    renderProducts();
    renderPagination();
    updateProductCount();
}

/**
 * Render products
 */
function renderProducts() {
    const shopGrid = document.getElementById('shopGrid');
    const emptyState = document.getElementById('emptyState');

    if (filteredProducts.length === 0) {
        shopGrid.style.display = 'none';
        emptyState.style.display = 'block';
        document.getElementById('pagination').style.display = 'none';
        return;
    }

    shopGrid.style.display = 'grid';
    emptyState.style.display = 'none';
    document.getElementById('pagination').style.display = 'flex';

    // Calculate pagination
    const startIndex = (currentPage - 1) * PRODUCTS_PER_PAGE;
    const endIndex = startIndex + PRODUCTS_PER_PAGE;
    const productsToShow = filteredProducts.slice(startIndex, endIndex);

    // Render products
    shopGrid.innerHTML = productsToShow.map(product => `
        <div class="shop-product-card" onclick="viewProduct(${product.id})">
            <div class="shop-product-image">
                <img src="${product.image}" alt="${product.name}">
                ${product.stock === 0 ? '<div class="out-of-stock-badge">Out of Stock</div>' : ''}
            </div>
            <div class="shop-product-content">
                <h3 class="shop-product-name">${product.name}</h3>
                <div class="shop-product-price">$ ${product.price.toFixed(2)}</div>
                <div class="shop-product-actions">
                    <button class="view-product-btn-shop" onclick="viewProduct(${product.id}); event.stopPropagation();">
                        <i class="fas fa-eye"></i> View Product
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Render pagination
 */
function renderPagination() {
    const pagination = document.getElementById('pagination');
    const totalPages = Math.ceil(filteredProducts.length / PRODUCTS_PER_PAGE);

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHTML = '';

    // Previous button
    paginationHTML += `
        <button 
            class="pagination-btn" 
            onclick="goToPage(${currentPage - 1})"
            ${currentPage === 1 ? 'disabled' : ''}
        >
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers
    const maxPagesToShow = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

    // Adjust if we're near the end
    if (endPage - startPage < maxPagesToShow - 1) {
        startPage = Math.max(1, endPage - maxPagesToShow + 1);
    }

    // First page
    if (startPage > 1) {
        paginationHTML += `
            <button class="pagination-btn" onclick="goToPage(1)">1</button>
        `;
        if (startPage > 2) {
            paginationHTML += `<span style="padding: 0 0.5rem; color: var(--text-light);">...</span>`;
        }
    }

    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button 
                class="pagination-btn ${i === currentPage ? 'active' : ''}" 
                onclick="goToPage(${i})"
            >
                ${i}
            </button>
        `;
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHTML += `<span style="padding: 0 0.5rem; color: var(--text-light);">...</span>`;
        }
        paginationHTML += `
            <button class="pagination-btn" onclick="goToPage(${totalPages})">${totalPages}</button>
        `;
    }

    // Next button
    paginationHTML += `
        <button 
            class="pagination-btn" 
            onclick="goToPage(${currentPage + 1})"
            ${currentPage === totalPages ? 'disabled' : ''}
        >
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    pagination.innerHTML = paginationHTML;
}

/**
 * Go to specific page
 */
function goToPage(page) {
    const totalPages = Math.ceil(filteredProducts.length / PRODUCTS_PER_PAGE);
    
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    renderProducts();
    renderPagination();
    updateProductCount();
    
    // Scroll to top of products
    document.querySelector('.shop-header').scrollIntoView({ behavior: 'smooth' });
}

/**
 * Update product count
 */
function updateProductCount() {
    const productCount = document.getElementById('productCount');
    const totalProducts = filteredProducts.length;
    
    if (totalProducts === 0) {
        productCount.textContent = 'No products found';
        return;
    }

    const startIndex = (currentPage - 1) * PRODUCTS_PER_PAGE + 1;
    const endIndex = Math.min(currentPage * PRODUCTS_PER_PAGE, totalProducts);

    productCount.textContent = `Showing ${startIndex}–${endIndex} of ${totalProducts} products`;
}

/**
 * Clear search
 */
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    
    searchQuery = '';
    currentPage = 1;
    filterAndSort();
}

/**
 * View product
 */
function viewProduct(productId) {
    // In production, navigate to product detail page
    window.location.href = `/product/${productId}`;
}

// ==================== INITIALIZATION ====================

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initShopPage);
} else {
    initShopPage();
}