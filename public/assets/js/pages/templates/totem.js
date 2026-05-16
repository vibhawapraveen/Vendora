// Ensure toast container exists
(function () {
  if (!document.getElementById('toast-container')) {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
})();

function showToast(message, type = "success", duration = 3000) {
  const container = document.getElementById('toast-container');
  const toast = document.createElement("div");
  toast.className = `toast show ${type}`;
  toast.textContent = message;

  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => {
      toast.remove();
    }, 300);
  }, duration);
}

function increaseQuantity(productId, maxStock) {
  const input = document.getElementById(`qty-${productId}`);
  const currentValue = parseInt(input.value);
  if (currentValue < maxStock) {
    input.value = currentValue + 1;
  }
}

function decreaseQuantity(productId) {
  const input = document.getElementById(`qty-${productId}`);
  const currentValue = parseInt(input.value);
  if (currentValue > 1) {
    input.value = currentValue - 1;
  }
}

window.showToast = showToast;
window.increaseQuantity = increaseQuantity;
window.decreaseQuantity = decreaseQuantity;
