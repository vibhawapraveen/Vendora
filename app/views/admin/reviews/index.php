<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback & Reviews - Vendora Admin</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/sellers.css">
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/pages/admin/reviews.css">
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/admin-sidebar.php' ?>

    <main class="content">
      <!-- Header Section -->
      <div class="page-header">
        <div class="page-header-left">
          <h2 class="font-semibold">Feedback & Reviews</h2>
          <p class="text-muted">Manage customer reviews and feedback across all stores.</p>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <!-- Search and Filter Header -->
        <div class="table-header">
          <div class="search-filter-row">
            <div class="search-input-wrapper">
              <input type="text" class="input" placeholder="Search reviews...">
            </div>
            <button class="btn-filter">
              <i class="fas fa-filter"></i>
              Filter
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Store</th>
                <th>Reviewer</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Tech Haven</td>
                <td>Alice Johnson</td>
                <td>
                  <span class="review-comment">Excellent product quality and fast shipping. Highly recommended!</span>
                </td>
                <td>Mar 15, 2024</td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn flag" title="Flag Review">
                      <i class="far fa-flag"></i>
                    </button>
                    <button class="action-btn delete" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Fashion Galaxy</td>
                <td>Bob Smith</td>
                <td>
                  <span class="review-comment">Good quality clothes, but delivery took longer than expected.</span>
                </td>
                <td>Mar 14, 2024</td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn flag" title="Flag Review">
                      <i class="far fa-flag"></i>
                    </button>
                    <button class="action-btn delete" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr class="flagged-row">
                <td>Home Essentials</td>
                <td>Carol White</td>
                <td>
                  <span class="review-comment">Product description was misleading. Not what I expected.</span>
                </td>
                <td>Mar 14, 2024</td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn flag flagged" title="Unflag Review">
                      <i class="fas fa-flag"></i>
                    </button>
                    <button class="action-btn delete" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Sports Zone</td>
                <td>David Brown</td>
                <td>
                  <span class="review-comment">Perfect for my needs. Great customer service too!</span>
                </td>
                <td>Mar 13, 2024</td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn flag" title="Flag Review">
                      <i class="far fa-flag"></i>
                    </button>
                    <button class="action-btn delete" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Book Corner</td>
                <td>Emma Davis</td>
                <td>
                  <span class="review-comment">Great selection of books. Prices are reasonable.</span>
                </td>
                <td>Mar 13, 2024</td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn flag" title="Flag Review">
                      <i class="far fa-flag"></i>
                    </button>
                    <button class="action-btn delete" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Beauty Bliss</td>
                <td>Fiona Green</td>
                <td>
                  <span class="review-comment">Products are okay, but packaging could be better.</span>
                </td>
                <td>Mar 12, 2024</td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn flag" title="Flag Review">
                      <i class="far fa-flag"></i>
                    </button>
                    <button class="action-btn delete" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr class="flagged-row">
                <td>Gadget Galaxy</td>
                <td>George Harris</td>
                <td>
                  <span class="review-comment">Terrible experience. Product stopped working after one week.</span>
                </td>
                <td>Mar 12, 2024</td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn flag flagged" title="Unflag Review">
                      <i class="fas fa-flag"></i>
                    </button>
                    <button class="action-btn delete" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Pet Paradise</td>
                <td>Hannah Lee</td>
                <td>
                  <span class="review-comment">My pets love these products! Will definitely order again.</span>
                </td>
                <td>Mar 11, 2024</td>
                <td>
                  <div class="action-buttons">
                    <button class="action-btn flag" title="Flag Review">
                      <i class="far fa-flag"></i>
                    </button>
                    <button class="action-btn delete" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Empty State (hidden by default) -->
          <div class="empty-state" style="display: none;">
            <i class="fas fa-inbox"></i>
            <p>No reviews found</p>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>

</html>