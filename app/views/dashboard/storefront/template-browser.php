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
      <h2 class="font-semibold">Choose Your Store Template</h2>
      <p class="text-muted">Pick a template which matches your store vibes!</p>

      <div class="mt-8" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; max-width: 1500px;">
        <?php foreach ($templates as $row) { ?>
          <div>

            <div class="cursor-pointer" onclick="updateModal('<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>','<?= $row['id'] ?>','<?= $row['file_path'] ?>')">
              <div style="width: 100%; aspect-ratio: 1366 / 768; overflow: hidden; border-radius: 8px; background: #f0f0f0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <img src="http://localhost/vendora/public/assets/img/templates/<?=$row['file_path']?>.png" alt="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="card-subtitle text-xl mt-2">
                  <?= $row['name'] ?>
                </div>
                <!-- <a href=<?= ROOT . "previewtemplate?template_id=" . $row['id'] ?> target="_blank" class="text-sm">
                  Preview ↗️
                </a> -->
              </div>
          </div>
        <?php } ?>
      </div>


      <div class="modal-overlay" id="demo-modal">
        <div class="modal">
          <div class="modal-header text-xl">Template Name</div>
          <div class="modal-body">
            <div style="width: 100%; max-width: 900px; aspect-ratio: 1366 / 768; overflow: hidden; border-radius: 8px; background: #f0f0f0; margin: 0 auto;">
              <img id="modalTemplateImage" src="http://localhost/vendora/public/assets/img/templates/lights.png" alt="Template Preview" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
          </div>
          <div class="modal-footer">
            <form action="" method="post">
              <input hidden id="templateId" name="templateId" type="text" value="">
              <button
              type="button"
                class="btn btn-secondary"
                onclick="closeModal('demo-modal')">
                Close
              </button>
              <button type="submit" class="btn btn-primary">Use Template</button>
            </form>
          </div>
        </div>
      </div>

    </main>
  </div>


  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/modal.js"></script>
  <script src="<?= ROOT ?>assets/js/custom/storefront/index.js"></script>
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>


  <script>
    function updateModal(templateName,templateId, filePath) {
      document.querySelector('#demo-modal .modal-header').textContent = templateName;
      document.getElementById("templateId").value = templateId;
      document.getElementById("modalTemplateImage").src = `http://localhost/vendora/public/assets/img/templates/${filePath}.png`;
      openModal('demo-modal');
    }
  </script>
</body>

</html>