<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - Vendora</title>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">
  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <style>
    .settings-container {
      width: 100%;
    }
    .settings-tabs {
      display: flex;
      gap: 0;
      border-bottom: 2px solid var(--border);
      margin-bottom: 28px;
    }
    .settings-tab {
      padding: 12px 28px;
      cursor: pointer;
      border: none;
      background: none;
      font-size: 0.95rem;
      font-weight: 500;
      color: var(--muted-foreground);
      border-bottom: 2px solid transparent;
      margin-bottom: -2px;
      transition: all 0.2s;
      font-family: 'Geist', sans-serif;
    }
    .settings-tab:hover {
      color: var(--foreground);
    }
    .settings-tab.active {
      color: var(--primary);
      border-bottom-color: var(--primary);
      font-weight: 600;
    }
    .settings-tab i {
      margin-right: 8px;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    .settings-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 32px;
    }
    .settings-card h3 {
      font-size: 1.15rem;
      font-weight: 600;
      color: var(--foreground);
      margin-bottom: 6px;
    }
    .settings-card .card-desc {
      font-size: 0.85rem;
      color: var(--muted-foreground);
      margin-bottom: 24px;
    }
    .form-row {
      display: flex;
      gap: 16px;
      margin-bottom: 20px;
    }
    .form-row .form-group {
      flex: 1;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--foreground);
      margin-bottom: 6px;
    }
    .avatar-upload {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 24px;
      padding: 20px;
      border: 1px dashed var(--border);
      border-radius: 12px;
      background: var(--secondary);
    }
    .avatar-preview {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--primary);
      color: var(--primary-foreground);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: 700;
      overflow: hidden;
      flex-shrink: 0;
    }
    .avatar-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .avatar-info h4 {
      font-size: 0.95rem;
      font-weight: 600;
      color: var(--foreground);
      margin-bottom: 4px;
    }
    .avatar-info p {
      font-size: 0.8rem;
      color: var(--muted-foreground);
      margin-bottom: 10px;
    }
    .btn-upload {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      font-size: 0.8rem;
      border-radius: var(--radius);
      border: 1px solid var(--border);
      background: white;
      color: var(--foreground);
      cursor: pointer;
      transition: all 0.2s;
      font-family: 'Geist', sans-serif;
    }
    .btn-upload:hover {
      background: var(--secondary);
    }
    .settings-footer {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 28px;
      padding-top: 20px;
      border-top: 1px solid var(--border);
    }
    .alert-msg {
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 0.875rem;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .alert-success {
      background: oklch(0.95 0.05 150);
      color: oklch(0.35 0.1 150);
      border: 1px solid oklch(0.85 0.08 150);
    }
    .alert-error {
      background: oklch(0.95 0.05 25);
      color: oklch(0.4 0.15 25);
      border: 1px solid oklch(0.85 0.1 25);
    }
    .password-strength {
      font-size: 0.75rem;
      color: var(--muted-foreground);
      margin-top: 4px;
    }
  </style>
</head>

<body>
  <button class="sidebar-toggle-btn" aria-label="Open sidebar">&#9776;</button>
  <div class="sidebar-backdrop"></div>

  <div class="layout">
    <?php require 'assets/components/sidebar.php' ?>

    <main class="content">
      <div class="settings-container">

        <!-- Page Header -->
        <div style="margin-bottom: 28px;">
          <h2 class="font-semibold" style="font-size: 1.5rem;">Settings</h2>
          <p style="color: var(--muted-foreground); font-size: 0.9rem; margin-top: 4px;">Manage your account settings and preferences.</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (!empty($success)): ?>
          <div class="alert-msg alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
          <div class="alert-msg alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="settings-tabs">
          <button class="settings-tab <?= $tab === 'profile' ? 'active' : '' ?>" onclick="switchTab('profile')">
            <i class="fas fa-user"></i> User Information
          </button>
          <button class="settings-tab <?= $tab === 'password' ? 'active' : '' ?>" onclick="switchTab('password')">
            <i class="fas fa-lock"></i> Password
          </button>
        </div>

        <!-- Tab 1: User Information -->
        <div id="tab-profile" class="tab-content <?= $tab === 'profile' ? 'active' : '' ?>">
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_profile">

            <div class="settings-card">
              <h3>Profile Picture</h3>
              <p class="card-desc">Upload a photo to personalize your account.</p>

              <div class="avatar-upload">
                <div class="avatar-preview" id="avatarPreview">
                  <?php if (!empty($seller['profile_picture'])): ?>
                    <img src="<?= ROOT . $seller['profile_picture'] ?>" alt="Profile">
                  <?php else: ?>
                    <?= strtoupper(substr($seller['name'], 0, 1)) ?>
                  <?php endif; ?>
                </div>
                <div class="avatar-info">
                  <h4>Profile Photo</h4>
                  <p>JPG, PNG or GIF. Max 2MB.</p>
                  <label for="profile_picture" class="btn-upload">
                    <i class="fas fa-cloud-upload-alt"></i> Choose File
                  </label>
                  <input type="file" name="profile_picture" id="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(this)">
                </div>
              </div>
            </div>

            <div class="settings-card" style="margin-top: 20px;">
              <h3>Personal Information</h3>
              <p class="card-desc">Update your name, store name, and contact details.</p>

              <div class="form-group">
                <label for="name"><i class="fas fa-user" style="color: var(--primary); margin-right: 6px;"></i>Name</label>
                <input type="text" id="name" name="name" class="input" value="<?= htmlspecialchars($seller['name']) ?>" required>
              </div>

              <div class="form-group">
                <label for="store_name"><i class="fas fa-store" style="color: var(--primary); margin-right: 6px;"></i>Store Name</label>
                <input type="text" id="store_name" name="store_name" class="input" value="<?= htmlspecialchars($storeName) ?>" <?= $storeId ? '' : 'disabled' ?>>
                <?php if (!$storeId): ?>
                  <span style="font-size: 0.75rem; color: var(--muted-foreground);">Create a store first to edit this field.</span>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="mobile_number"><i class="fas fa-phone" style="color: var(--primary); margin-right: 6px;"></i>Mobile Number</label>
                <input type="tel" id="mobile_number" name="mobile_number" class="input" value="<?= htmlspecialchars($seller['mobile_number'] ?? '') ?>" placeholder="e.g. +94 77 123 4567">
              </div>

              <div class="form-group">
                <label for="email"><i class="fas fa-envelope" style="color: var(--primary); margin-right: 6px;"></i>Email Address</label>
                <input type="email" id="email" name="email" class="input" value="<?= htmlspecialchars($seller['email']) ?>" required>
              </div>
            </div>

            <div class="settings-footer">
              <a href="<?= ROOT ?>dashboard" class="btn btn-secondary" style="text-decoration: none;">Cancel</a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save" style="margin-right: 6px;"></i> Save Changes
              </button>
            </div>
          </form>
        </div>

        <!-- Tab 2: Password -->
        <div id="tab-password" class="tab-content <?= $tab === 'password' ? 'active' : '' ?>">
          <form method="POST">
            <input type="hidden" name="action" value="change_password">

            <div class="settings-card">
              <h3>Change Password</h3>
              <p class="card-desc">Ensure your account is using a strong, unique password for security.</p>

              <div class="form-group">
                <label for="current_password"><i class="fas fa-key" style="color: var(--muted-foreground); margin-right: 6px;"></i>Current Password</label>
                <input type="password" id="current_password" name="current_password" class="input" placeholder="Enter your current password" required>
              </div>

              <div class="form-group">
                <label for="new_password"><i class="fas fa-lock" style="color: var(--primary); margin-right: 6px;"></i>New Password</label>
                <input type="password" id="new_password" name="new_password" class="input" placeholder="Enter new password" required minlength="6">
                <div class="password-strength">Minimum 6 characters required.</div>
              </div>

              <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock" style="color: var(--primary); margin-right: 6px;"></i>Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="input" placeholder="Re-enter new password" required>
              </div>
            </div>

            <div class="settings-footer">
              <a href="<?= ROOT ?>dashboard" class="btn btn-secondary" style="text-decoration: none;">Cancel</a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-shield-alt" style="margin-right: 6px;"></i> Update Password
              </button>
            </div>
          </form>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
  <script>
    function switchTab(tab) {
      document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      
      document.getElementById('tab-' + tab).classList.add('active');
      event.currentTarget.classList.add('active');

      // Update URL without reload
      const url = new URL(window.location);
      url.searchParams.set('tab', tab);
      window.history.replaceState({}, '', url);
    }

    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const preview = document.getElementById('avatarPreview');
          preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
</body>

</html>