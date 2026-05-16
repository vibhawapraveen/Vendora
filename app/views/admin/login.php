<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Vendora</title>
    <link rel="stylesheet" href="<?=ROOT ?>assets/css/main.css" />
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
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
        <!-- Login Header -->
        <div class="login-header">
            <div class="login-logo">Vendora</div>
            <div class="login-subtitle">Admin Portal</div>
        </div>

        <!-- Login Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-subtitle">Sign in to your account</h2>
            </div>

            <hr>
            
            <div class="card-content">
                <!-- Error Alert (show if login failed) -->
                <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-error">
                    <div class="alert-icon">❌</div>
                    <div class="alert-content">
                        <div class="alert-title">Login Failed</div>
                        <div class="alert-description"><?= $_SESSION['login_error'] ?></div>
                    </div>
                </div>
                <?php unset($_SESSION['login_error']); endif; ?>

                <!-- Login Form -->
                <form action="<?=ROOT ?>admin/authenticate" method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="input"
                            required
                            value="<?= $_POST['email'] ?? '' ?>"
                        />
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="input"
                            required
                        />
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Sign In
                    </button>
                </form>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem; color: rgba(255, 255, 255, 0.7); font-size: 0.875rem;">
            © <?= date('Y') ?> Vendora. All rights reserved.
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