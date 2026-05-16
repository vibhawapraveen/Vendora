<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Store Not Found - Vendora</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <style>
    .error-container {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .error-content {
      text-align: center;
      max-width: 500px;
    }

    .error-icon {
      font-size: 6rem;
      margin-bottom: 1.5rem;
      opacity: 0.9;
    }

    .error-code {
      font-size: 8rem;
      font-weight: 800;
      line-height: 1;
      margin-bottom: 1rem;
      color: white;
      text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .error-title {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: white;
    }

    .error-description {
      font-size: 1.125rem;
      margin-bottom: 2rem;
      color: rgba(255, 255, 255, 0.9);
      line-height: 1.6;
    }

    .error-actions {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-light {
      background: white;
      color: #667eea;
      border: none;
    }

    .btn-light:hover {
      background: rgba(255, 255, 255, 0.9);
    }
  </style>
</head>

<body>
  <div class="error-container">
    <div class="error-content">
      <div class="error-code">404</div>
      <h1 class="error-title">Store Not Found</h1>
      <p class="error-description">
        The store you're looking for doesn't exist or may have been removed.
        Please check the URL and try again.
      </p>
      <div class="error-actions">
        <button class="btn btn-light btn-lg" onclick="window.history.back()">
          <i class="fa-solid fa-arrow-left mr-2"></i> Go Back
        </button>
      </div>
    </div>
  </div>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
</body>

</html>