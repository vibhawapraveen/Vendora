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
      <h2 class="font-semibold">Store Template</h2>
      <p class="text-muted">Here is your current template. you can customize it further with your store name, colors, and content.</p>

      <div class="mt-5">
        <div class="" style="max-width: 600px;">
          <div style="width: 100%; aspect-ratio: 1366/768; background: url('http://localhost/vendora/public/assets/img/templates/<?= $template['file_path'] ?>.png') center/cover no-repeat; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
          </div>
          <div class="card-subtitle text-muted mt-2">
            <?= $template['name'] ?>
          </div>
          <div class="mt-3 flex gap-3">
            <a href="customize" style="text-decoration: none;" class="btn btn-primary font-normal">
              <i class="fa-solid fa-sliders mr-2"></i>
              Customize</a>
            <form action="<?= ROOT ?>dashboard/storefront/template/delete" method="post">
              <button href=<?= ROOT . "previewtemplate?template_id=" . $template['template_id'] ?> target="_blank" class="btn btn-outline font-normal">Remove
                <i class="fa-solid fa-trash ml-2"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div id="toast-container" class="toast-container"></div>

  <script src="<?= ROOT ?>assets/js/components/toast.js"></script>
  <script>
    // Check for success query parameter and show toast
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');

    if (successMessage) {
      showToast("Template picked successfully");

      // Optional: Remove the query parameter from URL without page reload
      const url = new URL(window.location);
      url.searchParams.delete('success');
      window.history.replaceState({}, '', url);
    }
  </script>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>