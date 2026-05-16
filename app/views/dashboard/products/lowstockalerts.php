<?php
$categoryOptions = [];
if (!empty($alerts)) {
    foreach ($alerts as $alert) {
        $categoryName = trim((string)($alert['category_name'] ?? ''));
        if ($categoryName === '') {
            $categoryName = 'Uncategorized';
        }
        $categoryOptions[strtolower($categoryName)] = $categoryName;
    }
    asort($categoryOptions, SORT_NATURAL | SORT_FLAG_CASE);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alerts</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/products/all.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>

        <main class="content">
            <!-- Low stock alert Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold">Low Stock Alerts</h2>
                    <p class="text-muted text-sm">Track products below threshold, prioritize urgent items, and trigger fast restocking.</p>
                </div>
                <div class="flex gap-2">
                    <!-- Export Button -->
                    <div class="btn btn-secondary" onclick="window.open('?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>', '_blank')" style="cursor: pointer;">
                        <i class="fa-solid fa-download pr-2"></i> Export Alert List
                    </div>
                </div>
            </div>

            <!-- Cards Section -->
            <div class="grid grid-cols-4 gap-3 mt-5">
                <!-- Total Products -->
                <div class="card flex justify-between items-center">
                    <div class="w-3/4">
                        <div class="text-sm text-muted">Total Alert Products</div>
                        <div class="card-content">
                            <div class="text-2xl font-bold"><?= number_format($stats['total_alerts'] ?? 0) ?></div>
                        </div>
                    </div>
                    <i class="fa-solid fa-box-archive text-3xl gray"></i>
                </div>

                <!-- Active Products -->
                <div class="card flex justify-between items-center">
                    <div class="w-3/4">
                        <div class="text-sm text-muted">Critical</div>
                        <div class="card-content">
                            <div class="text-2xl font-bold"><?= number_format($stats['critical_count'] ?? 0) ?></div>
                        </div>
                    </div>
                    <i class="fa-solid fa-circle-exclamation text-3xl red"></i>
                </div>

                <!-- Low Stock Alert -->
                <div class="card flex justify-between items-center">
                    <div class="w-3/4">
                        <div class="text-sm text-muted">Warning</div>
                        <div class="card-content">
                            <div class="text-2xl font-bold"><?= number_format($stats['warning_count'] ?? 0) ?></div>
                        </div>
                    </div>
                    <i class="fa-solid fa-circle-exclamation text-3xl yellow"></i>
                </div>

                <!-- Total Value -->
                <div class="card flex justify-between items-center">
                    <div class="w-3/4">
                        <div class="text-sm text-muted">Restock Soon</div>
                        <div class="card-content">
                            <div class="text-2xl font-bold"><?= number_format($stats['restock_soon_count'] ?? 0) ?></div>
                        </div>
                    </div>
                    <i class="fa-solid fa-circle-exclamation text-3xl blue"></i>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="card mt-5 gap-2 p-3">
                <div class="card-header mb-3">
                    <div class="card-subtitle">Filter Products</div>
                </div>
                <div class="flex flex-wrap gap-2 items-center">

                    <!-- Search Bar -->
                    <div class="card flex items-center px-2 py-1 searchBar searchBar--wide">
                        <i class="fa-solid fa-magnifying-glass mr-2 text-gray-500 text-sm"></i>
                        <input type="text" id="search-input" placeholder="Search products..." class="outline-none text-sm border-0 w-full">
                    </div>

                    <!-- Category Dropdown -->
                    <div class="card flex items-center px-2 py-1 searchBar">
                        <i class="fa-solid fa-tags mr-2 lightblue text-sm"></i>
                        <select id="category-filter" class="outline-none text-sm border-0 w-full">
                            <option value="">All Categories</option>
                            <?php foreach ($categoryOptions as $categoryValue => $categoryLabel): ?>
                                <option value="<?= htmlspecialchars($categoryValue) ?>"><?= htmlspecialchars($categoryLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Stock Status Dropdown -->
                    <div class="card flex items-center px-2 py-1 searchBar">
                        <i class="fa-solid fa-layer-group mr-2 gray"></i>
                        <select id="variant-filter" class="outline-none text-sm border-0 w-full">
                            <option value="">Variant</option>
                            <option value="multi">Multi</option>
                            <option value="single">Single</option>
                        </select>
                    </div>

                    <!-- Severity Status Dropdown -->
                    <div class="card flex items-center px-2 py-1 searchBar">
                        <i class="fa-solid fa-circle-exclamation mr-2 text-sm  "></i>
                        <select id="visibility-filter" class="outline-none text-sm border-0 w-full">
                            <option value="">Severity</option>
                            <option value="warning ">Warning</option>
                            <option value="restock_soon">Restock Soon</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                    <!-- Clear Filters Button -->
                    <button id="clear-filters-btn" class="btn btn-outline" style="display: none;">
                        <i class="fa-solid fa-xmark pr-2"></i>Clear Filters
                    </button>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mt-5 gap-3 p-0">
                <table class="table w-full text-sm mb-0 rounded-lg">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Variant</th>
                            <th class="px-6 py-3">Stock Status</th>
                            <th class="px-6 py-3">Threshold</th>
                            <th class="px-6 py-3">Severity</th>
                            <th class="px-6 py-3">Last Sold</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($alerts)): ?>
                            <?php foreach ($alerts as $alert): ?>
                                <?php
                                $severity = 'Restock Soon';
                                $severityClass = 'badge-single';
                                if ((int)$alert['threshold'] > 0) {
                                    $ratio = (int)$alert['current_stock'] / (int)$alert['threshold'];
                                    if ($ratio <= 0.30) {
                                        $severity = 'Critical';
                                        $severityClass = 'badge-destructive';
                                    } elseif ($ratio <= 0.70) {
                                        $severity = 'Warning';
                                        $severityClass = 'badge-secondary';
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <?php if (!empty($alert['first_image'])): ?>
                                            <img src="<?= ROOT . $alert['first_image'] ?>" alt="<?= htmlspecialchars($alert['product_name']) ?>" class="w-10 h-10 rounded-lg object-cover">
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <i class="fa-solid fa-image text-gray-400 text-sm"></i>
                                            </div>
                                        <?php endif; ?>
                                        <a href="<?= ROOT ?>dashboard/products/<?= $alert['product_id'] ?>/edit" class="font-medium hover:opacity-70 transition-all duration-200" style="text-decoration: none; color: #000;">
                                            <?= htmlspecialchars($alert['product_name']) ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= !empty($alert['category_name']) ? htmlspecialchars($alert['category_name']) : 'Uncategorized' ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if (!empty($alert['variant_id'])): ?>
                                            <span class="badge badge-multi">
                                                <i class="fa-solid fa-layer-group"></i> Multi
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-single">
                                                <i class="fa-solid fa-box"></i> Single
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= number_format($alert['current_stock']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= number_format($alert['threshold']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($severity === 'Critical'): ?>
                                            <span class="badge badge-destructive">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Critical
                                            </span>
                                        <?php elseif ($severity === 'Warning'): ?>
                                            <span class="badge" style="background-color: #fef3c7; color: #92400e; border-color: #fde68a;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Warning
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-multi">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Restock Soon
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= date('M d, Y', strtotime($alert['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="<?= ROOT ?>dashboard/products/<?= $alert['product_id'] ?>/edit" class="btn btn-secondary" style="text-decoration: none;">
                                            <i class="fa-solid fa-boxes-stacked pr-2"></i> Restock
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No open stock alerts found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Table Footer -->
                <div class="tableFooter">
                    <div class="text-sm text-gray-500">
                        <?php
                        $start = $totalAlerts > 0 ? (($currentPage - 1) * $limit + 1) : 0;
                        $end = min($currentPage * $limit, $totalAlerts);
                        ?>
                        Showing <?= $start ?> to <?= $end ?> of <?= $totalAlerts ?> results
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?>" class="pageBtn" style="text-decoration: none;">Previous</a>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);

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

                        <?php if ($endPage < $totalPages): ?>
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

        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/products/lowstock.js"></script>
</body>

</html>