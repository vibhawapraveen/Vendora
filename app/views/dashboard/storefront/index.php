<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
  <script src="<?= ROOT ?>assets/chartjs/chart.umd.min.js"></script>

</head>
<style>
  /* Social Share Buttons */
  .share-buttons-container {
    padding: 1rem 0;
  }

  .share-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    background-color: #ffffff;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    min-width: 100px;
    color: inherit;
  }

  .share-btn i {
    font-size: 1.5rem;
  }

  .share-btn:hover {
    border-color: #d1d5db;
    background-color: #f9fafb;
    transform: translateY(-2px);
  }

  .share-btn-whatsapp:hover {
    color: #25d366;
    border-color: #25d366;
  }

  .share-btn-facebook:hover {
    color: #1877f2;
    border-color: #1877f2;
  }

  .share-btn-instagram:hover {
    color: #e1306c;
    border-color: #e1306c;
  }
</style>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>

    <main class="content">
      <h2 class="font-semibold">Storefront Overview</h2>
      <p class="text-muted">Manage basic visibility settings for your store</p>

      <div class="mt-10 grid grid-cols-3 gap-3">
        <div class="col-span-2 card">
          <div class="card-header">
            <div class="card-subtitle flex items-center gap-3">Public Store URL <span class="badge badge-sm badge-secondary"><?= $data['code'] ?></span> </div>
          </div>
          <div class="card-content">
            <div class="flex" id="store-url-card">
              <input id="store-url" value="http://localhost/vendora/public/<?= $data['code'] ?>" disabled type="text" class="input">
              <button class="btn"><i class="fa-solid fa-copy"></i></button>
              <button type="button" onclick="openModal('share-modal')" class="btn btn-primary"><i class="fa-solid fa-arrow-up-right-from-square"></i></button>
            </div>

            <!-- Edit button -->
            <button class="btn btn-outline btn-sm mt-2" onclick="openModal('edit-code')">Edit Code</button>

            <!-- Hidden edit field -->
            <div id="edit-code-form" class="mt-3 hidden">
              <div class="flex items-center">
                <div>http://localhost/vendora/public/</div>
                <input type="text" id="code-input" class="input" value="<?= $data['code'] ?>">
              </div>
              <div class="flex gap-2">
                <button class="btn btn-primary btn-sm" id="save-code-btn">Save</button>
                <button class="btn btn-outline btn-sm" id="cancel-code-btn">Cancel</button>
              </div>
            </div>
          </div>

        </div>
        <div class="card pb-0">
          <div class="card-header">
            <div class="card-subtitle">Store Visibility</div>
          </div>
          <div class="card-content mt-5">
            <div class="flex flex-col items-center">
              <!-- <i class="fa-solid fa-globe fa-4x"></i>
              <div class="text-lg font-bold mt-1">Visible</div> -->

              <?php if ($data['visibility']) { ?>
                <i class="fa-solid fa-eye fa-4x"></i>
                <div class="text-lg font-bold mt-1">Visible</div>
              <?php } else { ?>
                <i class="fa-solid fa-eye-slash fa-4x"></i>
                <div class="text-lg font-bold mt-1">Hidden</div>
              <?php } ?>

              <form action="" method="post">
                <input type="text" name="action" value="toggle_visibility" hidden>
                <div class="mt-2">
                  <?php if ($data['visibility']) { ?>
                    <button class="btn btn-outline btn-block">Hide Store</button>
                  <?php } else { ?>
                    <?php if (!isset($stripe_account_id)) { ?>
                      <div class="alert alert-warning" style="margin-bottom: 1rem">
                        <div class="alert-content">
                          <div class="alert-description">
                            <a href="<?= ROOT ?>dashboard/earnings/stripeaccount">Connect your stripe to go live</a>
                          </div>
                        </div>
                      </div>
                    <?php } else { ?>
                      <button class="btn btn-primary btn-block">Go Live</button>
                    <?php } ?>
                  <?php } ?>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-4 gap-3 mt-10">
        <div class="card">
          <div class="card-header">
            <div class="card-subtitle">Total Views</div>
          </div>
          <div class="card-content">
            <div class="text-3xl font-bold"><?= number_format($views) ?></div>
            <div class="text-sm text-muted">All time</div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-subtitle">Avg Daily Views</div>
          </div>
          <div class="card-content">
            <div class="text-3xl font-bold"><?= $stats['avg_daily_views'] ? number_format(round($stats['avg_daily_views'])) : '0' ?></div>
            <div class="text-sm text-muted">Average</div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-subtitle">Peak Daily Views</div>
          </div>
          <div class="card-content">
            <div class="text-3xl font-bold"><?= $stats['max_daily_views'] ? number_format($stats['max_daily_views']) : '0' ?></div>
            <div class="text-sm text-muted">Maximum</div>
          </div>
        </div>
      </div>

      <!-- Views Over Time Chart -->
      <div class="card mt-10">
        <div class="card-header">
          <div class="card-subtitle">Store Views - Last 30 Days</div>
        </div>
        <div class="card-content">
          <?php if (!empty($viewsOverTime)) { ?>
            <div style="position: relative; height: 300px;">
              <canvas id="viewsChart"></canvas>
            </div>
          <?php } else { ?>
            <div class="text-center py-10">
              <i class="fa-solid fa-chart-line fa-3x text-muted" style="color: #ccc;"></i>
              <div class="text-muted mt-3">No view data available yet</div>
            </div>
          <?php } ?>
        </div>
    </main>
  </div>
  <div class="modal-overlay" id="share-modal">
    <div class="modal">
      <div class="modal-header">Share your store on social media</div>
      <div class="modal-body">
        <div class="share-buttons-container">
          <div class="flex gap-3 justify-center">
            <button class="share-btn share-btn-whatsapp" onclick="shareOnWhatsApp()">
              <i class="fa-brands fa-whatsapp"></i>
              <span>WhatsApp</span>
            </button>
            <button class="share-btn share-btn-facebook" onclick="shareOnFacebook()">
              <i class="fa-brands fa-facebook-f"></i>
              <span>Facebook</span>
            </button>
            <button class="share-btn share-btn-instagram" onclick="shareOnInstagram()">
              <i class="fa-brands fa-instagram"></i>
              <span>Instagram</span>
            </button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button
          class="btn btn-secondary btn-sm"
          onclick="closeModal('share-modal')">
          Close
        </button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="edit-code">
    <form method="POST" action="<?= ROOT ?>dashboard/storefront/changecode">
      <div class="modal">
        <div class="modal-header">Edit your store code</div>
        <div class="modal-body">
          <div class="card-content">
            <?php if (isset($error)): ?>
              <div class="alert alert-error mt-3" style="margin-bottom: 1rem">
                <div class="alert-content">
                  <div class="alert-description"><?= htmlspecialchars($error) ?></div>
                </div>
              </div>
            <?php endif; ?>

            <div class="w-full mt-3 flex items-center justify-center">
              <i class="fa-solid fa-6x fa-store text-muted p-3"></i>
            </div>

            <div class="mt-3" style="margin-bottom: 1rem;">

              <input
                value="<?= $data['code'] ?>"
                type="text"
                id="store_code"
                name="store_code"
                class="input input-lg"
                placeholder="mystorecode"
                pattern="[a-z0-9-]+"
                title="Only lowercase letters, numbers, and hyphens allowed"

                style="width: 100%;" />
              <small class="text-muted" style="display: block; margin-top: 0.5rem;">
                <i class="fa-solid fa-info"></i>
                Use only lowercase letters, numbers, and hyphens (no spaces)
              </small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary btn-sm" type="submit">Update</button>
          <button
            type="button"
            class="btn btn-secondary btn-sm"
            onclick="closeModal('edit-code')">
            Close
          </button>
        </div>
      </div>
    </form>
  </div>

  <script src="<?= ROOT ?>assets/js/components/toast.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('success')) {
        showToast(urlParams.get('success'));
      }
      if (urlParams.has('error')) {
        showToast(urlParams.get('error'), 'error');
      }
      // delete the parameters from the URL without reloading the page
      if (urlParams.has('success') || urlParams.has('error')) {
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
      }

      // Copy URL to clipboard
      const copyBtn = document.querySelector('#store-url-card button:first-of-type');
      const storeUrlInput = document.getElementById('store-url');

      copyBtn.addEventListener('click', () => {
        navigator.clipboard.writeText(storeUrlInput.value)
          .then(() => {
            showToast('Copied to clipboard!');
          })
          .catch(err => {
            showToast('Failed to copy', 'error');
          });
      });

      // Initialize Chart.js if data exists
      const chartCanvas = document.getElementById('viewsChart');
      if (chartCanvas) {
        const viewsData = <?php echo json_encode($viewsOverTime); ?>;

        const dates = viewsData.map(item => item.view_date);
        const counts = viewsData.map(item => parseInt(item.view_count));

        const ctx = chartCanvas.getContext('2d');
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: dates,
            datasets: [{
              label: 'Daily Views',
              data: counts,
              borderColor: '#3b82f6',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#3b82f6',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointRadius: 4,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: true,
                position: 'top',
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                }
              }
            }
          }
        });
      }
    })

    // Social Share Functions
    function getStoreShareUrl() {
      return document.getElementById('store-url').value;
    }

    function getShareMessage() {
      return 'Check out my store!';
    }

    function shareOnWhatsApp() {
      const url = getStoreShareUrl();
      const message = getShareMessage() + ' ' + url;
      const whatsappUrl = 'https://wa.me/?text=' + encodeURIComponent(message);
      window.open(whatsappUrl, '_blank');
    }

    function shareOnFacebook() {
      const url = getStoreShareUrl();
      const facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
      window.open(facebookUrl, '_blank', 'width=600,height=400');
    }

    function shareOnInstagram() {
      const url = getStoreShareUrl();
      // alert('Instagram doesn\'t allow direct URL sharing. Please copy the store URL and share it manually:\n\n' + url);
    }
  </script>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/custom/storefront/index.js"></script>
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
  <script src="<?= ROOT ?>assets/js/components/modal.js"></script>
</body>

</html>