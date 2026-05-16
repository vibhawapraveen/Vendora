function openEditCategoryModal(categoryId, categoryName, categoryStatus) {
  document.getElementById("edit-category-id").value = categoryId;
  document.getElementById("edit-category-name").value = categoryName;
  document.getElementById("edit-category-status").value =
    categoryStatus === "inactive" ? "inactive" : "active";
  openModal("edit-category-modal");
}

function openDeleteCategoryModal(categoryId, categoryName) {
  document.getElementById("delete-category-id").value = categoryId;
  document.getElementById("delete-category-name").textContent = categoryName;
  openModal("delete-category-modal");
}

window.openEditCategoryModal = openEditCategoryModal;
window.openDeleteCategoryModal = openDeleteCategoryModal;

(function () {
  const alerts = document.querySelectorAll(".alert");
  if (!alerts.length) return;

  setTimeout(function () {
    alerts.forEach(function (alert) {
      alert.style.transition = "opacity 0.5s ease";
      alert.style.opacity = "0";
      setTimeout(function () {
        alert.remove();
      }, 500);
    });
  }, 4000);
})();
