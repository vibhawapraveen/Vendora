<?php
$currentTab = $_GET['tab'] ?? 'featured';
$tabs = [
    'index' => 'Content',
    'featured' => 'Featured Products'
];
?>

<div class="tab-bar">
    <?php foreach ($tabs as $tabKey => $tabLabel): ?>
        <a href="?tab=<?= $tabKey ?>" class="tab-link <?= $currentTab === $tabKey ? 'tab-active' : '' ?>">
            <?= $tabLabel ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="mt-5">
    <form action="" method="post">
        <div class="grid grid-cols-3" style="gap: 1rem;">
            <?php foreach ($tab_contents['featured_products'] as $prod): ?>
                <div class="card">
                    <input name="METHOD" value="DELETE" hidden>
                    <h4 class="mb-3"><?= $prod['name'] ?></h4>
                    <img id="heroPreview" src="<?= ROOT . $prod['image_url'] ?>" style="width: 100%; height: 200px; display: block; object-fit: cover;" class="rounded" alt="">
                    <div class="mt-3">
                        <button name="product_id"  value="<?= $prod['id'] ?>" class="btn" type="submit">Remove
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (!empty($tab_contents['not_featured_products'])) { ?>
                <div onclick="openModal('feat-modal')" class="card flex items-center justify-center" style="border: 2px solid gray; border-style: dashed; cursor: pointer; min-height: 200px;">
                    <div class="grid items-center justify-center gap-3">
                        <i class="fa-solid fa-2x fa-plus m-auto"></i>
                        <span>
                            Add New Featured Product
                        </span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </form>

    <form action="" method="post">
        <div class="modal-overlay" id="feat-modal">
            <div class="modal" style="max-width: 1000px;">
                <div class="modal-header">Pick a Product</div>
                <div class="modal-body">
                <div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                    <?php foreach ($tab_contents['not_featured_products'] as $prod): ?>
                            <div class="card">
                                <h4 class="mb-3"><?= $prod['name'] ?></h4>
                                <img id="heroPreview" src="<?= ROOT . $prod['image_url'] ?>" style="width: 100%; height: 200px; display: block; object-fit: cover;" class="rounded" alt="">
                                <div class="mt-3">
                                    <button name="product_id"  value="<?= $prod['id'] ?>" class="btn" type="submit">Select
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary btn-sm"
                        onclick="closeModal('feat-modal')">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<form action="" method="post" enctype="multipart/form-data">
    <div class="grid grid-cols-3 gap-8 mt-8">
        <div class="col-span-3">

            <!-- HERO SECTION -->


            <!-- ACTION BUTTONS -->
            <!-- <div class="mt-5">
                <button type="button" class="btn btn-outline font-normal">Discard</button>
                <button type="submit" class="btn btn-primary font-normal">Update</button>
            </div> -->
        </div>
    </div>
</form>

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

    const changeBtn = document.getElementById("changeBtn");
    const heroInput = document.getElementById("heroInput");
    const preview = document.getElementById("heroPreview");


    const PromotionalChangeBtn = document.getElementById("promotionalChangeBtn");
    const promotionalInput = document.getElementById("promotionalInput");
    const promotionalPreview = document.getElementById("promotionalPreview");

    // Open file picker
    PromotionalChangeBtn.addEventListener("click", () => {
        promotionalInput.click();
    });

    // Open file picker
    changeBtn.addEventListener("click", () => {
        heroInput.click();
    });

    // Preview selected image
    heroInput.addEventListener("change", () => {
        const file = heroInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    promotionalInput.addEventListener("change", () => {
        const file = promotionalInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                promotionalPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<script src="<?= ROOT ?>assets/js/components/switch-card.js"></script>