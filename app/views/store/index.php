<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
  <link rel="stylesheet" href="<?= ROOT ?>assets/css/main.css">

  <!-- Store Header -->
  <header class="header" style="background: var(--background); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 50;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 1rem 2rem;">
      <div class="flex items-center justify-between">
        <!-- Store Logo/Name -->
        <div class="flex items-center">
          <h1 class="text-2xl font-bold" style="color: var(--primary);">
            <i class="fas fa-store mr-2"></i>
            Vendora Store
          </h1>
        </div>
        
        <!-- Navigation -->
        <nav class="flex items-center" style="gap: 2rem;">
          <a href="#" class="text-base font-medium" style="color: var(--foreground); text-decoration: none;">Home</a>
          <a href="#products" class="text-base font-medium" style="color: var(--foreground); text-decoration: none;">Products</a>
          <a href="#" class="text-base font-medium" style="color: var(--foreground); text-decoration: none;">About</a>
          <a href="#" class="text-base font-medium" style="color: var(--foreground); text-decoration: none;">Contact</a>
        </nav>
        
        <!-- Cart -->
        <div class="flex items-center" style="gap: 1rem;">
          <button class="btn btn-outline" style="position: relative;">
            <i class="fas fa-shopping-cart"></i>
            <span class="badge badge-primary badge-sm" style="position: absolute; top: -8px; right: -8px;">3</span>
          </button>
          <button class="btn btn-primary">
            <i class="fas fa-user mr-2"></i>
            Login
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero" style="background: linear-gradient(135deg, var(--primary), var(--accent)); padding: 4rem 0; color: white;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <div class="text-center">
        <h2 class="text-3xl font-bold mb-4" style="font-size: 3rem; margin-bottom: 1rem;">
          Welcome to Our Store
        </h2>
        <p class="text-lg mb-6" style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9;">
          Discover amazing products at unbeatable prices. Quality guaranteed!
        </p>
        <button class="btn btn-lg" style="background: white; color: var(--primary); border: none; padding: 0.75rem 2rem;">
          Shop Now
        </button>
      </div>
    </div>
  </section>

  <!-- Products Showcase -->
  <section id="products" class="products" style="padding: 4rem 0; background: var(--background);">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-4" style="color: var(--foreground);">Featured Products</h2>
        <p class="text-lg" style="color: var(--muted-foreground);">Check out our best-selling items</p>
      </div>
      
      <!-- Product Grid -->
      <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        
        <!-- Product Card 1 -->
        <div class="card">
          <div style="height: 200px; background: var(--muted); border-radius: var(--radius) var(--radius) 0 0; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-image text-3xl" style="color: var(--muted-foreground);"></i>
          </div>
          <div class="card-content">
            <h3 class="font-semibold mb-2">Premium Headphones</h3>
            <p class="text-sm mb-3" style="color: var(--muted-foreground);">High-quality wireless headphones with noise cancellation</p>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold" style="color: var(--primary);">$199.99</span>
              <button class="btn btn-primary btn-sm">Add to Cart</button>
            </div>
          </div>
        </div>

        <!-- Product Card 2 -->
        <div class="card">
          <div style="height: 200px; background: var(--muted); border-radius: var(--radius) var(--radius) 0 0; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-image text-3xl" style="color: var(--muted-foreground);"></i>
          </div>
          <div class="card-content">
            <h3 class="font-semibold mb-2">Smart Watch</h3>
            <p class="text-sm mb-3" style="color: var(--muted-foreground);">Feature-rich smartwatch with health monitoring</p>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold" style="color: var(--primary);">$299.99</span>
              <button class="btn btn-primary btn-sm">Add to Cart</button>
            </div>
          </div>
        </div>

        <!-- Product Card 3 -->
        <div class="card">
          <div style="height: 200px; background: var(--muted); border-radius: var(--radius) var(--radius) 0 0; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-image text-3xl" style="color: var(--muted-foreground);"></i>
          </div>
          <div class="card-content">
            <h3 class="font-semibold mb-2">Wireless Speaker</h3>
            <p class="text-sm mb-3" style="color: var(--muted-foreground);">Portable Bluetooth speaker with premium sound</p>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold" style="color: var(--primary);">$89.99</span>
              <button class="btn btn-primary btn-sm">Add to Cart</button>
            </div>
          </div>
        </div>

        <!-- Product Card 4 -->
        <div class="card">
          <div style="height: 200px; background: var(--muted); border-radius: var(--radius) var(--radius) 0 0; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-image text-3xl" style="color: var(--muted-foreground);"></i>
          </div>
          <div class="card-content">
            <h3 class="font-semibold mb-2">Gaming Mouse</h3>
            <p class="text-sm mb-3" style="color: var(--muted-foreground);">Precision gaming mouse with RGB lighting</p>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold" style="color: var(--primary);">$69.99</span>
              <button class="btn btn-primary btn-sm">Add to Cart</button>
            </div>
          </div>
        </div>

        <!-- Product Card 5 -->
        <div class="card">
          <div style="height: 200px; background: var(--muted); border-radius: var(--radius) var(--radius) 0 0; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-image text-3xl" style="color: var(--muted-foreground);"></i>
          </div>
          <div class="card-content">
            <h3 class="font-semibold mb-2">USB-C Hub</h3>
            <p class="text-sm mb-3" style="color: var(--muted-foreground);">Multi-port hub for modern laptops and devices</p>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold" style="color: var(--primary);">$49.99</span>
              <button class="btn btn-primary btn-sm">Add to Cart</button>
            </div>
          </div>
        </div>

        <!-- Product Card 6 -->
        <div class="card">
          <div style="height: 200px; background: var(--muted); border-radius: var(--radius) var(--radius) 0 0; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-image text-3xl" style="color: var(--muted-foreground);"></i>
          </div>
          <div class="card-content">
            <h3 class="font-semibold mb-2">Phone Case</h3>
            <p class="text-sm mb-3" style="color: var(--muted-foreground);">Protective case with wireless charging support</p>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold" style="color: var(--primary);">$24.99</span>
              <button class="btn btn-primary btn-sm">Add to Cart</button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- View All Products Button -->
      <div class="text-center">
        <button class="btn btn-outline btn-lg">
          View All Products
          <i class="fas fa-arrow-right ml-2"></i>
        </button>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features" style="padding: 4rem 0; background: var(--muted);">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
        
        <div class="text-center">
          <div style="width: 60px; height: 60px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-shipping-fast text-xl" style="color: white;"></i>
          </div>
          <h3 class="font-semibold mb-2">Free Shipping</h3>
          <p class="text-sm" style="color: var(--muted-foreground);">Free shipping on orders over $50</p>
        </div>

        <div class="text-center">
          <div style="width: 60px; height: 60px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-shield-alt text-xl" style="color: white;"></i>
          </div>
          <h3 class="font-semibold mb-2">Secure Payment</h3>
          <p class="text-sm" style="color: var(--muted-foreground);">Your payment information is safe with us</p>
        </div>

        <div class="text-center">
          <div style="width: 60px; height: 60px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-undo text-xl" style="color: white;"></i>
          </div>
          <h3 class="font-semibold mb-2">Easy Returns</h3>
          <p class="text-sm" style="color: var(--muted-foreground);">30-day return policy for your peace of mind</p>
        </div>

        <div class="text-center">
          <div style="width: 60px; height: 60px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
            <i class="fas fa-headset text-xl" style="color: white;"></i>
          </div>
          <h3 class="font-semibold mb-2">24/7 Support</h3>
          <p class="text-sm" style="color: var(--muted-foreground);">Round-the-clock customer support</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer style="background: var(--foreground); color: var(--background); padding: 3rem 0 1rem;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        
        <!-- Company Info -->
        <div>
          <h3 class="font-bold mb-4 text-lg">Vendora Store</h3>
          <p class="text-sm mb-4" style="opacity: 0.8;">Your trusted partner for quality products and exceptional service. We're committed to bringing you the best shopping experience.</p>
          <div class="flex" style="gap: 1rem;">
            <a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none;"><i class="fab fa-facebook text-lg"></i></a>
            <a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none;"><i class="fab fa-twitter text-lg"></i></a>
            <a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none;"><i class="fab fa-instagram text-lg"></i></a>
            <a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none;"><i class="fab fa-linkedin text-lg"></i></a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="font-bold mb-4 text-lg">Quick Links</h3>
          <ul style="list-style: none;">
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Home</a></li>
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Products</a></li>
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">About Us</a></li>
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Contact</a></li>
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">FAQ</a></li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div>
          <h3 class="font-bold mb-4 text-lg">Customer Service</h3>
          <ul style="list-style: none;">
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Shipping Info</a></li>
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Returns & Exchanges</a></li>
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Size Guide</a></li>
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Track Your Order</a></li>
            <li class="mb-2"><a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Privacy Policy</a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div>
          <h3 class="font-bold mb-4 text-lg">Contact Us</h3>
          <div class="mb-2">
            <i class="fas fa-map-marker-alt mr-2"></i>
            <span class="text-sm" style="opacity: 0.8;">123 Store Street, City, State 12345</span>
          </div>
          <div class="mb-2">
            <i class="fas fa-phone mr-2"></i>
            <span class="text-sm" style="opacity: 0.8;">+1 (555) 123-4567</span>
          </div>
          <div class="mb-2">
            <i class="fas fa-envelope mr-2"></i>
            <span class="text-sm" style="opacity: 0.8;">info@vendorastore.com</span>
          </div>
        </div>
      </div>
      
      <hr style="border-color: rgba(255, 255, 255, 0.2);">
      
      <div class="flex items-center justify-between" style="padding-top: 1rem;">
        <p class="text-sm" style="opacity: 0.8;">&copy; 2025 Vendora Store. All rights reserved.</p>
        <div class="flex" style="gap: 1rem;">
          <a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Terms of Service</a>
          <a href="#" style="color: var(--background); opacity: 0.8; text-decoration: none; font-size: 0.875rem;">Privacy Policy</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- <script src="https://kit.fontawesome.com/4c8b60a8ce.js" crossorigin="anonymous"></script> -->
  <script src="<?= ROOT ?>assets/js/components/sidebar.js"></script>
</body>
</html>