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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .product-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .cart-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
        
        .badge-notification {
            position: absolute;
            top: -5px;
            right: -5px;
        }
        
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }
        
        .footer a {
            color: #ddd;
            text-decoration: none;
        }
        
        .footer a:hover {
            color: white;
            text-decoration: underline;
        }
        
        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 10px;
            transition: background 0.3s;
        }
        
        .social-icons a:hover {
            background: var(--primary-color);
        }
    </style>
</head>
<body>
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
                        <a class="nav-link position-relative" href="<?= SITE_URL ?>/cart">
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
    <main class="container py-4">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Theme behaviors (best-seller tabs + responsive multi-item carousel) -->
    <script src="<?= SITE_URL ?>/assets/js/app.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);
        
        // Cart count update
        function updateCartCount(count) {
            const badge = document.querySelector('.badge-notification');
            if (badge) {
                badge.textContent = count;
                if (count > 0) {
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
        
        // Add to cart with AJAX
        function addToCart(productId, quantity = 1) {
            $.ajax({
                url: '<?= SITE_URL ?>/cart/add/' + productId,
                method: 'POST',
                data: { quantity: quantity },
                success: function(response) {
                    updateCartCount(response.count);
                    alert('Product added to cart!');
                },
                error: function() {
                    alert('Failed to add product to cart.');
                }
            });
        }
        
        // Search suggestions
        $('#searchInput').on('input', function() {
            const query = $(this).val();
            if (query.length > 2) {
                // Implement search suggestions
            }
        });
    </script>
</body>
</html>