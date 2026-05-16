<!-- ============ HEADER ============ -->
<header>
    <div class="header-container">
        <div class="header-left">
             <a href="#" class="logo">
            <img src="<?= $content['logo'] ?>" alt="" style="height: 40px; width: auto;">
        </a>
            
        </div>

       <nav class="main-nav">
                <a href="<?= ROOT . $storecode ?>" class="<?= !isset($totalProducts) ? 'active' : ''; ?>">Home</a>
                <a href="<?= ROOT . $storecode ?>/products" class="<?= isset($totalProducts) ? 'active' : ''; ?>">Shop</a>
            </nav>

        <div class="header-right">
            <!-- <div class="search-container">
                <input type="text" placeholder="Search Products" class="search-input">
            </div> -->
            <div class="header-actions">
                <a class="navbar-icon-btn" href="<?= ROOT . $storecode ?>/orders">
                    <i class="fas fa-user"></i>
                </a>
                <a class="navbar-icon-btn" href="<?= ROOT . $storecode ?>/cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
                </a>
            </div>
        </div>
    </div>
</header>