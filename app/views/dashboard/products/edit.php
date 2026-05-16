<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - <?= htmlspecialchars($data['product']['name']) ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/edit.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>

        <main class="content">
            <!-- Alert Messages -->
            <?php if (isset($_GET['success']) && $_GET['success'] === 'product_updated'): ?>
                <div class="alert alert-success" style="margin-bottom: 1rem">
                    <div class="alert-icon">✅</div>
                    <div class="alert-content">
                        <div class="alert-title">Success!</div>
                        <div class="alert-description">Product has been successfully updated.</div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error" style="margin-bottom: 1rem">
                    <div class="alert-icon">❌</div>
                    <div class="alert-content">
                        <div class="alert-title">Error</div>
                        <div class="alert-description">
                            <?php
                            switch ($_GET['error']) {
                                case 'update_failed':
                                    echo 'Failed to update the product. Please try again.';
                                    break;
                                case 'missing_name':
                                    echo 'Product name is required.';
                                    break;
                                default:
                                    echo 'An unexpected error occurred.';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="flex justify-between items-center mb-5">
                <div>
                    <h2 class="font-semibold">Edit Product</h2>
                    <p class="text-muted text-sm">Update product information and manage inventory</p>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" id="editProductForm">
                <div id="removed-images-inputs"></div>
                <!-- Basic Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-3">Basic Information</h3>
                    </div>
                    <div class="card-content">
                        <!-- Product Name -->
                        <div class="mb-5">
                            <p class="mb-1">
                                Product Name
                            </p>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="input"
                                placeholder="Enter product name"
                                value="<?= htmlspecialchars($data['product']['name']) ?>"
                                required>
                        </div>

                        <!-- Product Description -->
                        <div class="mb-5">
                            <p class="mb-1">
                                Description
                            </p>
                            <div id="description-editor" style="height: 220px; background: #fff;"></div>
                            <input type="hidden" id="description" name="description">
                        </div>

                        <!-- Product Status -->
                        <div class="mb-5">
                            <p class="mb-1">
                                Product Status
                            </p>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="visibility"
                                    class="form-checkbox"
                                    <?= $data['product']['visibility'] == 1 ? 'checked' : '' ?>>
                                <span>Active (Visible to customers)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Product Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-3">Product Information</h3>
                    </div>
                    <div class="card-content">
                        <!-- Product Type Badge -->
                        <div class="mb-4">
                            <p class="text-sm text-muted mb-2">
                                <i class="fa-solid fa-layer-group" style="color: #3b82f6; margin-right: 0.5rem;"></i>Product Type
                            </p>
                            <?php if ($data['product']['is_variant'] == 1): ?>
                                <span class="badge badge-multi" style="display: inline-flex;">
                                    <i class="fa-solid fa-layer-group pr-2"></i> Multi-Variant
                                </span>
                            <?php else: ?>
                                <span class="badge badge-single" style="display: inline-flex;">
                                    <i class="fa-solid fa-box pr-2"></i> Single Product
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Timestamps -->
                        <div class="mb-3">
                            <p class="text-sm text-muted mb-1">Created</p>
                            <p class="text-sm">
                                <?= date('M d, Y \\a\\t h:i A', strtotime($data['product']['created_at'])) ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted mb-1">Last Updated</p>
                            <p class="text-sm">
                                <?= date('M d, Y \\a\\t h:i A', strtotime($data['product']['updated_at'])) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if ($data['product']['is_variant'] == 0): ?>
                    <!-- Single Product - Price & Stock -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Pricing & Inventory</h3>
                        </div>
                        <div class="card-content">
                            <div class="mb-5">
                                <p class="mb-1">
                                    <i class="fa-solid fa-dollar-sign" style="color: #16a34a; margin-right: 0.5rem;"></i>Product Price (USD)
                                </p>
                                <input
                                    type="number"
                                    id="price"
                                    name="price"
                                    class="input"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    value="<?= htmlspecialchars($data['product']['price']) ?>"
                                    required>
                            </div>
                            <div class="mb-5">
                                <p class="mb-1">
                                    <i class="fa-solid fa-boxes-stacked" style="color: #f59e0b; margin-right: 0.5rem;"></i>Stock Quantity
                                </p>
                                <input
                                    type="number"
                                    id="stock_quantity"
                                    name="stock_quantity"
                                    class="input"
                                    step="1"
                                    min="0"
                                    placeholder="0"
                                    value="<?= htmlspecialchars($data['product']['stock_quantity']) ?>"
                                    required>
                            </div>
                            <div class="mb-5">
                                <p class="mb-1">
                                    <i class="fa-solid fa-circle-exclamation" style="color: #e20000; margin-right: 0.5rem;"></i>Low Stock Alert
                                </p>
                                <input
                                    type="number"
                                    id="low_stock_alert"
                                    name="low_stock_alert"
                                    class="input"
                                    step="1"
                                    min="0"
                                    placeholder="0"
                                    value="<?= htmlspecialchars($data['product']['low_stock_alert'] ?? '') ?>"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Single Product - Images -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Product Images</h3>
                        </div>
                        <div class="card-content">
                            <!-- Existing Images -->
                            <?php if (!empty($data['images'])): ?>
                                <div class="mb-4">
                                    <label class="form-label">Current Images</label>
                                    <div class="grid grid-cols-3 gap-4">
                                        <?php foreach ($data['images'] as $image): ?>
                                            <div class="relative" style="border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--border); background: #f9fafb;">
                                                <img src="<?= ROOT . htmlspecialchars($image['image_url']) ?>"
                                                    alt="Product image"
                                                    class="w-full h-48" style="object-fit: cover; border-radius: 0.5rem;">
                                                <button type="button"
                                                    class="btn-remove-image"
                                                    onclick="removeImage('<?= $image['id'] ?>', this)">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-sm mb-3">No images uploaded yet.</p>
                            <?php endif; ?>

                            <!-- Upload New Images -->
                            <div class="mb-5">
                                <p class="mb-1">Add New Images</p>
                                <input
                                    type="file"
                                    id="product_images"
                                    name="product_images[]"
                                    accept="image/*"
                                    multiple
                                    onchange="previewProductImages(this)">
                                <p class="text-muted text-xs mt-1">You can select multiple images</p>

                                <!-- Preview Container -->
                                <div id="product-images-preview" class="grid grid-cols-3 gap-4 mt-4" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <?php $mainThumbnail = !empty($data['images']) ? $data['images'][0] : null; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Main Product Thumbnail</h3>
                        </div>
                        <div class="card-content">
                            <p class="text-muted text-sm mb-3">This thumbnail is used as the product-level image for your multi-variant product.</p>
                            <div class="mb-4" id="main-thumbnail-preview" style="max-width: 320px; border: 1px solid var(--border); border-radius: 0.5rem; overflow: hidden; background: #f9fafb;">
                                <?php if (!empty($mainThumbnail) && !empty($mainThumbnail['image_url'])): ?>
                                    <img src="<?= ROOT . htmlspecialchars($mainThumbnail['image_url']) ?>"
                                        alt="Main product thumbnail"
                                        style="display: block; width: 100%; height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                                        <div style="text-align: center;">
                                            <i class="fa-solid fa-image" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                            <div class="text-sm">No main thumbnail</div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <input
                                type="file"
                                id="main_thumbnail"
                                name="main_thumbnail"
                                accept="image/*"
                                onchange="previewMainThumbnail(this)">
                            <p class="text-muted text-xs mt-1">Upload one image to replace the current main thumbnail.</p>
                        </div>
                    </div>

                    <!-- Multi-Variant Product - Variants Cards -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title mb-3">Product Variants</h3>
                            <p class="text-muted text-sm mb-5">Manage pricing, stock, and images for each variant</p>
                        </div>
                        <div class="card-content">
                            <?php if (!empty($data['variants'])): ?>
                                <!-- Attributes Summary -->
                                <div class="p-4 mb-4" style="background: #f9fafb; border-radius: var(--radius); border: 1px solid var(--border);">
                                    <h4 class="font-medium mb-2">Product Attributes:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($data['attributes'] as $attribute): ?>
                                            <div class="inline-flex items-center px-3 py-2 text-sm" style="background: white; border: 1px solid var(--border); border-radius: var(--radius);">
                                                <strong class="mr-1"><?= htmlspecialchars($attribute['name']) ?>:</strong>
                                                <?php
                                                $values = array_map(function ($v) {
                                                    return htmlspecialchars($v['value']);
                                                }, $attribute['values']);
                                                echo implode(', ', $values);
                                                ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Variants Cards -->
                                <div class="variants-grid">
                                    <?php foreach ($data['variants'] as $variant): ?>
                                        <div class="variant-card">
                                            <div class="variant-card-header">
                                                <h4 class="text-base font-semibold mb-1"><?= htmlspecialchars($variant['variant_name']) ?></h4>
                                                <span class="text-sm" style="color: #6b7280;">SKU: <?= htmlspecialchars($variant['sku']) ?></span>
                                            </div>

                                            <div class="p-5">
                                                <!-- Variant Image -->
                                                <div class="mb-5">
                                                    <label class="form-label">Variant Image</label>
                                                    <div class="mt-2 mb-3">
                                                        <div class="variant-image-preview" id="variant-preview-<?= $variant['id'] ?>">
                                                            <?php if (!empty($variant['image'])): ?>
                                                                <img src="<?= ROOT . htmlspecialchars($variant['image']) ?>"
                                                                    alt="Variant image"
                                                                    class="variant-card-image">
                                                            <?php else: ?>
                                                                <div class="variant-card-image-placeholder">
                                                                    <i class="fa-solid fa-image"></i>
                                                                    <span class="text-sm">No image</span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <input
                                                        type="file"
                                                        id="variant-image-<?= $variant['id'] ?>"
                                                        name="variant_images[<?= $variant['id'] ?>]"
                                                        class="variant-image-input"
                                                        accept="image/*"
                                                        onchange="previewVariantImage(this, '<?= $variant['id'] ?>')">
                                                </div>

                                                <!-- Price and Stock -->
                                                <div class="grid grid-cols-1 gap-4">
                                                    <div class="mb-3">
                                                        <p class="mb-1">
                                                            <i class="fa-solid fa-dollar-sign" style="color: #16a34a; margin-right: 0.5rem;"></i>Price (USD)
                                                        </p>
                                                        <input
                                                            type="number"
                                                            name="variants[<?= $variant['id'] ?>][price]"
                                                            class="input"
                                                            step="0.01"
                                                            min="0"
                                                            placeholder="0.00"
                                                            value="<?= htmlspecialchars($variant['price']) ?>"
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <p class="mb-1">
                                                            <i class="fa-solid fa-boxes-stacked" style="color: #f59e0b; margin-right: 0.5rem;"></i>Stock Quantity
                                                        </p>
                                                        <input
                                                            type="number"
                                                            name="variants[<?= $variant['id'] ?>][stock]"
                                                            class="input"
                                                            step="1"
                                                            min="0"
                                                            placeholder="0"
                                                            value="<?= htmlspecialchars($variant['stock_quantity']) ?>"
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <p class="mb-1">
                                                            <i class="fa-solid fa-circle-exclamation" style="color: #e20000; margin-right: 0.5rem;"></i>
                                                            Low Stock Alert
                                                        </p>
                                                        <input
                                                            type="number"
                                                            name="variants[<?= $variant['id'] ?>][low_stock_alert]"
                                                            class="input"
                                                            step="1"
                                                            min="0"
                                                            placeholder="0"
                                                            value="<?= htmlspecialchars($variant['low_stock_alert']) ?>"
                                                            required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No variants found for this product.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Actions Card -->
                <div class="flex gap-3 items-center ">
                    <button type="submit" class="btn btn-primary mt-3 mr-3">
                        <i class="fa-solid fa-save pr-2"></i> Save Changes
                    </button>
                    <a href="<?= ROOT ?>dashboard/products/all"
                        class="btn btn-secondary mt-3 mr-3"
                        style="text-decoration: none;">
                        <i class="fa-solid fa-times pr-2"></i> Cancel
                    </a>
                    <button type="button"
                        onclick="deleteProduct('<?= $data['product']['id'] ?>')"
                        class="btn btn-destructive mt-3 mr-3">
                        <i class="fa-solid fa-trash pr-2"></i> Delete Product
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        const quill = new Quill('#description-editor', {
            theme: 'snow',
            placeholder: 'Enter product description',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        const initialDescription = <?= json_encode($data['product']['description'] ?? '') ?>;
        quill.root.innerHTML = initialDescription;

        // Preview product images when selected (for single variant products)
        function previewProductImages(input) {
            const previewContainer = document.getElementById('product-images-preview');
            previewContainer.innerHTML = '';

            if (input.files && input.files.length > 0) {
                previewContainer.style.display = 'grid';

                Array.from(input.files).forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'relative';
                        previewDiv.style.cssText = 'border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--border); background: #f9fafb;';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" 
                                alt="Preview ${index + 1}" 
                                class="w-full h-48" 
                                style="object-fit: cover; border-radius: 0.5rem;">
                            <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(0,0,0,0.6); color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
                                New
                            </div>
                        `;
                        previewContainer.appendChild(previewDiv);
                    };

                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.style.display = 'none';
            }
        }

        // Preview variant image when selected
        function previewVariantImage(input, variantId) {
            const previewContainer = document.getElementById('variant-preview-' + variantId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Create new image element
                    previewContainer.innerHTML = `
                        <img src="${e.target.result}" 
                            alt="Variant image preview" 
                            class="variant-card-image">
                    `;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewMainThumbnail(input) {
            const previewContainer = document.getElementById('main-thumbnail-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <img src="${e.target.result}"
                            alt="Main thumbnail preview"
                            style="display: block; width: 100%; height: 200px; object-fit: cover;">
                    `;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Remove image function
        function removeImage(imageId, buttonEl) {
            if (confirm('Are you sure you want to delete this image?')) {
                const removedInputsContainer = document.getElementById('removed-images-inputs');

                // Prevent duplicate hidden inputs for the same image id.
                if (!removedInputsContainer.querySelector(`input[value="${imageId}"]`)) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'removed_images[]';
                    hiddenInput.value = imageId;
                    removedInputsContainer.appendChild(hiddenInput);
                }

                const imageCard = buttonEl.closest('.relative');
                if (imageCard) {
                    imageCard.style.display = 'none';
                }
            }
        }

        // Delete product function
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                // Create a form and submit it as POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= ROOT ?>dashboard/products/delete';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'product_id';
                input.value = productId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Form validation
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const descriptionHtml = quill.root.innerHTML.trim();
            const descriptionText = quill.getText().trim();

            if (!name) {
                e.preventDefault();
                alert('Product name is required');
                return false;
            }

            if (!descriptionText.length) {
                e.preventDefault();
                alert('Description is required');
                return false;
            }

            const numericInputs = this.querySelectorAll('input[type="number"]');
            for (const input of numericInputs) {
                if (input.value === '') {
                    continue;
                }

                const numericValue = Number(input.value);
                if (!Number.isFinite(numericValue) || numericValue < 0) {
                    e.preventDefault();
                    alert('Price, stock quantity, and low stock alert cannot be negative.');
                    input.focus();
                    return false;
                }
            }

            document.getElementById('description').value = descriptionHtml;

            // Confirm before saving
            if (!confirm('Are you sure you want to save these changes?')) {
                e.preventDefault();
                return false;
            }
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>