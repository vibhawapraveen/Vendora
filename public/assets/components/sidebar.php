<nav class="sidebar" aria-label="Main sidebar navigation">
  <ul class="sidebar-nav">
    <li>
      <a href="<?= ROOT ?>dashboard" class="sidebar-link">Dashboard</a>
    </li>

    <li>
      <a href="<?= ROOT ?>dashboard/storefront" class="sidebar-link">
        Storefront
        <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
      </a>
      <ul class="sub-items">
        <li><a href="<?= ROOT ?>dashboard/storefront">Overview</a></li>
        <li><a href="<?= ROOT ?>dashboard/storefront/template">Template</a></li>
        <li><a href="<?= ROOT ?>dashboard/storefront/customize">Customize</a></li>
      </ul>
    </li>

    <li>
      <a href="<?= ROOT ?>dashboard/products" class="sidebar-link">
        Products
        <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
      </a>
      <ul class="sub-items">
        <li><a href="<?= ROOT ?>dashboard/products">Overview</a></li>
        <li><a href="<?= ROOT ?>dashboard/products/all">All Products</a></li>
        <li><a href="<?= ROOT ?>dashboard/products/newproduct">New Products</a></li>
        <li><a href="<?= ROOT ?>dashboard/products/managecategories">Manage Categories</a></li>
        <li><a href="<?= ROOT ?>dashboard/products/lowstockalerts">Low Stock Alerts</a></li>
      </ul>
    </li>

    <li>
      <a href="<?= ROOT ?>dashboard/orders" class="sidebar-link">
        Orders
        <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
      </a>
      <ul class="sub-items">
        <li><a href="<?= ROOT ?>dashboard/orders">Overview</a></li>
        <li><a href="<?= ROOT ?>dashboard/orders/all">All orders</a></li>
      </ul>
    </li>

    <li>
      <a href="<?= ROOT ?>dashboard/customers" class="sidebar-link">
        Customers
        <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
      </a>
      <ul class="sub-items">
        <li><a href="<?= ROOT ?>dashboard/customers">Overview</a></li>
        <li><a href="<?= ROOT ?>dashboard/customers/all">All customers</a></li>
        <!-- <li><a href="<?= ROOT ?>dashboard/customers/newcustomer">New customer</a></li> -->
      </ul>
    </li>

    <li>
      <a href="<?= ROOT ?>dashboard/earnings" class="sidebar-link">
        Earnings
        <button class="collapse-btn" aria-label="Toggle sub-menu">&#9654;</button>
      </a>
      <ul class="sub-items">
        <li><a href="<?= ROOT ?>dashboard/earnings">Overview</a></li>
        <li><a href="<?= ROOT ?>dashboard/earnings/stripeaccount">Stripe Account</a></li>
      </ul>
    </li>

    <li>
      <a href="<?= ROOT ?>dashboard/pos" class="sidebar-link">
        POS
      </a>
    </li>


  </ul>

  <div>
    <ul class="sidebar-nav">
      <li>
        <a href="<?= ROOT ?>dashboard/settings" class="sidebar-link">
          Settings
          <i class="fas fa-bars" style="font-size: 0.9rem; opacity: 0.7;"></i>
        </a>
      </li>
    </ul>
    <div class="sidebar-user-card" title="Logged in user">
      <div class="sidebar-user-avatar">
        <?php if (!empty(Session::user()['profile_picture'])): ?>
          <img src="<?= ROOT . Session::user()['profile_picture'] ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
        <?php else: ?>
          😁
        <?php endif; ?>
      </div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name"><?= Session::user()['store_name'] ?></div>
        <div class="sidebar-user-email"><?= Session::user()['email'] ?></div>
        <a href=<?= ROOT . "auth/signout" ?> class="text-xs">Sign Out</a>
      </div>
    </div>
  </div>

</nav>