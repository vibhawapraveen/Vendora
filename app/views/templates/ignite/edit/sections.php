<?php
$sections = $tab_contents['section_data'];
$products = $tab_contents['products'];
// pre($sections);

$currentTab = $_GET['tab'] ?? 'carousel';
$tabs = [
    'index' => 'Content',
    'carousel' => 'Carousel',
    'sections' => 'Manage Sections',
];
?>
<style>
    .about-section {
        position: relative;
        height: 300px;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        overflow: hidden;
    }

    .about-section::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.45);
        z-index: 1;
    }

    .about-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
        padding: 2rem;
    }

    .about-heading {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0;
    }
</style>

<div class="tab-bar">
    <?php foreach ($tabs as $tabKey => $tabLabel): ?>
        <a href="?tab=<?= $tabKey ?>" class="tab-link <?= $currentTab === $tabKey ? 'tab-active' : '' ?>">
            <?= $tabLabel ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-3 gap-8 mt-8">
    <div class="col-span-3 grid gap-3">

        <?php foreach ($sections as $section): ?>
            <?php if ($section['section_type'] == 'product_feature') { ?>
                <div class="card">
                    <div class="card-subtitle text-center"><?= $section['title'] ?></div>
                    <div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                        <?php foreach ($section['products'] as $prod): ?>
                            <form action="" method="POST">
                                <div class="card">
                                    <h4 class="mb-3"><?= $prod['name'] ?></h4>
                                    <img id="heroPreview" src="<?= ROOT . $prod['image_url'] ?>" style="width: 100%; height: 200px; display: block; object-fit: cover;" class="rounded" alt="">
                                    <div class="mt-3">
                                        <input type="hidden" name="METHOD" value="DELETE">
                                        <button name="feature_item_id" value="<?= $prod['id'] ?>" class="btn btn-sm" type="submit">Remove
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endforeach; ?>
                        <div onclick="openModal('product-modal'); updateProductModalHiddenInput('<?= $section['id'] ?>');" class="card flex items-center justify-center" style="border: 2px solid gray; border-style: dashed; cursor: pointer; min-height: 200px;">
                            <div class="grid items-center justify-center gap-3">
                                <i class="fa-solid fa-2x fa-plus m-auto"></i>
                                <span>
                                    Add New Featured Product
                                </span>
                            </div>
                        </div>
                    </div>
                    <form action="" method="post">
                        <input type="hidden" name="METHOD" value="DELETE" id="">
                        <button name="delete_product_feature_section_id" value="<?= $section['id'] ?>" class="btn btn-sm mt-2" style="color: var(--destructive);">Delete Section</button>
                    </form>
                </div>
            <?php } elseif ($section['section_type'] == 'promotional_banner') { ?>
                <div class="card">
                    <form action="" method="post">
                        <section class="about-section" style="background-image: url('<?= $section['background_image'] ?>')">
                            <div class="about-content">
                                <h2 class="about-heading"><?= $section['title'] ?></h2>
                            </div>
                        </section>
                        <input type="hidden" name="METHOD" value="DELETE" id="">
                        <button name="delete_promotional_section_id" value="<?= $section['id'] ?>" type="submit" class="btn btn-sm mt-2" style="color: var(--destructive);">Delete Section</button>
                    </form>
                </div>
            <?php } ?>
        <?php endforeach; ?>

        <div onclick="openModal('section-select-modal')" class="card flex items-center justify-center" style="border: 2px solid gray; border-style: dashed; cursor: pointer; min-height: 200px;">
            <div class="grid items-center justify-center gap-3">
                <i class="fa-solid fa-2x fa-plus m-auto"></i>
                <span>
                    Add New Section
                </span>
            </div>
        </div>
    </div>
</div>


<!-- Type Selection Modal -->
<div class="modal-overlay" id="section-select-modal">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">Select a Section</div>
        <div class="modal-body">
            <div class="card flex gap-3">
                <div onclick="openModal('promotional-modal'); closeModal('section-select-modal')" class="card flex flex-1 items-center justify-center" style="border: 2px solid gray; border-style: dashed; cursor: pointer; min-height: 200px;">
                    <div class="grid items-center justify-center gap-3">
                        <i class="fa-solid fa-2x fa-bullhorn m-auto"></i>
                        <span>
                            Promotional Banner
                        </span>
                    </div>
                </div>
                <div onclick="openModal('productshowcase-modal');  closeModal('section-select-modal');" class="card flex flex-1 items-center justify-center" style="border: 2px solid gray; border-style: dashed; cursor: pointer; min-height: 200px;">
                    <div class="grid items-center justify-center gap-3">
                        <i class="fa-solid fa-2x fa-cubes m-auto"></i>
                        <span>
                            Product Showcase
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-secondary btn-sm"
                onclick="closeModal('section-select-modal');">
                Close
            </button>
        </div>
    </div>
</div>


<!-- Promotional Banner Add -->
<div class="modal-overlay" id="promotional-modal">
    <div class="modal" style="max-width: 500px;">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="modal-header">Add a New Promotional Banner</div>
            <hr>
            <div class="modal-body">
                <div class="grid gap-3">
                    <div class="">
                        <label for="">Background Image</label>
                        <input type="hidden" name="section_type" value="promotional_banner">
                        <input id="bgimg" class="input" type="file" accept="image/*" required name="background" id="">
                        <img style="max-width: 100%; max-height: 200px;" id="preview" src="" alt="">
                    </div>

                    <div>
                        <label for="">Title</label>
                        <input required class="input" type="text" name="title">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="submit"
                    class="btn btn-primary btn-sm">
                    Add
                </button>
                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    onclick="closeModal('promotional-modal')">
                    Close
                </button>
        </form>
    </div>
</div>
</div>

<!-- Product Showcase Add -->
<div class="modal-overlay" id="productshowcase-modal">
    <div class="modal" style="max-width: 500px;">
        <form action="" method="POST">
            <div class="modal-header">Add a New Product Showcase</div>
            <div class="modal-body">
                <div class=" gap-3">
                    <div>
                        <label for="">Product Showcase Title</label>
                        <input type="hidden" name="section_type" value="product_feature">
                        <input required class="input" type="text" name="title">
                    </div>
                    <div class="mt-5">
                        <label class="text-muted">You can feature products later</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm">Add</button>
                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    onclick="closeModal('productshowcase-modal')">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Add new product to section -->
<div class="modal-overlay" id="product-modal">
    <div class="modal" style="max-width: 1000px;">
        <div class="modal-header">Pick a Product</div>
        <div class="modal-body">
            <div class="grid" style="max-height: 80vh; overflow: auto; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <?php foreach ($products as $prod): ?>
                    <form action="" method="post">
                        <div class="card">
                            <h4 class="mb-3"><?= $prod['name'] ?></h4>
                            <img id="heroPreview" src="<?= ROOT . $prod['image_url'] ?>" style="width: 100%; height: 200px; display: block; object-fit: cover;" class="rounded" alt="">
                            <div class="mt-3">
                                <input value="" type="hidden" name="product_feature_section_id" class="product_feature_section_id">
                                <button name="product_id" value="<?= $prod['id'] ?>" class="btn" type="submit">Select
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-secondary btn-sm"
                onclick="closeModal('product-modal')">
                Close
            </button>
        </div>
    </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script src="<?= ROOT ?>assets/js/components/toast.js"></script>
<script src="<?= ROOT ?>assets/js/components/modal.js"></script>
<script>
    // Check for success query parameter and show toast
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');

    if (successMessage) {
        showToast("Storefront updated!");

        // Optional: Remove the query parameter from URL without page reload
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, '', url);
    }

    const bgimg = document.getElementById("bgimg");
    const preview = document.getElementById("preview");
    bgimg.addEventListener("change", () => {
        console.log("Sdsd");
        const file = bgimg.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    function updateProductModalHiddenInput(section_id) {
        console.log(section_id);
        const productModal = document.getElementById('product-modal');
        const hiddenInuts = productModal.querySelectorAll('.product_feature_section_id');
        hiddenInuts.forEach((e) => {
            e.value = section_id;
        })
    }
</script>

<script src="<?= ROOT ?>assets/js/components/switch-card.js"></script>