<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/products_index.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/all.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>

        <main class="content">
            <?php
            // Calculate statistics from the database categories
            $activeCategories = count(array_filter($categoryList, fn($c) => ($c['status'] ?? 'active') === 'active'));
            $inactiveCategories = count($categoryList) - $activeCategories;
            $totalCategories = count($categoryList);
            $totalProducts = array_sum(array_map(fn($c) => $c['products'] ?? 0, $categoryList));
            ?>

            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold">Manage Categories</h2>
                    <p class="text-muted text-sm">Organize your catalog with clean category groups and visibility controls.</p>
                </div>
            </div>

            <?php if (!empty($_GET['success']) && $_GET['success'] === '1'): ?>
                <div class="alert alert-success mt-3" style="margin-bottom: 1rem;">
                    <div class="alert-icon">✅</div>
                    <div class="alert-content">
                        <div class="alert-title">Category Created</div>
                        <div class="alert-description">Category created successfully.</div>
                    </div>
                </div>
            <?php elseif (!empty($_GET['success']) && $_GET['success'] === 'deleted'): ?>
                <div class="alert alert-success mt-3" style="margin-bottom: 1rem;">
                    <div class="alert-icon">✅</div>
                    <div class="alert-content">
                        <div class="alert-title">Category Deleted</div>
                        <div class="alert-description">Category deleted. Related products will be deleted.</div>
                    </div>
                </div>
            <?php elseif (!empty($_GET['success']) && $_GET['success'] === 'status_updated'): ?>
                <div class="alert alert-success mt-3" style="margin-bottom: 1rem;">
                    <div class="alert-icon">✅</div>
                    <div class="alert-content">
                        <div class="alert-title">Category Updated</div>
                        <div class="alert-description">Category status was updated successfully.</div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate'): ?>
                <div class="alert alert-error mt-3" style="margin-bottom: 1rem;">
                    <div class="alert-icon">❌</div>
                    <div class="alert-content">
                        <div class="alert-title">Duplicate Category</div>
                        <div class="alert-description">Category already exists. Please use a different name.</div>
                    </div>
                </div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'empty'): ?>
                <div class="alert alert-error mt-3" style="margin-bottom: 1rem;">
                    <div class="alert-icon">❌</div>
                    <div class="alert-content">
                        <div class="alert-title">Missing Category Name</div>
                        <div class="alert-description">Category name is required.</div>
                    </div>
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-error mt-3" style="margin-bottom: 1rem;">
                    <div class="alert-icon">❌</div>
                    <div class="alert-content">
                        <div class="alert-title">Error</div>
                        <div class="alert-description">Unable to save category. Please try again.</div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-4 gap-3 mt-5">
                <div class="card flex justify-between items-center">
                    <div class="w-3/4">
                        <div class="text-sm text-muted">Total Categories</div>
                        <div class="card-content">
                            <div class="text-2xl font-bold"><?= number_format($totalCategories) ?></div>
                        </div>
                    </div>
                    <i class="fa-solid fa-folder-tree text-3xl gray"></i>
                </div>

                <div class="card flex justify-between items-center">
                    <div class="w-3/4">
                        <div class="text-sm text-muted">Active</div>
                        <div class="card-content">
                            <div class="text-2xl font-bold"><?= number_format($activeCategories) ?></div>
                        </div>
                    </div>
                    <i class="fa-solid fa-circle-check text-3xl green"></i>
                </div>

                <div class="card flex justify-between items-center">
                    <div class="w-3/4">
                        <div class="text-sm text-muted">Inactive</div>
                        <div class="card-content">
                            <div class="text-2xl font-bold"><?= number_format($inactiveCategories) ?></div>
                        </div>
                    </div>
                    <i class="fa-solid fa-circle-pause text-3xl yellow"></i>
                </div>

                <div class="card flex justify-between items-center">
                    <div class="w-3/4">
                        <div class="text-sm text-muted">Products Mapped</div>
                        <div class="card-content">
                            <div class="text-2xl font-bold"><?= number_format($totalProducts) ?></div>
                        </div>
                    </div>
                    <i class="fa-solid fa-boxes-stacked text-3xl blue"></i>
                </div>
            </div>

            <div class="card gap-3 mt-5">
                <div class="card-header mb-3">
                    <div class="card-subtitle">Add New Category</div>
                </div>

                <form method="POST" action="<?= ROOT ?>dashboard/products/managecategories">
                    <div class="mb-5">
                        <p class="mb-1">Category Name</p>
                        <input type="text" name="category_name" class="input" placeholder="Enter category name" required />
                    </div>

                    <div class="flex justify-end items-center mt-5">
                        <button type="button" class="btn btn-secondary mr-3">
                            <p class="px-2">Cancel</p>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <p class="px-2">Save Category</p>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Filters Section -->
            <div class="card mt-5 gap-2 p-3">
                <div class="card-header mb-3">
                    <div class="card-subtitle">Filter Categories</div>
                </div>
                <div class="flex flex-wrap gap-2 items-center">

                    <!-- Search Bar -->
                    <div class="card flex items-center px-2 py-1 searchBar searchBar--wide">
                        <i class="fa-solid fa-magnifying-glass mr-2 text-gray-500 text-sm"></i>
                        <input type="text" id="search-input" placeholder="Search categories..." class="outline-none text-sm border-0 w-full">
                    </div>

                    <!-- Visibility Status Dropdown -->
                    <div class="card flex items-center px-2 py-1 searchBar">
                        <i class="fa-solid fa-eye mr-2 blue text-sm"></i>
                        <select id="visibility-filter" class="outline-none text-sm border-0 w-full">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Clear Filters Button -->
                    <button id="clear-filters-btn" class="btn btn-outline" style="display: none;">
                        <i class="fa-solid fa-xmark pr-2"></i>Clear Filters
                    </button>
                </div>
            </div>

            <div class="card mt-5 gap-3 p-0">
                <div class="table-wrap">
                    <table class="table w-full text-sm mb-0 rounded-lg">
                        <thead>
                            <tr class="text-left">
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3">Products</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Last Updated</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="category-table-body">
                            <?php foreach ($categoryList as $category): ?>
                                <tr data-name="<?= strtolower($category['name']) ?>" data-status="<?= htmlspecialchars($category['status']) ?>">
                                    <td class="px-6 py-4">
                                        <div class="category-name">
                                            <div>
                                                <div class="font-medium"><?= htmlspecialchars($category['name']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><?= number_format($category['products']) ?></td>
                                    <td class="px-6 py-4">
                                        <?php if (($category['status'] ?? 'active') === 'inactive'): ?>
                                            <span class="badge badge-destructive">Inactive</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4"><?= date('M d, Y', strtotime($category['created_at'])) ?></td>
                                    <td class="px-6 py-4">
                                        <button
                                            class="action-btn edit-btn mr-3"
                                            type="button"
                                            title="Edit Category"
                                            onclick="openEditCategoryModal('<?= htmlspecialchars($category['id']) ?>', '<?= htmlspecialchars($category['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($category['status'] ?? 'active') ?>')">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button
                                            class="action-btn delete-btn"
                                            type="button"
                                            title="Delete Category"
                                            onclick="openDeleteCategoryModal('<?= htmlspecialchars($category['id']) ?>', '<?= htmlspecialchars($category['name'], ENT_QUOTES) ?>')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr id="category-empty-row" style="display:none;">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No categories match your filters.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="tableFooter">
                    <div class="text-sm text-gray-500">
                        <?php
                        $start = ($currentPage - 1) * $limit + 1;
                        $end = min($currentPage * $limit, $totalCategories);
                        ?>
                        Showing <?= $start ?> to <?= $end ?> of <?= $totalCategories ?> results
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?>" class="pageBtn" style="text-decoration: none;">Previous</a>
                        <?php endif; ?>

                        <?php
                        // Calculate which page numbers to show
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);

                        // Show first page if we're not starting from 1
                        if ($startPage > 1): ?>
                            <a href="?page=1" class="pageBtn" style="text-decoration: none;">1</a>
                            <?php if ($startPage > 2): ?>
                                <span class="pageBtn">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <?php if ($i == $currentPage): ?>
                                <span class="currentPage"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?>" class="pageBtn" style="text-decoration: none;"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php
                        // Show last page if we're not ending at the last page
                        if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <span class="pageBtn">...</span>
                            <?php endif; ?>
                            <a href="?page=<?= $totalPages ?>" class="pageBtn" style="text-decoration: none;"><?= $totalPages ?></a>
                        <?php endif; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 ?>" class="pageBtn" style="text-decoration: none;">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>


            <div class="modal-overlay" id="edit-category-modal">
                <div class="modal">
                    <div class="modal-header">Edit Category</div>
                    <div class="modal-body">
                        <form method="POST" action="<?= ROOT ?>dashboard/products/managecategories" id="edit-category-form">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="category_id" id="edit-category-id">

                            <div class="mb-3">
                                <label class="mb-1" for="edit-category-name" style="display:block;">Category Name</label>
                                <input type="text" id="edit-category-name" class="input" readonly>
                            </div>

                            <div>
                                <label class="mb-1" for="edit-category-status" style="display:block;">Status</label>
                                <select name="category_status" id="edit-category-status" class="input" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <p class="text-sm text-muted mt-2">If set to inactive, all products in this category will be marked inactive.</p>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" onclick="closeModal('edit-category-modal')">Cancel</button>
                        <button class="btn btn-primary btn-sm" type="submit" form="edit-category-form">Save Changes</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="delete-category-modal">
                <div class="modal">
                    <div class="modal-header">Delete Category</div>
                    <div class="modal-body">
                        <form method="POST" action="<?= ROOT ?>dashboard/products/managecategories" id="delete-category-form">
                            <input type="hidden" name="action" value="delete_category">
                            <input type="hidden" name="category_id" id="delete-category-id">
                            <p>Are you sure you want to delete <strong id="delete-category-name"></strong>?</p>
                            <p class="text-sm text-muted mt-2">All products in this category will be soft deleted and will not appear in product lists.</p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" onclick="closeModal('delete-category-modal')">Cancel</button>
                        <button class="btn btn-destructive btn-sm" type="submit" form="delete-category-form">Delete</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/components/modal.js"></script>
    <script src="<?= ROOT ?>assets/js/products/managecategories.js"></script>

</body>

</html>