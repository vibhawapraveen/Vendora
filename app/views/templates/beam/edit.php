<form action="" method="post" enctype="multipart/form-data">
    <div class="grid grid-cols-3 gap-8 mt-8">
        <div class="col-span-3">
            <!-- NAVBAR CONTENT -->
            <!-- <div class="card">
                <div>
                    <div class="card-subtitle">
                        Navbar Content
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div>
                        <label class="">Logo Text</label>
                        <input name="logo_text" type="text" class="input" value="<?php if (isset($store_contents['logo_text'])) {
                                                                                        echo $store_contents['logo_text'];
                                                                                    } ?>">
                    </div>
                </div>
            </div> -->

            <!-- HERO SECTION -->
            <div class="card mt-5">
                <div>
                    <div class="card-subtitle">
                        Hero Section
                    </div>
                    <hr>
                </div>
                <div class="flex" style="gap: 50px;">
                    <div class="grid gap-3 flex-1">
                        <div>
                            <label class="">Heading</label>
                            <input name="heading" type="text" class="input" value="<?= $store_contents['heading'] ?>">
                        </div>
                        <div>
                            <label class="">Sub-heading / Description</label>
                            <textarea name="subheading" class="input" rows="3"><?= $store_contents['subheading'] ?></textarea>
                        </div>
                        <div>
                            <label class="">CTA Button Text</label>
                            <input name="hero_cta_text" type="text" class="input" value="<?= $store_contents['cta_text'] ?>">
                        </div>
                    </div>
                    <div class="grid gap-3">
                        <label class="">Hero Image</label>
                        <img id="heroPreview" src="<?= $store_contents['hero_img'] ?>" class="max-w-lg rounded" alt="">
                        <input type="file" id="heroInput" name="hero_img" accept="image/*" hidden>
                        <input type="text" name="hero_img" value="<?= $store_contents['hero_img'] ?>" hidden>
                        <button type="button" id="changeBtn" class="btn btn-link">Change</button>
                    </div>
                </div>
            </div>

            <!-- FEATURED PRODUCTS SECTION -->
            <div class="card mt-5">
                <div>
                    <div class="card-subtitle">
                        Featured Products Section
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div>
                        <label class="">Section Title</label>
                        <input name="featured_products_title" type="text" class="input" value="<?= $store_contents['featured_products_title'] ?>">
                    </div>
                </div>
            </div>

            <!-- BANNER SECTION -->
            <div class="card mt-5">
                <div>
                    <div class="card-subtitle">
                        Promotional Banner
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3 flex-1">
                    <div>
                        <label class="">Banner Heading</label>
                        <input name="promotional_text" type="text" class="input" value="<?= $store_contents['promotional_text'] ?>">
                    </div>
                </div>
                <div>
                    <div class="grid gap-3">
                        <label class="">Banner Image</label>
                        <img id="promotionalPreview" src="<?= $store_contents['promotional_img'] ?>" class="max-w-lg rounded" alt="">
                        <input type="file" id="promotionalInput" name="promotional_img" accept="image/*" hidden>
                        <input type="text" name="promotional_img" value="<?= $store_contents['promotional_img'] ?>" hidden>
                        <button type="button" id="promotionalChangeBtn" class="btn btn-link" style="width: fit-content;">Change</button>
                    </div>
                </div>
            </div>

            <!-- FOOTER SECTION -->
            <div class="card mt-5">
                <div>
                    <div class="card-subtitle">
                        Footer Content
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div>
                        <label class="">Footer Description</label>
                        <textarea name="footer_text" class="input" rows="3"><?= $store_contents['footer_text'] ?></textarea>
                    </div>
                    <div class="flex gap-3">

                        <div class="flex-1">
                            <label class="">Facebook URL</label>
                            <input name="fb_url" type="text" class="input" value="<?= $store_contents['fb_url'] ?>">
                        </div>
                        <div class="flex-1">
                            <label class="">Facebook URL</label>
                            <input name="insta_url" type="text" class="input" value="<?= $store_contents['insta_url'] ?>">
                        </div>
                    </div>
                    <div>
                        <label class="">Copyright Text</label>
                        <input name="footer_copyright" type="text" class="input" value="<?= $store_contents['footer_copyright'] ?>">
                    </div>
                </div>
            </div>

            <!-- THEME COLOR -->
            <div class="card mt-5">
                <div>
                    <div class="card-subtitle">
                        Theme Color
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div>
                        <label class="">Select Color</label>
                        <input type="color" name="primary_color" class="input" value="<?= isset($store_contents['primary_color']) && !empty($store_contents['primary_color']) ? strtolower($store_contents['primary_color']) : '#2b7fff' ?>" style="width: 100px; height: 50px; cursor: pointer; border: none;">
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="mt-5">
                <button type="button" class="btn btn-outline font-normal">Discard</button>
                <button type="submit" class="btn btn-primary font-normal">Update</button>
            </div>
        </div>
    </div>
</form>

<div id="toast-container" class="toast-container"></div>

<script src="<?= ROOT ?>assets/js/components/toast.js"></script>
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