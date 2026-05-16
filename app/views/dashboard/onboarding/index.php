<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Setup - Onboarding</title>
    <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
</head>

<style>
    body {
        background-color: #f9f9f9;
    }
</style>

<body>
    <div class="">
        <div class="card m-auto mt-12" style="max-width: 500px;">
            <div class="card-header">
                <div class="card-title">
                    Choose Your Store Code
                </div>
                <p class="text-muted">
                    Pick a unique code for your store. This will be used in your store URL.<br>
                </p>
            </div>

            <form method="POST" action="<?= ROOT ?>dashboard/onboarding">
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
                            type="text"
                            id="store_code"
                            name="store_code"
                            class="input input-lg"
                            placeholder="mystorecode"
                            pattern="[a-z0-9-]+"
                            title="Only lowercase letters, numbers, and hyphens allowed"
                            required
                            style="width: 100%;" />
                        <small class="text-muted" style="display: block; margin-top: 0.5rem;">
                            <i class="fa-solid fa-info"></i>
                            Use only lowercase letters, numbers, and hyphens (no spaces)
                        </small>
                    </div>
                </div>

                <div class="card-footer" style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Continue
                        <i class="fa-solid ml-2 fa-angle-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($_GET['welcome']) && $_GET['welcome'] == '1'): ?>
                showToast("Registration successful! Please choose your store code to continue.");
            <?php endif; ?>

        });
    </script>
  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
    <script src="<?= ROOT ?>assets/js/components/toast.js"></script>
    <script src="<?= ROOT ?>assets/js/lucide.js"></script>
</body>

</html>