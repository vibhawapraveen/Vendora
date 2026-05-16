// Media upload JavaScript
document.addEventListener("DOMContentLoaded", function () {
  const uploadArea = document.getElementById("uploadArea");
  const imageInput = document.getElementById("imageInput");
  const selectedFiles = document.getElementById("selectedFiles");
  const previewGrid = document.getElementById("previewGrid");

  let selectedFilesList = [];

  // Only initialize if elements exist (for non-variant products)
  if (uploadArea && imageInput) {
    // Drag and drop handlers
    uploadArea.addEventListener("dragover", (e) => {
      e.preventDefault();
      uploadArea.classList.add("dragover");
    });

    uploadArea.addEventListener("dragleave", (e) => {
      e.preventDefault();
      uploadArea.classList.remove("dragover");
    });

    uploadArea.addEventListener("drop", (e) => {
      e.preventDefault();
      uploadArea.classList.remove("dragover");

      const files = Array.from(e.dataTransfer.files);
      handleFiles(files);
    });

    imageInput.addEventListener("change", (e) => {
      const files = Array.from(e.target.files);
      handleFiles(files);
    });

    function handleFiles(files) {
      selectedFilesList = [...selectedFilesList, ...files];
      updatePreview();
    }

    function updatePreview() {
      if (selectedFilesList.length === 0) {
        selectedFiles.style.display = "none";
        return;
      }

      selectedFiles.style.display = "block";
      previewGrid.innerHTML = "";

      selectedFilesList.forEach((file, index) => {
        if (file.type.startsWith("image/")) {
          const reader = new FileReader();
          reader.onload = (e) => {
            const previewItem = document.createElement("div");
            previewItem.className = "preview-item";
            previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-btn" onclick="removeFile(${index})">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        `;
            previewGrid.appendChild(previewItem);
          };
          reader.readAsDataURL(file);
        }
      });
    }

    window.removeFile = function (index) {
      selectedFilesList.splice(index, 1);
      updateFileInput();
      updatePreview();
    };

    function updateFileInput() {
      // Create new FileList with remaining files
      const dt = new DataTransfer();
      selectedFilesList.forEach((file) => dt.items.add(file));
      imageInput.files = dt.files;
    }
  }

  // Handle variant image previews
  document.querySelectorAll(".variant-image-input").forEach((input) => {
    input.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        const variantCard = this.closest(".variant-card");
        const uploadArea = variantCard.querySelector(".variant-upload-area");

        reader.onload = function (e) {
          // Check if current image div exists, if not create it
          let currentImageDiv = uploadArea.querySelector(".current-image");
          if (!currentImageDiv) {
            currentImageDiv = document.createElement("div");
            currentImageDiv.className = "current-image mb-2";
            uploadArea.insertBefore(currentImageDiv, uploadArea.firstChild);
          }

          // Update image
          currentImageDiv.innerHTML = `<img src="${e.target.result}" alt="Variant Image" style="max-width: 100%; border-radius: 8px;">`;

          // Update label text
          const label = uploadArea.querySelector(".variant-upload-label span");
          if (label) {
            label.textContent = "Change Image";
          }
        };

        reader.readAsDataURL(file);
      }
    });
  });

  // Add form submission handler with loading state
  const form = document.querySelector('form[enctype="multipart/form-data"]');
  if (form) {
    form.addEventListener("submit", function (e) {
      const submitBtn = form.querySelector(
        'button[type="submit"]:not([name="skip"])',
      );
      if (submitBtn && !form.querySelector('[name="skip"]:focus')) {
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<i class="fa-solid fa-spinner fa-spin"></i> Uploading...';
      }
    });
  }
});
