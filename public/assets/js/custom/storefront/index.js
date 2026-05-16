const editBtn = document.getElementById("edit-code-btn");
const editForm = document.getElementById("edit-code-form");
const codeInput = document.getElementById("code-input");
const storeUrlInput = document.getElementById("store-url");
const storeUrlCard = document.getElementById("store-url-card");
const saveBtn = document.getElementById("save-code-btn");
const cancelBtn = document.getElementById("cancel-code-btn");

editBtn.addEventListener("click", () => {
  editForm.classList.remove("hidden");
  editBtn.classList.add("hidden");
  storeUrlCard.classList.add("hidden");
});

cancelBtn.addEventListener("click", () => {
  editForm.classList.add("hidden");
  editBtn.classList.remove("hidden");
  storeUrlCard.classList.remove("hidden");

  codeInput.value = storeUrlInput.value.split("/").pop(); // reset value
});

saveBtn.addEventListener("click", () => {
  const newCode = codeInput.value.trim();
  if (newCode) {
    const newUrl = `http://localhost/vendora/public/${newCode}`;
    storeUrlInput.value = newUrl;
    editForm.classList.add("hidden");
    editBtn.classList.remove("hidden");
  storeUrlCard.classList.remove("hidden");
  }
});
