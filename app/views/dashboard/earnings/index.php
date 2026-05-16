<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earnings</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
  <script src="<?= ROOT ?>assets/chartjs/chart.umd.min.js"></script>

</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>
        <main class="content">
            
            <!-- Earnings Dashboard Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold">Earnings</h2>
                    <p class="text-muted text-sm">Track your all-time and current month earnings</p>
                </div>
                <div class="flex gap-2">
                    <!-- Export Button -->
                    <div class="btn btn-secondary">
                        <i class="fa-solid fa-download pr-2"></i> Export Earnings
                    </div>
                </div>
            </div>

            <!-- All-Time vs Current Month Toggle -->
            <div class="mt-5 flex gap-3">
                <!-- All-Time Stats -->
                <div class="flex-1">
                    <div class="mb-3">
                        <div class="text-sm text-muted" style="font-weight: 600;">All-Time Earnings</div>
                    </div>
                    <div class="grid grid-cols-4 gap-3">
                        <!-- Total Earnings -->
                        <div class="card flex justify-between items-center">
                            <div class="w-3/4">
                                <div class="text-sm text-muted">Total Earned</div>
                                <div class="card-content">
                                    <div class="text-2xl font-bold">$ <?= number_format($allTimeData['total_earned'] ?? 0, 2) ?></div>
                                </div>
                                <div class="text-xs text-muted" style="margin-top: 0.5rem;">From <?= $allTimeData['total_orders'] ?? 0 ?> orders</div>
                            </div>
                            <i class="fa-solid fa-wallet text-3xl" style="color: #10b981;"></i>
                        </div>

                        <!-- Paid Amount -->
                        <div class="card flex justify-between items-center">
                            <div class="w-3/4">
                                <div class="text-sm text-muted">Platform Fees</div>
                                <div class="card-content">
                                    <div class="text-2xl font-bold">$ <?= number_format($allTimeData['total_platform_fee'] ?? 0, 2) ?></div>
                                </div>
                                <div class="text-xs text-muted" style="margin-top: 0.5rem;">Total commission</div>
                            </div>
                            <i class="fa-solid fa-hashtag text-3xl" style="color: #06b6d4;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Month Stats -->
            <div class="mt-5 flex gap-3">
                <div class="flex-1">
                    <div class="mb-3">
                        <div class="text-sm text-muted" style="font-weight: 600;"><?= date('F Y') ?> Earnings</div>
                    </div>
                    <div class="grid grid-cols-4 gap-3">
                        <!-- Current Month Total -->
                        <div class="card flex justify-between items-center">
                            <div class="w-3/4">
                                <div class="text-sm text-muted">Month Total</div>
                                <div class="card-content">
                                    <div class="text-2xl font-bold">$ <?= number_format($monthData['month_total'] ?? 0, 2) ?></div>
                                </div>
                                <div class="text-xs text-muted" style="margin-top: 0.5rem;"><?= $monthData['month_orders'] ?? 0 ?> orders</div>
                            </div>
                            <i class="fa-solid fa-calendar-days text-3xl" style="color: #8b5cf6;"></i>
                        </div>

                        <!-- Current Month Paid -->
                        <div class="card flex justify-between items-center">
                            <div class="w-3/4">
                                <div class="text-sm text-muted">Month Paid</div>
                                <div class="card-content">
                                    <div class="text-2xl font-bold">$ <?= number_format($monthData['month_total'] ?? 0, 2) ?></div>
                                </div>
                            </div>
                            <i class="fa-solid fa-check-circle text-3xl" style="color: #10b981;"></i>
                        </div>

                        <!-- Current Month Pending -->
                        <div class="card flex justify-between items-center">
                            <div class="w-3/4">
                                <div class="text-sm text-muted">Pending This Month</div>
                                <div class="card-content">
                                    <div class="text-2xl font-bold">$ <?= number_format($pendingData['pending_total'] ?? 0, 2) ?></div>
                                </div>
                                <div class="text-xs text-muted" style="margin-top: 0.5rem;"><?= $pendingData['pending_orders'] ?? 0 ?> orders</div>
                            </div>
                            <i class="fa-solid fa-clock text-3xl" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="mt-5 gap-2">
                <div class="card-header mb-3">
                    <div class="card-subtitle">Filter Transactions</div>
                </div>
                <div class="flex flex-wrap gap-2 items-center">

                    <!-- Date Range Search -->
                    <div class="card flex items-center px-2 py-1 searchBar">
                        <i class="fa-solid fa-calendar mr-2 text-gray-500 text-sm"></i>
                        <input type="date" id="date-from" placeholder="From date" class="outline-none text-sm border-0 w-full" value="<?= !empty($filters['from_date']) ? htmlspecialchars($filters['from_date']) : '' ?>">
                    </div>

                    <div class="card flex items-center px-2 py-1 searchBar">
                        <i class="fa-solid fa-calendar mr-2 text-gray-500 text-sm"></i>
                        <input type="date" id="date-to" placeholder="To date" class="outline-none text-sm border-0 w-full" value="<?= !empty($filters['to_date']) ? htmlspecialchars($filters['to_date']) : '' ?>">
                    </div>

                    <!-- Status Dropdown -->
                    <div class="card flex items-center px-2 py-1 searchBar">
                        <i class="fa-solid fa-filter mr-2 lightblue text-sm"></i>
                        <select id="status-filter" name="status" class="outline-none text-sm border-0 w-full">
                            <option value="" <?= empty($filters['status']) ? 'selected' : '' ?>>All Status</option>
                            <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="failed" <?= $filters['status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="refunded" <?= $filters['status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                        </select>
                    </div>

                    <!-- Payment Method Dropdown -->
                    <div class="card flex items-center px-2 py-1 searchBar">
                        <i class="fa-solid fa-credit-card mr-2 gray text-sm"></i>
                        <select id="method-filter" name="method" class="outline-none text-sm border-0 w-full">
                            <option value="" <?= empty($filters['method']) ? 'selected' : '' ?>>All Methods</option>
                            <option value="stripe" <?= $filters['method'] === 'stripe' ? 'selected' : '' ?>>Stripe</option>
                            <option value="cash" <?= $filters['method'] === 'cash' ? 'selected' : '' ?>>Cash</option>
                        </select>
                    </div>

                    <!-- Apply Filters Button -->
                    <button id="apply-filters-btn" class="btn btn-primary">
                        <i class="fa-solid fa-search pr-2"></i>Filter
                    </button>

                    <!-- Clear Filters Button -->
                    <button id="clear-filters-btn" class="btn btn-outline" style="display: <?php $hasFilters = !empty($filters['status']) || !empty($filters['method']) || !empty($filters['from_date']) || !empty($filters['to_date']); echo $hasFilters ? 'inline-block' : 'none'; ?>">
                        <i class="fa-solid fa-xmark pr-2"></i>Clear
                    </button>

                </div>
            </div>

            <!-- Earnings Table -->
            <div class="card mt-5 gap-3 p-0">
                <table class="table w-full text-sm mb-0 rounded-lg">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-3">Payment ID</th>
                            <th class="px-6 py-3">Order</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Platform Fee</th>
                            <th class="px-6 py-3">Your Earning</th>
                            <th class="px-6 py-3">Method</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                            <!-- <th class="px-6 py-3">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-muted">
                                    <i class="fa-solid fa-inbox text-3xl" style="opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                                    No transactions found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="px-6 py-4 font-medium"><?= htmlspecialchars($payment['payment_number']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($payment['order_id']) ?></td>
                                <td class="px-6 py-4">$ <?= number_format($payment['amount'], 2) ?></td>
                                <td class="px-6 py-4">$ <?= number_format($payment['platform_fee'], 2) ?></td>
                                <td class="px-6 py-4">
                                    <span style="color: #10b981; font-weight: 600;">$ <?= number_format($payment['vendor_amount'], 2) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($payment['payment_method'] === 'stripe'): ?>
                                        <span class="badge badge-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                            <i class="fa-brands fa-stripe"></i> Stripe
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                            <i class="fa-solid fa-money-bill"></i> Cash
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $statusClasses = [
                                        'paid' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'failed' => 'badge-danger',
                                        'refunded' => 'badge-secondary'
                                    ];
                                    $statusClass = $statusClasses[$payment['status']] ?? 'badge-secondary';
                                    $statusIcons = [
                                        'paid' => 'fa-check-circle',
                                        'pending' => 'fa-hourglass-end',
                                        'failed' => 'fa-circle-xmark',
                                        'refunded' => 'fa-arrow-rotate-left'
                                    ];
                                    $icon = $statusIcons[$payment['status']] ?? 'fa-circle';
                                    ?>
                                    <span class="badge <?= $statusClass ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; text-transform: capitalize;">
                                        <i class="fa-solid <?= $icon ?>"></i> <?= ucfirst($payment['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4"><?= date('M d, Y', strtotime($payment['created_at'])) ?></td>
                                <!-- <td class="px-6 py-4">
                                    <button onclick="viewPaymentDetails('<?= htmlspecialchars($payment['id']) ?>')" class="action-btn view-btn" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button onclick="downloadReceipt('<?= htmlspecialchars($payment['id']) ?>')" class="action-btn edit-btn" title="Download Receipt">
                                        <i class="fa-solid fa-receipt"></i>
                                    </button>
                                </td> -->
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Table Footer with Pagination -->
                <div class="tableFooter">
                    <div class="text-sm text-gray-500">
                        <?php 
                        $from = (($page - 1) * $limit) + 1;
                        $to = min($page * $limit, $totalTransactions);
                        ?>
                        Showing <?= $from ?> to <?= $to ?> of <?= $totalTransactions ?> transactions
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?><?= !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?><?= !empty($filters['method']) ? '&method=' . urlencode($filters['method']) : '' ?>" class="pageBtn" style="text-decoration: none;">← Previous</a>
                        <?php else: ?>
                            <span class="pageBtn" style="opacity: 0.5;">← Previous</span>
                        <?php endif; ?>
                        
                        <?php 
                        // Show page numbers
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        if ($startPage > 1): ?>
                            <a href="?page=1" class="pageBtn" style="text-decoration: none;">1</a>
                            <?php if ($startPage > 2): ?>
                                <span class="pageBtn">...</span>
                            <?php endif;
                        endif;
                        
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="currentPage"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?><?= !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?><?= !empty($filters['method']) ? '&method=' . urlencode($filters['method']) : '' ?>" class="pageBtn" style="text-decoration: none;"><?= $i ?></a>
                            <?php endif;
                        endfor;
                        
                        if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <span class="pageBtn">...</span>
                            <?php endif; ?>
                            <a href="?page=<?= $totalPages ?><?= !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?><?= !empty($filters['method']) ? '&method=' . urlencode($filters['method']) : '' ?>" class="pageBtn" style="text-decoration: none;"><?= $totalPages ?></a>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?><?= !empty($filters['status']) ? '&status=' . urlencode($filters['status']) : '' ?><?= !empty($filters['method']) ? '&method=' . urlencode($filters['method']) : '' ?>" class="pageBtn" style="text-decoration: none;">Next →</a>
                        <?php else: ?>
                            <span class="pageBtn" style="opacity: 0.5;">Next →</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal-overlay" id="payment-details-modal">
        <div class="modal" style="max-width: 600px;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Payment Details</h3>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: #6b7280;">Transaction information</p>
                </div>
                <button type="button" onclick="closeModal('payment-details-modal')" class="modal-close-btn" aria-label="Close modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <!-- Left Column -->
                    <div>
                        <div style="margin-bottom: 1.5rem;">
                            <div class="text-sm text-muted" style="margin-bottom: 0.25rem;">Payment Number</div>
                            <div style="font-weight: 600; font-size: 1rem;" id="detail-payment-number">PAY-2026-001</div>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <div class="text-sm text-muted" style="margin-bottom: 0.25rem;">Order ID</div>
                            <div style="font-weight: 600; font-size: 1rem;" id="detail-order-id">ORD-0001</div>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <div class="text-sm text-muted" style="margin-bottom: 0.25rem;">Payment Method</div>
                            <div style="font-weight: 600; font-size: 1rem;" id="detail-method">Stripe</div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <div style="margin-bottom: 1.5rem;">
                            <div class="text-sm text-muted" style="margin-bottom: 0.25rem;">Status</div>
                            <div id="detail-status"></div>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <div class="text-sm text-muted" style="margin-bottom: 0.25rem;">Date</div>
                            <div style="font-weight: 600; font-size: 1rem;" id="detail-date">Apr 10, 2026</div>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <div class="text-sm text-muted" style="margin-bottom: 0.25rem;">Customer</div>
                            <div style="font-weight: 600; font-size: 1rem;" id="detail-customer">John Doe</div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Amount Breakdown -->
                <div style="margin-top: 1.5rem;">
                    <div style="font-weight: 600; font-size: 1rem; margin-bottom: 1rem;">Amount Breakdown</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span class="text-muted">Order Total</span>
                        <span style="font-weight: 600;" id="detail-total">$ 5,500.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span class="text-muted">Platform Fee (2.9%)</span>
                        <span style="color: #ef4444; font-weight: 600;" id="detail-fee">- $ 160.00</span>
                    </div>
                    <hr>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 600;">Your Earning</span>
                        <span style="font-size: 1.25rem; font-weight: 700; color: #10b981;" id="detail-earning">$ 5,340.00</span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('payment-details-modal')">
                    <i class="fa-solid fa-xmark" style="margin-right: 0.5rem;"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="downloadReceipt()">
                    <i class="fa-solid fa-download" style="margin-right: 0.5rem;"></i> Download Receipt
                </button>
            </div>
        </div>
    </div>

    <script>
        // Sample action functions
        function viewPaymentDetails(paymentId) {
            // In a real application, this would fetch actual data
            openModal('payment-details-modal');
        }

        function downloadReceipt(paymentId) {
            console.log('Downloading receipt for:', paymentId);
            // Add download functionality here
        }

        // Build URL with filters
        function buildFilterURL() {
            const status = document.getElementById('status-filter').value;
            const method = document.getElementById('method-filter').value;
            const fromDate = document.getElementById('date-from').value;
            const toDate = document.getElementById('date-to').value;
            
            let url = '?page=1'; // Reset to page 1 when applying filters
            
            if (status) url += '&status=' + encodeURIComponent(status);
            if (method) url += '&method=' + encodeURIComponent(method);
            if (fromDate) url += '&from_date=' + encodeURIComponent(fromDate);
            if (toDate) url += '&to_date=' + encodeURIComponent(toDate);
            
            return url;
        }

        // Update clear button visibility
        function updateClearButtonVisibility() {
            const status = document.getElementById('status-filter').value;
            const method = document.getElementById('method-filter').value;
            const fromDate = document.getElementById('date-from').value;
            const toDate = document.getElementById('date-to').value;
            
            const hasFilters = status || method || fromDate || toDate;
            document.getElementById('clear-filters-btn').style.display = hasFilters ? 'inline-block' : 'none';
        }

        // Apply filters button click
        document.getElementById('apply-filters-btn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = buildFilterURL();
        });

        // Clear filters button click
        document.getElementById('clear-filters-btn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('status-filter').value = '';
            document.getElementById('method-filter').value = '';
            document.getElementById('date-from').value = '';
            document.getElementById('date-to').value = '';
            window.location.href = '?page=1';
        });

        // Listen for filter changes to show/hide clear button
        document.getElementById('status-filter').addEventListener('change', updateClearButtonVisibility);
        document.getElementById('method-filter').addEventListener('change', updateClearButtonVisibility);
        document.getElementById('date-from').addEventListener('change', updateClearButtonVisibility);
        document.getElementById('date-to').addEventListener('change', updateClearButtonVisibility);

        // Initial check for clear button visibility
        updateClearButtonVisibility();
    </script>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/components/modal.js"></script>
</body>

</html>