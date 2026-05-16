<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vendora Login</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css" />
  <style>
    .side-img {
      height: 100vh;
      background-image:
        linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
        /* Black overlay */
        url("<?= ROOT ?>assets/img/auth-bg-2.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    }
  </style>
</head>

<body>
  <main class="flex">
    <div class="w-1/2 side-img">
    </div>
    <div class="bg- w-1/2 flex items-center justify-center">
      <div class="p-6 w-full max-w-md shadow-xl bg-white rounded-2xl space-y-6">
        <h1 class="text-2xl font-bold text-center">Create your account</h1>
        <p class="text-center text-sm text-muted">Join Vendora for free!</p>

        <hr>
        <?php if(isset($error)){ ?><div class="alert alert-error" style="margin-bottom: 1rem">
          <div class="alert-content">
            <div class="alert-title"><?php echo $error ?></div>
          </div>
        </div>
        <?php } ?>

        <form action="" method="post" id="loginForm" class="grid gap-5 mt-6">
          <div class="form-group">
            <label for="name" class="label">Owner's name</label>
            <input name="name" required type="text" id="name" class="input" value="<?= $_POST['name'] ?? '' ?>" />
          </div>

          <div class="form-group">
            <label for="store_name" class="label">Store name</label>
            <input name="store_name" required type="text" id="store_name" class="input" value="<?= $_POST['store_name'] ?? '' ?>" />
          </div>

          <div class="form-group">
            <label for="email" class="label">Email address</label>
            <input name="email" required type="email" id="email" class="input" value="<?= $_POST['email'] ?? '' ?>" />
          </div>

          <div class="flex gap-3">
            <div class="form-group flex-1">
              <label for="mobile" class="label">Mobile number</label>
              <input name="mobile" required type="text" id="mobile" class="input" value="<?= $_POST['mobile'] ?? '' ?>" />
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="label">New password</label>
            <input name="password" required type="password" id="password" class="input" />
          </div>
          <div class="form-group">
            <label for="password_confirm" class="label">Confirm password</label>
            <input name="password_confirm" required type="password" id="password_confirm" class="input" />
          </div>

          <div class="form-group">
            <div class="checkbox">
              <input type="checkbox" id="agree" required />
              <label for="agree">I agree to the terms</label>
            </div>
          </div>


          <button type="submit" class="btn btn-primary w-full mt-4">Create Account</button>
        </form>

        <p class="text-center text-sm text-muted mt-2">
          Already have an account? <a href="<?= ROOT ?>auth/login" class="link">Login</a>
        </p>
      </div>
    </div>
  </main>

  <script src="script.js"></script>
</body>

</html>