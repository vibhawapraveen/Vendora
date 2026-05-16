<?php
foreach ($store_contents as $item) {
    $store_values[$item['field_name']] = $item['field_value'];
}
?>
<form action="" method="post">
    <div class="grid grid-cols-3 gap-8 mt-8">
        <div class="col-span-2">
            <div class="card">
                <div>
                    <div class="card-subtitle">
                        Customize content
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div>
                        <label class="">Navbar text</label>
                        <input name="navbar_text_totem" type="text" class="input" value="<?php if(isset($store_values['navbar_text_totem'])) {echo $store_values['navbar_text_totem'];} ?>">
                    </div>
                    <div>
                        <label class="">Heading</label>
                        <input name="heading_totem" type="text" class="input" value="<?php if(isset($store_values['heading_totem'])) {echo $store_values['heading_totem'];} ?>">
                    </div>
                    <div>
                        <label class="">Paragraph</label>
                        <input name="paragraph_totem" type="text" class="input" value="<?php if(isset($store_values['paragraph_totem'])) {echo $store_values['paragraph_totem'];} ?>">
                    </div>
                </div>
            </div>

            <div class="card mt-5">
                <div>
                    <div class="card-subtitle">
                        Primary theme color
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div class="flex">
                        <div class="switch-group">
                            <label class="switch-label <?= (isset($store_values['primary_color_totem']) && $store_values['primary_color_totem'] === '#8b4513') ? 'selected' : '' ?>" style="width: 160px">
                                <input value="#8b4513" type="radio" name="primary_color_totem" <?= (isset($store_values['primary_color_totem']) && $store_values['primary_color_totem'] === '#8b4513') ? 'checked' : '' ?> />
                                <div class="switch-title">Saddle Brown</div>
                                <div style="background-color: #8b4513; width: 100%; height: 40px; border-radius: 5px;"></div>
                            </label>

                            <label class="switch-label <?= (isset($store_values['primary_color_totem']) && $store_values['primary_color_totem'] === '#1a472a') ? 'selected' : '' ?>" style="width: 160px">
                                <input value="#1a472a" type="radio" name="primary_color_totem" <?= (isset($store_values['primary_color_totem']) && $store_values['primary_color_totem'] === '#1a472a') ? 'checked' : '' ?> />
                                <div class="switch-title">Forest Green</div>
                                <div style="background-color: #1a472a; width: 100%; height: 40px; border-radius: 5px;"></div>
                            </label>

                            <label class="switch-label <?= (isset($store_values['primary_color_totem']) && $store_values['primary_color_totem'] === '#1e3a5f') ? 'selected' : '' ?>" style="width: 160px">
                                <input value="#1e3a5f" type="radio" name="primary_color_totem" <?= (isset($store_values['primary_color_totem']) && $store_values['primary_color_totem'] === '#1e3a5f') ? 'checked' : '' ?> />
                                <div class="switch-title">Navy Blue</div>
                                <div style="background-color: #1e3a5f; width: 100%; height: 40px; border-radius: 5px;"></div>
                            </label>

                            <label class="switch-label <?= (isset($store_values['primary_color_totem']) && $store_values['primary_color_totem'] === '#6b2c3e') ? 'selected' : '' ?>" style="width: 160px">
                                <input value="#6b2c3e" type="radio" name="primary_color_totem" <?= (isset($store_values['primary_color_totem']) && $store_values['primary_color_totem'] === '#6b2c3e') ? 'checked' : '' ?> />
                                <div class="switch-title">Burgundy</div>
                                <div style="background-color: #6b2c3e; width: 100%; height: 40px; border-radius: 5px;"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <button type="button" class="btn btn-outline font-normal">Discard</button>
                <button type="submit" class="btn btn-primary font-normal">Update</button>
            </div>
        </div>
        <div class="">
            <!-- <div>
                <div style="background: url(https://placehold.co/100); height:200px" class="w-full">
                    <img src="" alt="">
                </div>
                <div class="card-subtitle text-muted mt-2">
                    Totem
                </div>
                <button type="button" class="btn btn-link">Preview</button>
            </div> -->
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
</script>

<script src="<?= ROOT ?>assets/js/components/switch-card.js"></script>