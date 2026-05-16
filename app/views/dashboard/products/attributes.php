<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Attributes</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/attributes.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>

        <main class="content">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active">
                    <i class="fa-solid fa-tags"></i>
                    <span>Attributes</span>
                </div>
                <div class="step pending">
                    <i class="fa-solid fa-list"></i>
                    <span>Variants</span>
                </div>
                <div class="step pending">
                    <i class="fa-solid fa-image"></i>
                    <span>Media Upload</span>
                </div>
                <div class="step pending">
                    <i class="fa-solid fa-flag-checkered"></i>
                    <span>Complete</span>
                </div>
            </div>

            <!-- Title Section -->
            <div class="flex items-center mb-5">
                <h1 class="page-title">Manage attributes</h1>
            </div>

            <div class="card gap-3">
                <!-- Header with description and Add button -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="section-title mb-2">Attributes</h2>
                        <p class="section-description">
                            Define attributes like Color, Size, Storage. Add values for<br>
                            each. Variants will be generated from all combinations.
                        </p>
                    </div>
                    <button type="button" class="btn btn-outline btn-add-attribute" onclick="addAttribute()">
                        <span>+</span>
                        <span>Add attribute</span>
                    </button>
                </div>

                <form id="attributesForm" method="POST" action="<?= ROOT ?>dashboard/products/newproduct/<?= $data['product_id'] ?>/attributes" data-root="<?= ROOT ?>" data-product-id="<?= $data['product_id'] ?>" data-existing-attributes='<?= !empty($data['existingAttributes']) ? json_encode($data['existingAttributes']) : "" ?>'>
                    <!-- Attributes Container -->
                    <div id="attributesContainer">
                        <!-- Example attribute (Color) - empty values -->
                        <div class="attribute-item mb-4">
                            <div class="mb-4">
                                <label class="attribute-label mb-2">Attribute name</label>
                                <div class="attribute-input-group">
                                    <div class="attribute-input-wrapper">
                                        <input type="text" class="input attribute-input" placeholder="Enter attribute name">
                                    </div>
                                    <button type="button" class="delete-btn" onclick="removeAttribute(this)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="attribute-label mb-3">Values</label>
                                <div class="values-container mb-4">
                                    <!-- Empty - no pre-filled values -->
                                </div>

                                <div class="value-input-container">
                                    <input type="text" placeholder="Add value and press Enter" class="input value-input" onkeypress="handleValueInput(event, this)">
                                    <button type="button" class="btn btn-outline add-btn" onclick="addValue(this)">
                                        <span class="text-sm">+</span>
                                        <span>Add</span>
                                    </button>
                                </div>

                                <p class="tip-text mt-2">
                                    Tip: Keep values concise, e.g., Midnight, 512GB, Large.
                                </p>
                            </div>
                        </div>


                    </div>
                    <!-- Next Button -->
                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary">
                            Next
                        </button>
                    </div>
            </div>
            </form>
    </div>
    </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/products/attributes.js"></script>
</body>

</html>