const ROOT = document.querySelector("#attributesForm").dataset.root;
const PRODUCT_ID = document.querySelector("#attributesForm").dataset.productId;

function addAttribute() {
  const container = document.getElementById("attributesContainer");
  const attributeItem = document.createElement("div");
  attributeItem.className = "attribute-item mb-4";

  attributeItem.innerHTML = `
        <div class="mb-4">
            <label class="attribute-label mb-2">Attribute name</label>
            <div class="attribute-input-group">
                <div class="attribute-input-wrapper">
                    <input type="text" class="input attribute-input" placeholder="e.g. Size, Color, Material">
                </div>
                <button type="button" class="delete-btn" onclick="removeAttribute(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div>
            <label class="attribute-label mb-3">Values</label>
            <div class="values-container mb-4">
                <!-- Values will be added here -->
            </div>
            
            <div class="value-input-container">
                <input type="text" placeholder="Add value and press Enter" class="input value-input" onkeypress="handleValueInput(event, this)">
                <button type="button" class="btn btn-outline add-btn" onclick="addValue(this)">
                    <span class="text-sm">+</span>
                    <span>Add</span>
                </button>
            </div>
        </div>
    `;

  container.appendChild(attributeItem);
}

function removeAttribute(button) {
  const attributeItem = button.closest(".attribute-item");
  attributeItem.remove();
}

function addValue(button) {
  const valueInput = button.previousElementSibling;
  const value = valueInput.value.trim();

  if (value === "") {
    alert("Please enter a value");
    return;
  }

  const valuesContainer = button
    .closest(".attribute-item")
    .querySelector(".values-container");

  const valueTag = document.createElement("span");
  valueTag.className = "value-tag";
  valueTag.innerHTML = `
        ${value}
        <button type="button" class="delete-btn p-0" onclick="removeValue(this)">
            <i class="fa-solid fa-trash text-xs"></i>
        </button>
    `;

  valuesContainer.appendChild(valueTag);
  valueInput.value = "";
}

function removeValue(button) {
  const valueTag = button.closest(".value-tag");
  valueTag.remove();
}

function handleValueInput(event, input) {
  if (event.key === "Enter") {
    event.preventDefault();
    const addButton = input.nextElementSibling;
    addValue(addButton);
  }
}

function collectAttributesData() {
  const attributeItems = document.querySelectorAll(".attribute-item");
  const attributes = [];

  attributeItems.forEach((item) => {
    const nameInput = item.querySelector(".attribute-input");
    const name = nameInput.value.trim();

    if (name === "") {
      return;
    }

    const valueTags = item.querySelectorAll(".value-tag");
    const values = [];

    valueTags.forEach((tag) => {
      const valueText = tag.childNodes[0].textContent.trim();
      if (valueText) {
        values.push(valueText);
      }
    });

    if (values.length > 0) {
      attributes.push({
        name: name,
        values: values,
      });
    }
  });

  return attributes;
}

document
  .getElementById("attributesForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const attributes = collectAttributesData();

    if (attributes.length === 0) {
      alert("Please add at least one attribute with values");
      return;
    }

    for (let attr of attributes) {
      if (attr.values.length === 0) {
        alert(`Attribute "${attr.name}" must have at least one value`);
        return;
      }
    }

    const data = {
      attributes: attributes,
    };

    const submitBtn = document.querySelector(".btn-primary");
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch(
      ROOT + "dashboard/products/newproduct/" + PRODUCT_ID + "/attributes",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      },
    )
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.href = data.redirect_url;
        } else {
          alert("Error: " + data.message);
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred while saving attributes");
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      });
  });

// Load existing attributes if available
window.addEventListener("DOMContentLoaded", function () {
  // Get existing attributes from data attribute
  const existingAttrsData =
    document.querySelector("#attributesForm").dataset.existingAttributes;

  if (existingAttrsData) {
    const existingAttributes = JSON.parse(existingAttrsData);
    const container = document.getElementById("attributesContainer");
    container.innerHTML = "";

    existingAttributes.forEach((attr) => {
      addAttribute();
      const lastAttribute = container.lastElementChild;

      const nameInput = lastAttribute.querySelector(".attribute-input");
      nameInput.value = attr.name;

      const valuesContainer = lastAttribute.querySelector(".values-container");
      attr.values.forEach((valueObj) => {
        const valueTag = document.createElement("span");
        valueTag.className = "value-tag";
        valueTag.innerHTML = `
                    ${valueObj.value}
                    <button type="button" class="delete-btn p-0" onclick="removeValue(this)">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                `;
        valuesContainer.appendChild(valueTag);
      });
    });
  }
});
