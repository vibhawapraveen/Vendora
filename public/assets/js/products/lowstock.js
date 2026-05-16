// Initializes the search functionality for the product table
function initializeSearch() {
  // Grab the search input element from the DOM
  const searchInput = document.getElementById("search-input");
  const categoryFilter = document.getElementById("category-filter");
  const severityFilter = document.getElementById("visibility-filter");
  const variantFilter = document.getElementById("variant-filter");
  const clearFiltersBtn = document.getElementById("clear-filters-btn");

  // If markup is missing the search input, skip binding to avoid runtime errors.
  if (!searchInput) return;

  const allRows = Array.from(document.querySelectorAll("tbody tr"));

  // Runs every time the user types in the search box
  function applySearch() {
    // Read the current search input, convert to lowercase for
    // case-insensitive comparison, and remove accidental leading/trailing spaces
    const searchTerm = searchInput.value.toLowerCase().trim();
    const selectedCategory = categoryFilter
      ? categoryFilter.value.toLowerCase().trim()
      : "";
    const selectedSeverity = severityFilter
      ? severityFilter.value.toLowerCase().trim()
      : "";
    const selectedVariant = variantFilter
      ? variantFilter.value.toLowerCase().trim()
      : "";

    // Tracks how many rows are currently visible after filtering
    let visibleCount = 0;

    // Loop through every row in the table
    allRows.forEach((row) => {
      // Skip special rows that span all columns (e.g. "No products found" row)
      // These are layout rows, not real product rows
      if (row.querySelector("td[colspan]")) return;

      // Read the product name from the 1st column and lowercase it
      // Optional chaining (?.) prevents errors if the cell doesn't exist
      // Falls back to empty string ('') if the cell is missing
      const productName =
        row.querySelector("td:nth-child(1)")?.textContent.toLowerCase() || "";

      // Determine if this row should be visible:
      // - Show it if there's no search term (empty search = show all)
      // - Show it if the product name contains the search term
      let show = !searchTerm || productName.includes(searchTerm);

      // Category filter
      if (selectedCategory && show) {
        const categoryText =
          row
            .querySelector("td:nth-child(2)")
            ?.textContent.toLowerCase()
            .trim() || "";
        if (categoryText !== selectedCategory) {
          show = false;
        }
      }

      // Severity filter
      if (selectedSeverity && show) {
        const severityCell = row.querySelector("td:nth-child(6) .badge");
        const severityText =
          severityCell?.textContent.toLowerCase().trim() || "";

        if (
          selectedSeverity === "critical" &&
          !severityText.includes("critical")
        ) {
          show = false;
        } else if (
          selectedSeverity === "warning" &&
          !severityText.includes("warning")
        ) {
          show = false;
        } else if (
          selectedSeverity === "restock_soon" &&
          !severityText.includes("restock soon")
        ) {
          show = false;
        }
      }

      // Variant filter
      if (selectedVariant && show) {
        const variantCell = row.querySelector("td:nth-child(3) .badge");
        const variantText = variantCell?.textContent.toLowerCase().trim() || "";

        if (selectedVariant === "multi" && !variantText.includes("multi")) {
          show = false;
        } else if (
          selectedVariant === "single" &&
          !variantText.includes("single")
        ) {
          show = false;
        }
      }

      // Show or hide the row using inline style
      // '' restores the default display, 'none' hides it completely
      row.style.display = show ? "" : "none";

      // Count this row if it's visible
      if (show) visibleCount++;
    });

    // After looping all rows, update the "no results" message
    // based on how many rows are visible
    updateNoResultsMessage(visibleCount);

    // Keep clear filters button visibility in sync with active filters.
    updateClearButtonVisibility();
  }

  // Shows or hides a "no results" message row inside the table
  function updateNoResultsMessage(visibleCount) {
    const tbody = document.querySelector("tbody");

    // Check if the "no results" row already exists in the table
    let noResultsRow = tbody.querySelector(".no-results-row");

    if (visibleCount === 0) {
      // No rows matched the search — show the "no results" message

      if (!noResultsRow) {
        // Message row doesn't exist yet — create it dynamically
        noResultsRow = document.createElement("tr");
        noResultsRow.className = "no-results-row";

        // colspan="8" makes the cell span all 8 columns so it looks centered
        noResultsRow.innerHTML = `
          <td colspan="8" class="px-6 py-4 text-center text-gray-500">
            No products match your search.
          </td>
        `;

        // Add the new row to the end of the table body
        tbody.appendChild(noResultsRow);
      }

      // Make the message row visible (it may have been hidden previously)
      noResultsRow.style.display = "";
    } else {
      // At least one row is visible — hide the "no results" message if it exists
      // We hide instead of removing it so we can reuse it next time
      if (noResultsRow) noResultsRow.style.display = "none";
    }
  }

  // Show clear button only when any filter is active.
  function updateClearButtonVisibility() {
    if (!clearFiltersBtn) return;

    const hasActiveFilters =
      searchInput.value.trim() !== "" ||
      (categoryFilter && categoryFilter.value !== "") ||
      (severityFilter && severityFilter.value !== "") ||
      (variantFilter && variantFilter.value !== "");

    clearFiltersBtn.style.display = hasActiveFilters ? "inline-flex" : "none";
  }

  // Reset all filters and re-run table filtering.
  function clearFilters() {
    searchInput.value = "";
    if (categoryFilter) categoryFilter.value = "";
    if (severityFilter) severityFilter.value = "";
    if (variantFilter) variantFilter.value = "";
    applySearch();
  }

  // Listen for any keystroke in the search input and run applySearch() immediately
  // 'input' fires on every character typed, unlike 'change' which only fires on blur
  searchInput.addEventListener("input", applySearch);
  if (categoryFilter) {
    categoryFilter.addEventListener("change", applySearch);
  }
  if (severityFilter) {
    severityFilter.addEventListener("change", applySearch);
  }
  if (variantFilter) {
    variantFilter.addEventListener("change", applySearch);
  }
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener("click", clearFilters);
  }

  // Initial button state.
  updateClearButtonVisibility();
}

// Call initializeSearch when the page loads
document.addEventListener("DOMContentLoaded", function () {
  initializeSearch();
});
