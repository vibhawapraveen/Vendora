<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>

        <main class="content">
            <h2 class="font-semibold">Customize your template</h2>
            <p class="text-muted">Customize your selected template further with your store name, colors, and content.</p>

            <!-- <div class="grid grid-cols-3 gap-8 mt-8">
                <div class="col-span-2">
                    <div class="card grid gap-5">
                        <div>
                            <label class="">Heading</label>
                            <input type="text" class="input">
                        </div>

                        <div>
                            <label class="">Subheading</label>
                            <input type="text" class="input">
                        </div>

                        <div>
                            <label class="">Subheading</label>
                            <input type="color" class="">
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="cursor-pointer">
                        <div style="background: url(https://placehold.co/100); height:200px" class="w-full">
                            <img src="" alt="">
                        </div>
                        <div class="card-subtitle text-muted mt-2">
                            Lolipop
                        </div>
                        <div>
                            <button class="btn btn-outline font-normal">Save & Preview</button>
                        </div>
                    </div>
                </div>
            </div> -->
            
            <?php require '../app/views/templates/'.$file_path.'/edit/'.$tab.'.php' ?>

        </main>
    </div>


    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <script src="<?= ROOT ?>assets/js/custom/storefront/index.js"></script>
    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>