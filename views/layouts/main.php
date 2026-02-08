<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? SITE_NAME ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS (Bootstrap-first overrides + UX polish) -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">

    <!-- jQuery (kept for existing AJAX helpers) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Make SITE_URL available to JS safely -->
    <script>
      window.SITE_URL = <?= json_encode(SITE_URL) ?>;
    </script>
</head>
<body>
    <a class="skip-link btn btn-light" href="#mainContent">Skip to content</a>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= SITE_URL ?>">
                <i class="fas fa-store me-2"></i><?= SITE_NAME ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Menu -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/products">
                            <i class="fas fa-box me-1"></i> Products
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-list me-1"></i> Categories
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            // This would be populated from database
                            $categories = [
                                ['id' => 1, 'name' => 'Electronics'],
                                ['id' => 2, 'name' => 'Fashion'],
                                ['id' => 3, 'name' => 'Home & Garden'],
                                ['id' => 4, 'name' => 'Books']
                            ];
                            
                            foreach ($categories as $category): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= SITE_URL ?>/category/<?= $category['id'] ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/contact">
                            <i class="fas fa-envelope me-1"></i> Contact
                        </a>
                    </li>
                </ul>
                
                <!-- Search Form -->
                <form class="d-flex me-3" action="<?= SITE_URL ?>/search" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" name="q" placeholder="Search products..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Right Menu -->
                <ul class="navbar-nav">
                    <!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link position-relative js-cart-link" href="<?= SITE_URL ?>/cart" aria-label="Cart">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                                <span class="badge bg-danger badge-notification">
                                    <?= $_SESSION['cart_count'] ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <!-- User Menu -->
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                <?= htmlspecialchars($_SESSION['user']['username'] ?? 'User') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?= SITE_URL ?>/profile">
                                        <i class="fas fa-user-circle me-2"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= SITE_URL ?>/orders">
                                        <i class="fas fa-shopping-bag me-2"></i> Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= SITE_URL ?>/wishlist">
                                        <i class="fas fa-heart me-2"></i> Wishlist
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= SITE_URL ?>/admin">
                                            <i class="fas fa-cog me-2"></i> Admin Panel
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= SITE_URL ?>/logout">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/login">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/register">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="container mt-3">
            <?php foreach ($_SESSION['flash'] as $type => $messages): ?>
                <?php if (is_array($messages)): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?= $type ?> alert-dismissible fade show">
                            <?php if (is_array($message)) { $message = implode(' ', array_map(function($m){ return is_array($m) ? implode(' ', $m) : $m; }, $message)); } ?>
                            <?= htmlspecialchars((string)$message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-<?= $type ?> alert-dismissible fade show">
                        <?= htmlspecialchars($messages) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main id="mainContent" class="container py-4">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><?= SITE_NAME ?></h5>
                    <p class="text-light">Your one-stop shop for all your needs. Quality products at affordable prices with excellent customer service.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= SITE_URL ?>">Home</a></li>
                        <li><a href="<?= SITE_URL ?>/products">Products</a></li>
                        <li><a href="<?= SITE_URL ?>/about">About Us</a></li>
                        <li><a href="<?= SITE_URL ?>/contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5>Customer Service</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= SITE_URL ?>/faq">FAQ</a></li>
                        <li><a href="<?= SITE_URL ?>/shipping">Shipping Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/returns">Returns & Refunds</a></li>
                        <li><a href="<?= SITE_URL ?>/privacy">Privacy Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/terms">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled text-light">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 184 Le Dai Hanh st</li>
                        <li><i class="fas fa-phone me-2"></i> +1 234 567 890</li>
                        <li><i class="fas fa-envelope me-2"></i> support@<?= strtolower(SITE_NAME) ?>.com</li>
                        <li><i class="fas fa-clock me-2"></i> Mon-Fri: 9AM-6PM</li>
                    </ul>
                </div>
            </div>
            
            <hr class="bg-light">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">
                        <i class="fab fa-cc-visa fa-2x me-2"></i>
                        <i class="fab fa-cc-mastercard fa-2x me-2"></i>
                        <i class="fab fa-cc-paypal fa-2x me-2"></i>
                        <i class="fab fa-cc-apple-pay fa-2x"></i>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mini cart offcanvas (progressive enhancement: link still works without JS) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="miniCart" aria-labelledby="miniCartLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="miniCartLabel"><i class="fas fa-shopping-cart me-2"></i>Your cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body" data-mini-cart-body>
        <div class="mini-cart-skeleton" data-mini-cart-skeleton>
          <div class="skeleton-line w-75"></div>
          <div class="skeleton-line w-100"></div>
          <div class="skeleton-line w-90"></div>
          <div class="skeleton-line w-80"></div>
        </div>
        <div class="d-none" data-mini-cart-content></div>
      </div>
      <div class="offcanvas-footer p-3 border-top">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">Total</span>
          <span class="fw-semibold">$<span data-mini-cart-total>0.00</span></span>
        </div>
        <div class="d-grid gap-2">
          <a class="btn btn-outline-primary" href="<?= SITE_URL ?>/cart">View cart</a>
          <a class="btn btn-success" href="<?= SITE_URL ?>/checkout">Checkout</a>
        </div>
      </div>
    </div>

    <!-- Quick view modal (used by product cards) -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="quickViewLabel">Quick view</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12 col-md-5">
                <div class="ratio ratio-4x3 rounded overflow-hidden bg-light position-relative">
                  <img class="w-100 h-100 object-fit-cover" data-qv-img alt="">
                  <div class="img-skeleton-overlay" data-qv-img-skel></div>
                </div>
              </div>
              <div class="col-12 col-md-7">
                <h4 class="h5 mb-2" data-qv-title></h4>
                <div class="h5 mb-3">$<span data-qv-price></span></div>
                <div class="d-flex gap-2 flex-wrap">
                  <a class="btn btn-outline-primary" data-qv-details href="#">View details</a>
                  <button class="btn btn-success" type="button" data-qv-add>Add to cart</button>
                </div>
                <div class="form-text mt-2">Tip: “Quick view” uses only data already on the page, so it can’t break server features.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Theme behaviors (filters, carousel, cart totals, toasts, back-to-top) -->
    <script src="<?= SITE_URL ?>/assets/js/app.js"></script>

    <script>
      // Keep this small block for backwards compatibility with pages that may call updateCartCount().
      window.updateCartCount = function updateCartCount(count) {
        const badge = document.querySelector('.badge-notification');
        if (!badge) return;
        badge.textContent = count;
        badge.style.display = (count > 0) ? 'inline-block' : 'none';
      };

      // Auto-dismiss flash alerts after 5 seconds (non-blocking)
      window.setTimeout(() => {
        document.querySelectorAll('.alert').forEach((el) => {
          try { bootstrap.Alert.getOrCreateInstance(el).close(); } catch (e) {}
        });
      }, 5000);
    </script>
</body>
</html>