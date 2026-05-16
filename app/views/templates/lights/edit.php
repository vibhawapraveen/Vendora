<?php
foreach ($store_contents as $item) {
    $store_values[$item['field_name']] = $item['field_value'];
}
?>
<form action="" method="post">
    <div class="grid grid-cols-3 gap-8 mt-8">
        <div class="col-span-3">
            <div class="card">
                <div>
                    <div class="card-subtitle">
                        Customize content
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div>
                        <label class="">Heading</label>
                        <input name="heading" type="text" class="input" value="<?php if(isset($store_values['heading'])) {echo $store_values['heading'];} ?>">
                    </div>
                    <div>
                        <label class="">Sub-heading</label>
                        <input name="subheading" type="text" class="input" value="<?php if(isset($store_values['subheading'])) {echo $store_values['subheading'];} ?>">
                    </div>
                    <div>
                        <label class="">Navbar text</label>
                        <input name="navbar_text" type="text" class="input" value="<?php if(isset($store_values['navbar_text'])) {echo $store_values['navbar_text'];} ?>">
                    </div>
                </div>
            </div>

            <div class="card mt-5">
                <div>
                    <div class="card-subtitle">
                        Theme color
                    </div>
                    <hr>
                </div>
                <div class="grid gap-3">
                    <div class="flex">
                        <div class="switch-group">
                            <label class="switch-label <?= (isset($store_values['primary_color']) && $store_values['primary_color'] === '#2b7fff') ? 'selected' : '' ?>" style="width: 150px">
                                <input value="#2b7fff" type="radio" name="primary_color" <?= (isset($store_values['primary_color']) && $store_values['primary_color'] === '#2b7fff') ? 'checked' : '' ?> />
                                <div class="switch-title">Blue</div>
                                <div style="background-color: #2b7fff; width: 100%; height: 40px; border-radius: 5px;"></div>
                            </label>

                            <label class="switch-label <?= (isset($store_values['primary_color']) && $store_values['primary_color'] === '#00c951') ? 'selected' : '' ?>" style="width: 150px">
                                <input value="#00c951" type="radio" name="primary_color" <?= (isset($store_values['primary_color']) && $store_values['primary_color'] === '#00c951') ? 'checked' : '' ?> />
                                <div class="switch-title">Green</div>
                                <div style="background-color: #00c951; width: 100%; height: 40px; border-radius: 5px;"></div>
                            </label>

                            <label class="switch-label <?= (isset($store_values['primary_color']) && $store_values['primary_color'] === '#171717') ? 'selected' : '' ?>" style="width: 150px">
                                <input value="#171717" type="radio" name="primary_color" <?= (isset($store_values['primary_color']) && $store_values['primary_color'] === '#171717') ? 'checked' : '' ?> />
                                <div class="switch-title">Black</div>
                                <div style="background-color: #171717; width: 100%; height: 40px; border-radius: 5px;"></div>
                            </label>

                            <label class="switch-label <?= (isset($store_values['primary_color']) && $store_values['primary_color'] === '#ff6900') ? 'selected' : '' ?>" style="width: 150px">
                                <input value="#ff6900" type="radio" name="primary_color" <?= (isset($store_values['primary_color']) && $store_values['primary_color'] === '#ff6900') ? 'checked' : '' ?> />
                                <div class="switch-title">Orange</div>
                                <div style="background-color: #ff6900; width: 100%; height: 40px; border-radius: 5px;"></div>
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