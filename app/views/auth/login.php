<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vendora Login</title>
    <link rel="stylesheet" href="<?=ROOT ?>assets/css/main.css" />

</head>
<body">
  <main class="max-w-xl m-auto flex items-center justify-center p-4 mt-12">
    <div class="card p-6 w-full max-w-md shadow-xl bg-white rounded-2xl space-y-6">
      <h1 class="text-2xl font-bold text-center">Login to your account</h1>
      <p class="text-center text-sm text-muted">Continue managing your store</p>

      <form id="loginForm" class="grid gap-5 mt-6">
        <div class="form-group">
          <label for="email" class="label">Email</label>
          <input type="email" id="email" class="input" required />
        </div>

        <div class="form-group">
          <label for="password" class="label">Password</label>
          <input type="password" id="password" class="input" required />
        </div>

        <button type="submit" class="btn btn-primary w-full mt-4">Login</button>
      </form>

      <p class="text-center text-sm text-muted mt-2">
        Don't have an account? <a href="<?=ROOT ?>auth/register" class="link">Register</a>
      </p>
    </div>
  </main>

  <script src="script.js"></script>
</body>
</html>
