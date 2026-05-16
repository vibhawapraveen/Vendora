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

            <div class="alert alert-info mt-5" style="margin-bottom: 2rem">
                <div class="alert-icon" style="margin: auto;">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="alert-content">
                    <div class="alert-description">You have to pick a template before customizing.</div>
                    <a class="btn btn-outline btn-sm mt-1" href="./template">Browse Templates</a>

                </div>
            </div>

        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/lucide.js"></script>
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <script src="<?= ROOT ?>assets/js/custom/storefront/index.js"></script>
    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>