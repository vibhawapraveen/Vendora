<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="<?=ROOT ?>assets/css/main.css" />
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: oklch(0.94 0.03 295);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 500px;
            padding: 2rem;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo {
            font-size: 2rem;
            font-weight: bold;
            color: white;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.875rem;
        }
        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }
        .forgot-password a {
            color: #6366f1;
            text-decoration: none;
            font-size: 0.875rem;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .alert {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">

        <!-- Login Card -->
        <div class="card" style="width: 500px;">
            <div class="card-header">
                <h2 class="card-subtitle">Register</h2>
            </div>

            <hr>
            
            <div class="card-content">
                <!-- Error Alert (show if registration failed) -->
                <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <div class="alert-description"><?= htmlspecialchars($error) ?></div>
                </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="" method="POST">

                <div class="form-group">
                        <label for="name" class="form-label">Your Name</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="input"
                            required
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        />
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="input"
                            required
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        />
                    </div>

                    <div class="form-group">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        <input 
                            type="tel" 
                            id="mobile" 
                            name="mobile" 
                            class="input"
                            required
                            value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>"
                        />
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="input"
                            required
                            minlength="6"
                        />
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Register
                    </button>
                    <hr class="mt-3 mb-2">
                    <div class="">
                        Already have an account?
                        <a href="<?=ROOT?>authcustomer?redirect_url=<?= htmlspecialchars($_GET['redirect_url'] ?? '') ?>">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include your JavaScript if needed -->
    <script src="<?=ROOT ?>assets/js/components/toast.js"></script>
    <script>
        // Show toast if there's a success message
        <?php if (isset($_SESSION['login_success'])): ?>
        showToast('<?= $_SESSION['login_success'] ?>', 'success');
        <?php unset($_SESSION['login_success']); endif; ?>
    </script>
</body>
</html>