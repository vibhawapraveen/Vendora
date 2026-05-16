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

window.showToast = showToast;
