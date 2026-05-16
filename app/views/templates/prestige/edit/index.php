<?php
$currentTab = $_GET['tab'] ?? 'index';
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
            <!-- NAVBAR CONTENT -->
            <div class="card" style="width: fit-content;">
                <div>
                    <div class="card-subtitle">
                        Branding / Navbar Content
                    </div>
                    <hr>
                </div>
                <div class="flex" style="gap: 40px;">
                    <div class="">
                        <label class="">Web Page Title</label>
                        <input name="title" type="text" class="input" value="<?= $tab_contents['title'] ?>">
                    </div>
                    <div class="grid gap-3">
                        <div>
                            <label class="">Select Color</label>
                            <input type="color" name="primary_color" class="input" value="<?= isset($tab_contents['primary_color']) && !empty($tab_contents['primary_color']) ? strtolower($tab_contents['primary_color']) : '#2b7fff' ?>" style="width: 100px; height: 50px; cursor: pointer; border: none;">
                        </div>
                    </div>
                    <div class="grid gap-3">
                        <label class="">Logo</label>
                        <img id="logoPreview" src="<?= $tab_contents['logo'] ?>" class="max-w-lg rounded" style="max-height: 50px;" alt="">
                        <input type="file" id="logoInput" name="logo" accept="image/*" hidden>
                        <input type="text" name="logo" value="<?= $tab_contents['logo'] ?>" hidden>
                        <button type="button" id="logoChangeBtn" class="btn btn-link">Change</button>
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
                    <div class="">
                        <label class="">Footer Title</label>
                        <input name="footer_title" type="text" class="input" value="<?= $tab_contents['footer_title'] ?>">
                    </div>
                    <div>
                        <label class="">Footer Description</label>
                        <textarea name="footer_text" class="input" rows="3"><?= $tab_contents['footer_text'] ?></textarea>
                    </div>
                    <div class="flex gap-3">

                        <div class="flex-1">
                            <label class="">Facebook URL</label>
                            <input name="fb_url" type="text" class="input" value="<?= $tab_contents['fb_url'] ?>">
                        </div>
                        <div class="flex-1">
                            <label class="">Instagram URL</label>
                            <input name="insta_url" type="text" class="input" value="<?= $tab_contents['insta_url'] ?>">
                        </div>
                    </div>
                    <div>
                        <label class="">Copyright Text</label>
                        <input name="copyright_text" type="text" class="input" value="<?= $tab_contents['copyright_text'] ?>">
                    </div>
                </div>
            </div>

            <!-- THEME COLOR -->
            <!-- <div class="card mt-5">
                <div>
                    <div class="card-subtitle">
                        Theme Color
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div>
                        <label class="">Select Color</label>
                        <input type="color" name="primary_color" class="input" value="<?= isset($tab_contents['primary_color']) && !empty($tab_contents['primary_color']) ? strtolower($tab_contents['primary_color']) : '#2b7fff' ?>" style="width: 100px; height: 50px; cursor: pointer; border: none;">
                    </div>
                </div>
            </div> -->

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

    const LogoChangeBtn = document.getElementById("logoChangeBtn");
    const logoInput = document.getElementById("logoInput");
    const logoPreview = document.getElementById("logoPreview");

    LogoChangeBtn.addEventListener("click", () => {
        logoInput.click();
    });

    logoInput.addEventListener("change", () => {
        const file = logoInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<script src="<?= ROOT ?>assets/js/components/switch-card.js"></script>