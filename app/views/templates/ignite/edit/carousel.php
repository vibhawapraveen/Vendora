<?php
$currentTab = $_GET['tab'] ?? 'carousel';
$tabs = [
    'index' => 'Content',
    'carousel' => 'Carousel',
    'sections' => 'Manage Sections',
];
?>

<div class="tab-bar">
    <?php foreach ($tabs as $tabKey => $tabLabel): ?>
        <a href="?tab=<?= $tabKey ?>" class="tab-link <?= $currentTab === $tabKey ? 'tab-active' : '' ?>">
            <?= $tabLabel ?>
        </a>
    <?php endforeach; ?>
</div>

<form action="" method="post" enctype="multipart/form-data">
    <div class="grid grid-cols-3 gap-8 mt-8">
        <div class="col-span-3">

            <!-- FOOTER SECTION -->
            <div class="grid grid-cols-3 gap-3">
                <?php foreach ($tab_contents['slides'] as $slide): ?>
                    <form action="" method="post">
                        <div class="card grid gap-3" style="height: 350px;">
                            <div style="overflow: hidden;">
                                <img style="width: 100%; max-height: 200px; object-fit: cover; display: block;" src="<?= $slide['background'] ?>" alt="">
                            </div>
                            <div class="">
                                <h3 class=""><?= $slide['title'] ?></h3>
                                <p><?= $slide['subtitle'] ?></p>
                            </div>
                            <div>
                                <p>Featured product - <a href="<?= ROOT ?>dashboard/products/<?= $slide['product_id'] ?>/edit"><?= $slide['name'] ?></a> </p>
                            </div>
                            <hr class="m-0">
                            <div>
                                <input type="hidden" name="METHOD" value="DELETE" id="">
                                <button name="slide_id" value="<?= $slide['id'] ?>" class="btn" type="submit">Remove</button>
                            </div>

                        </div>
                    </form>
                <?php endforeach; ?>

                <div onclick="openModal('caro-modal')" class="card flex items-center justify-center" style="border: 2px solid gray; border-style: dashed; cursor: pointer; min-height: 200px;">
                    <div class="grid items-center justify-center gap-3">
                        <i class="fa-solid fa-2x fa-plus m-auto"></i>
                        <span>
                            Add Carousel Slide
                        </span>
                    </div>
                </div>
            </div>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-overlay" id="caro-modal">
                    <div class="modal" style="max-width: 500px;">
                        <div class="modal-header">Add a Carousel Slide</div>
                        <hr>
                        <div class="modal-body grid gap-3">
                            <div class="">
                                <label for="">Background Image</label>
                                <input id="bgimg" class="input" type="file" accept="image/*" required name="background" id="">
                                <img style="max-width: 100%; max-height: 200px;" id="preview" src="" alt="">
                            </div>

                            <div>
                                <label for="">Title</label>
                                <input required class="input" type="text" name="title">
                            </div>

                            <div>
                                <label for="">Subtitle</label>
                                <input required class="input" type="text" name="subtitle">
                            </div>

                            <div>
                                <label for="">Select Product to Feature</label>
                                <select class="input" name="product_id" id="">
                                    <?php foreach ($tab_contents['products'] as $prod): ?>
                                        <option value="<?= $prod['id'] ?>"><?= $prod['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
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
                                onclick="closeModal('caro-modal')">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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
    })
</script>

<script src="<?= ROOT ?>assets/js/components/switch-card.js"></script>