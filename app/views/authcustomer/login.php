<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-method-toggle {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
        }
        .toggle-button {
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            color: #6b7280;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .toggle-button.active {
            color: #6366f1;
            border-bottom-color: #6366f1;
        }
        .toggle-button:hover {
            color: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="login-container">

        <!-- Login Card -->
        <div class="card" style="width: 500px;">
            <div class="card-header">
                <h2 class="card-subtitle">Sign in</h2>
            </div>

            <hr>
            
            <div class="card-content">
                <!-- Error Alert (show if login failed) -->
                <?php if (isset($error)): ?>
                <div class="alert alert-error">
                        <div class="alert-description"><?= $error ?></div>
                    </div>
                </div>
                <?php unset($_SESSION['login_error']); endif; ?>

                <!-- Login Method Toggle -->
                <div class="login-method-toggle">
                    <button type="button" class="toggle-button active" onclick="switchLoginMethod('email')" id="email-toggle">
                        Email Login
                    </button>
                    <button type="button" class="toggle-button" onclick="switchLoginMethod('mobile')" id="mobile-toggle">
                        Mobile Login
                    </button>
                </div>

                <!-- Login Form -->
                <form action="" method="POST" id="login-form">
                    <input type="hidden" name="login_type" id="login_type" value="email">
                    
                    <!-- Email Login Field -->
                    <div class="form-group" id="email-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="input"
                            value="<?= $_POST['email'] ?? '' ?>"
                        />
                    </div>

                    <!-- Mobile Login Field (Hidden by default) -->
                    <div class="form-group" id="mobile-group" style="display: none;">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        <input 
                            type="tel" 
                            id="mobile" 
                            name="mobile" 
                            class="input"
                            placeholder="Enter your mobile number"
                            value="<?= $_POST['mobile'] ?? '' ?>"
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
                    <hr class="mt-3 mb-2">
                    <div class="">
                        Don't have an account?
                        <a href="<?=ROOT?>authcustomer/register?redirect_url=<?=$_GET['redirect_url'] ?? "";?>">Register</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include your JavaScript if needed -->
    <script src="<?=ROOT ?>assets/js/components/toast.js"></script>
    <script>
        function switchLoginMethod(method) {
            const emailGroup = document.getElementById('email-group');
            const mobileGroup = document.getElementById('mobile-group');
            const emailToggle = document.getElementById('email-toggle');
            const mobileToggle = document.getElementById('mobile-toggle');
            const loginTypeInput = document.getElementById('login_type');
            const emailInput = document.getElementById('email');
            const mobileInput = document.getElementById('mobile');
            const loginForm = document.getElementById('login-form');

            if (method === 'email') {
                emailGroup.style.display = 'block';
                mobileGroup.style.display = 'none';
                emailToggle.classList.add('active');
                mobileToggle.classList.remove('active');
                loginTypeInput.value = 'email';
                emailInput.required = true;
                mobileInput.required = false;
                mobileInput.value = ''; // Clear mobile input
            } else if (method === 'mobile') {
                emailGroup.style.display = 'none';
                mobileGroup.style.display = 'block';
                emailToggle.classList.remove('active');
                mobileToggle.classList.add('active');
                loginTypeInput.value = 'mobile';
                emailInput.required = false;
                mobileInput.required = true;
                emailInput.value = ''; // Clear email input
            }
        }

        // Show toast if there's a success message
        <?php if (isset($_SESSION['login_success'])): ?>
        showToast('<?= $_SESSION['login_success'] ?>', 'success');
        <?php unset($_SESSION['login_success']); endif; ?>
    </script>
</body>
</html>