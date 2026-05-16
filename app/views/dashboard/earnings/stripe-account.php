<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isConnected ? 'Stripe Account Details' : 'Connect Stripe' ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
    <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</head>

<body>
    <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>

    <div class="sidebar-backdrop"></div>

    <div class="layout">
        <?php require 'assets/components/sidebar.php' ?>
        <main class="content">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold">
                        <?= $isConnected ? 'Stripe Account Connected' : 'Connect Stripe' ?>
                    </h2>
                    <p class="text-muted text-sm">
                        <?= $isConnected ? 'Your Stripe account is active and ready to receive payments' : 'Connect your stripe account to receive payments from your customers' ?>
                    </p>
                </div>
            </div>
            <div class="mt-5">

                <?php if (!$isConnected): ?>
                    <!-- Not Connected Content -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div>
                            <div class="card" style="display: flex; flex-direction: column; height: 100%;">
                                <div class="card-description">Get started with instant payments</div>

                                <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                                    <div style="text-align: center; margin-bottom: 1.5rem;">
                                        <div style="font-size: 3rem; margin-bottom: 0.75rem;">🔒</div>
                                        <p style="font-size: 0.9rem; color: var(--muted-foreground);">Your account is secure with Stripe's industry-leading security standards</p>
                                    </div>

                                    <a href="<?= ROOT ?>dashboard/earnings/stripeaccount/connect" class="btn btn-primary btn-block" style="margin-bottom: 1rem; padding: 0.75rem 1.25rem; text-decoration: none;">
                                        <span style="margin-right: 0.5rem;">→</span> Connect Stripe Account
                                    </a>
                                    <p style="font-size: 0.75rem; color: var(--muted-foreground); text-align: center;">You'll be redirected to Stripe's secure site</p>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Connected Content -->
                    <!-- Account Information Cards -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">

                        <!-- Account Status -->
                        <div class="card">
                            <div class="card-title">Account Status</div>
                            <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                                <div style="padding: 1rem; background: var(--success-light); border-radius: 0.5rem; border-left: 4px solid var(--success);">
                                    <p style="font-size: 0.875rem; color: var(--success); font-weight: 600;">✓ Connected</p>
                                    <p style="font-size: 0.75rem; color: var(--success); margin-top: 0.25rem;">Ready to accept payments</p>
                                </div>
                                <?php if (isset($account['first_name'])) { ?>
                                    <div style="padding: 1rem; background: var(--info-light); border-radius: 0.5rem;">
                                        <p style="font-size: 0.875rem; color: var(--foreground); font-weight: 600;">Account Name</p>
                                        <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.25rem; word-break: break-all;">
                                            <?= htmlspecialchars($account['first_name'] . " " . $account['last_name'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                    <div style="padding: 1rem; background: var(--info-light); border-radius: 0.5rem;">
                                        <p style="font-size: 0.875rem; color: var(--foreground); font-weight: 600;">Account ID</p>
                                        <p style="font-size: 0.75rem; color: var(--muted-foreground); margin-top: 0.25rem; word-break: break-all;">
                                            <?= htmlspecialchars($account['id'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                <?php } ?>
                            </div>
                            <hr>
                            <div>
                                <form action="" method="post">
                                    <button class="btn btn-ghost btn-sm">Disconnect Account</button>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
    <script src="<?= ROOT ?>assets/js/components/modal.js"></script>
    <script src="<?= ROOT ?>assets/js/products/all.js"></script>
</body>

</html>