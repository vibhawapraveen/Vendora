document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("product-search");
  const categoryFilter = document.getElementById("category-filter");
  const productCards = document.querySelectorAll(".pos-product-card");

  if (!searchInput || !categoryFilter || productCards.length === 0) {
    return;
  }

  const applyFilters = () => {
    const searchTerm = searchInput.value.trim().toLowerCase();
    const selectedCategory = categoryFilter.value.toLowerCase();

    productCards.forEach((card) => {
      const name = (card.dataset.name || "").toLowerCase();
      const category = (card.dataset.category || "").toLowerCase();
      const matchesSearch = !searchTerm || name.includes(searchTerm);
      const matchesCategory = selectedCategory === "all" || category === selectedCategory;

      card.style.display = matchesSearch && matchesCategory ? "" : "none";
    });
  };

  searchInput.addEventListener("input", applyFilters);
  categoryFilter.addEventListener("change", applyFilters);
});
