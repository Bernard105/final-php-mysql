<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? (SITE_NAME . ' Admin') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { padding-top: 64px; background: #f6f7fb; }
    .sidebar a { text-decoration:none; }
    .sidebar .nav-link.active { font-weight: 600; }
    .card { border: 0; box-shadow: 0 2px 10px rgba(0,0,0,.04); border-radius: 12px; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= SITE_URL ?>/admin"><i class="fa-solid fa-gauge-high me-2"></i><?= SITE_NAME ?> Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>"><i class="fa-solid fa-store me-1"></i>Store</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/logout"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row g-3">
    <aside class="col-12 col-lg-2">
      <div class="card p-2 sidebar">
        <?php $uri = $_SERVER['REQUEST_URI'] ?? ''; ?>
        <nav class="nav flex-column">
          <a class="nav-link <?= (strpos($uri, '/admin/products')===0) ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/products"><i class="fa-solid fa-box me-2"></i>Products</a>
          <a class="nav-link <?= (strpos($uri, '/admin/categories')===0) ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/categories"><i class="fa-solid fa-tags me-2"></i>Categories</a>
          <a class="nav-link <?= (strpos($uri, '/admin/brands')===0) ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/brands"><i class="fa-solid fa-copyright me-2"></i>Brands</a>
          <a class="nav-link <?= (strpos($uri, '/admin/orders')===0) ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/orders"><i class="fa-solid fa-receipt me-2"></i>Orders</a>
          <a class="nav-link <?= (strpos($uri, '/admin/users')===0) ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/users"><i class="fa-solid fa-users me-2"></i>Users</a>
          <hr class="my-2">
          <a class="nav-link <?= ($uri === '/admin' || $uri === '/admin/') ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</a>
        </nav>
      </div>
    </aside>

    <main class="col-12 col-lg-10">
      <div class="mb-3">
        <?php if (!empty($_SESSION['flash']['success'])): ?>
          <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash']['success']) ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash']['error'])): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash']['error']) ?></div>
        <?php endif; ?>
      </div>

      <?= $content ?>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
