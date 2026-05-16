    <nav class="sidebar sidebar-admin" aria-label="Main sidebar navigation">
      <ul class="sidebar-nav">
        <li>
          <a href="<?=ROOT?>admin/dashboard" class="sidebar-link">Dashboard</a>
        </li>

        <li>
          <a href="<?=ROOT?>admin/dashboard/sellers" class="sidebar-link">
            Sellers
            <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
          </a>
        <li>

        <li>
          <a href="<?=ROOT?>admin/dashboard/stores" class="sidebar-link">
            Stores
            <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
          </a>
        <li>

        <li>
          <a href="<?=ROOT?>admin/dashboard/products" class="sidebar-link">
            Products
            <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
          </a>
        <li></li>

        <li>
          <a href="<?=ROOT?>admin/dashboard/orders" class="sidebar-link">
            Orders
            <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
          </a>
        <li>

        <li>
          <a href="<?=ROOT?>admin/dashboard/payments" class="sidebar-link">
            Payments
            <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
          </a>
        <li>

        <li>
          <a href="<?=ROOT?>admin/dashboard/customers" class="sidebar-link">
            Customers
            <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
          </a>
        <li>

        <li>
          <a href="<?=ROOT?>admin/dashboard/analytics" class="sidebar-link">
            Analytics
            <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
          </a>
        <li>
       
      </ul>
      <div class="sidebar-user-card" title="Logged in user">
        <div class="sidebar-user-info">
          <div class="sidebar-user-name"><?= Session::user()['name'] ?? 'Admin' ?></div>
          <div class="sidebar-user-email"><?= Session::user()['email'] ?? 'admin@vendora.com' ?></div>
        </div>
      </div>

    </nav>