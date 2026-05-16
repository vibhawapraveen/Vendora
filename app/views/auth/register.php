<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vendora Login</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css" />

</head>

<body>
  <main class="max-w-xl m-auto flex items-center justify-center p-4 mt-12">
    <div class="card p-6 w-full max-w-md shadow-xl bg-white rounded-2xl space-y-6">
      <h1 class="text-2xl font-bold text-center">Create your account</h1>
      <p class="text-center text-sm text-muted">Join Vendora for free!</p>

      <hr>

      <form id="loginForm" class="grid gap-5 mt-6">
        <div class="flex gap-3">
          <div class="form-group flex-1">
            <label for="email" class="label">Business name</label>
            <!-- <div class="text-xs text-muted">This won't be shared with anyone</div> -->
            <input type="email" id="email" class="input" required />
          </div>
          <div class="form-group flex-1">
            <label for="email" class="label">Owner's name</label>
            <!-- <div class="text-xs text-muted">This won't be shared with anyone</div> -->
            <input type="email" id="email" class="input" required />
          </div>
        </div>

        <div class="form-group">
          <label for="email" class="label">Email address</label>
          <input type="email" id="email" class="input" required />
        </div>

        <div class="flex gap-3">
          <div class="form-group flex-1">
            <label for="email" class="label">Mobile number</label>
            <!-- <div class="text-xs text-muted">This won't be shared with anyone</div> -->
            <input type="text" id="email" class="input" required />
          </div>
          <div class="form-group flex-1">
            <label for="email" class="label">Business type</label>
            <!-- <div class="text-xs text-muted">This won't be shared with anyone</div> -->
            <select class="select select-sm w-full">
              <option>Default</option>
              <option>Option 1</option>
              <option>Option 2</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="password" class="label">New password</label>
          <input type="password" id="password" class="input" required />
        </div>
        <div class="form-group">
          <label for="password" class="label">Confirm password</label>
          <input type="password" id="password" class="input" required />
        </div>

        <div class="form-group">
          <div class="checkbox">
            <input type="checkbox" id="agree" />
            <label for="agree">I agree to the terms</label>
          </div>
        </div>


        <button type="submit" class="btn btn-primary w-full mt-4">Create Account</button>
      </form>

      <p class="text-center text-sm text-muted mt-2">
        Already have an account? <a href="<?= ROOT ?>auth/login" class="link">Login</a>
      </p>
    </div>
  </main>
  
  <script src="script.js"></script>
</body>
</html>
