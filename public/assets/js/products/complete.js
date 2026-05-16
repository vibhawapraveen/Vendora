// Auto-save visibility changes
document.querySelectorAll('input[name="visibility"]').forEach((radio) => {
  radio.addEventListener("change", function () {
    const formData = new FormData(document.getElementById("visibilityForm"));

    // Get PHP variables from data attributes
    const form = document.getElementById("visibilityForm");
    const root = form.dataset.root;
    const productId = form.dataset.productId;

    fetch(root + "dashboard/products/newproduct/" + productId + "/complete", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (response.ok) {
          // Optional: Show a brief success indicator
        }
      })
      .catch((error) => {
        console.error("Error updating visibility:", error);
      });
  });
});
