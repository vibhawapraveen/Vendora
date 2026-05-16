<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Product</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/new.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>
        <main class="content">
            <!-- Title Section -->
            <div class="flex items-center">
                <div>
                    <div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-cart-shopping text-xl"></i>
                            <h2 class="font-semibold">Add New Product</h2>
                        </div>
                        <p class="text-muted text-sm">Create a new product listing for your store.</p>
                    </div>
                </div>
            </div>

            <div class="card gap-3 mt-5">
                <div class="card-header mb-3">
                    <div class="card-subtitle">Product Information</div>
                </div>

                <form method="POST" action="<?= ROOT ?>dashboard/products/newproduct">
                    <!-- Product Name -->
                    <div class="mb-5">
                        <p class=" mb-1">Product Name</p>
                        <input type="text" name="product_name" class="input" placeholder="Enter product name" required />
                    </div>

                    <!-- Category -->
                    <div class="mb-5">
                        <p class="mb-1">Category</p>
                        <select name="category_id" class="input">
                            <option value="">Select category</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['id']) ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <a href="<?= ROOT ?>dashboard/products/managecategories" target="_blank" rel="noopener" class="text-sm" style="display: inline-flex; align-items: center; gap: 6px; margin-top: 8px; color: #2563eb; text-decoration: underline;">
                            <i class="fa-solid fa-plus"></i> Add a new category
                        </a>
                    </div>

                    <!-- Description -->
                    <div class="mb-5">
                        <p class="mb-1">Description</p>
                        <div id="description-editor" style="height: 220px;"></div>
                        <input type="hidden" name="description" id="description" required>
                    </div>
            </div>

            <div class="card mt-5 gap-3">
                <div class="card-header mb-3">
                    <div class="card-subtitle">Product Variant Type</div>
                    <p class="text-muted text-sm mt-1">Choose whether this product has variations (size, color, etc.) or is a single product</p>
                </div>

                <div class="flex items-center gap-6 mb-4">
                    <div class="mt-5 flex">
                        <div class="switch-group">
                            <label class="switch-label" style="width: 400px">
                                <input type="radio" name="is_variant" value="false" checked />
                                <div class="switch-title">Simple Mode</div>
                                <div class="switch-desc">Single Price and Stock</div>
                            </label>

                            <label class="switch-label" style="width: 400px">
                                <input type="radio" name="is_variant" value="true" />
                                <div class="switch-title">Multi Variant Mode</div>
                                <div class="switch-desc">
                                    Multiple variants with prices and stocks
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <button type="submit" class="btn btn-primary mt-5 mr-3">
                <p class="px-2">Next</p>
            </button>
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

        const form = document.querySelector('form[action$="dashboard/products/newproduct"]');
        const descriptionInput = document.getElementById('description');

        form.addEventListener('submit', function(e) {
            const html = quill.root.innerHTML.trim();
            const text = quill.getText().trim();

            if (!text.length) {
                e.preventDefault();
                alert('Description is required');
                return;
            }

            descriptionInput.value = html;
        });

        const params = new URLSearchParams(window.location.search);
        if (window.location.pathname.endsWith('/dashboard/products/newproduct') && params.get('error') === '1') {
            alert('Error!');
        }
    </script>
</body>

</html>