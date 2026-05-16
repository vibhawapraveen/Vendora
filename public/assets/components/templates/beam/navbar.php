<nav class="navbar">
    <div class="navbar-container">
        <a href="<?= ROOT.$storecode ?>" class="navbar-logo">
            <img src="<?= $content['logo'] ?>" alt="">
        </a>
        <div class="navbar-icons">
            <a class="navbar-icon-btn" href="<?= ROOT.$storecode ?>/cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
            </a>
            <a class="navbar-icon-btn" href="<?= ROOT.$storecode ?>/orders">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </div>
</nav>