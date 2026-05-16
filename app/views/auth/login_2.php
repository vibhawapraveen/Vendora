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
<body">
  <main class="flex">
    <div class="w-1/2 side-img">
    </div>
    <div class="bg- w-1/2 flex items-center justify-center">
      <div class=" p-6 w-full max-w-md shadow-xl bg-white rounded-2xl space-y-6">
        <h1 class="text-2xl font-bold text-center">Login to your account</h1>
        <p class="text-center text-sm text-muted">Continue managing your store</p>

        <hr>

        <?php if(isset($error)){ ?><div class="alert alert-error" style="margin-bottom: 1rem">
          <div class="alert-content">
            <div class="alert-title"><?php echo $error ?></div>
          </div>
        </div>
        <?php } ?>

        <form id="loginForm" method="post" action="" class="grid gap-5 mt-6">
          <div class="form-group">
            <label for="email" class="label">Email</label>
            <input name="email" type="email" id="email" class="input" required />
          </div>

          <div class="form-group">
            <label for="password" class="label">Password</label>
            <input name="password" type="password" id="password" class="input" required />
          </div>

          <button type="submit" class="btn btn-primary w-full mt-4">Login</button>
        </form>

        <p class="text-center text-sm text-muted mt-2">
          Don't have an account? <a href="<?= ROOT ?>auth/register" class="link">Register</a>
        </p>
      </div>
    </div>
  </main>

  <script src="script.js"></script>
  </body>

</html>