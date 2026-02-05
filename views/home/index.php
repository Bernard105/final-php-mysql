<?php
  // Modern homepage (Bootstrap 5) with: hero, best sellers tabs (with badges),
  // responsive new arrivals carousel (4/2/1), brand strip, newsletter.
  $products = $products ?? [];
  $categories = $categories ?? [];
  $brands = $brands ?? [];

  $heroProduct = $products[0] ?? null;

  // Best sellers: no sales table available → use first 8 as "best sellers"
  $bestSellers = array_slice($products, 0, 8);

  // New arrivals: no created_at → reverse list as "new"
  $newArrivals = array_slice(array_reverse($products), 0, 12);

  // Tabs: show top 4 categories
  $filterCats = array_slice($categories, 0, 4);

  // Badge counts per tab
  $bestCounts = ['all' => count($bestSellers)];
  foreach ($filterCats as $c) {
    $cid = (int)($c['category_id'] ?? 0);
    $bestCounts[(string)$cid] = 0;
  }
  foreach ($bestSellers as $p) {
    $cid = (int)($p['category_id'] ?? 0);
    if (isset($bestCounts[(string)$cid])) {
      $bestCounts[(string)$cid]++;
    }
  }

  // Flash for newsletter
  $newsletterOk = $_SESSION['newsletter_ok'] ?? null;
  $newsletterErr = $_SESSION['newsletter_err'] ?? null;
  unset($_SESSION['newsletter_ok'], $_SESSION['newsletter_err']);
?>

<div class="container py-4">

  <?php if ($newsletterOk): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($newsletterOk) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  <?php if ($newsletterErr): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($newsletterErr) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- HERO -->
  <section class="home-hero mb-4">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <div class="p-4 p-lg-5 rounded-4 border bg-white shadow-sm">
          <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <span class="badge text-bg-primary">New arrivals</span>
            <span class="badge text-bg-light border">Easy checkout • Fast delivery</span>
          </div>

          <h1 class="display-6 fw-bold mb-2">Shop smarter. <span class="d-block">Look sharper.</span></h1>
          <p class="text-muted mb-4">Browse products by category, discover new drops, and checkout in minutes.</p>

          <form class="mb-3" method="GET" action="<?= SITE_URL ?>/search" role="search">
            <div class="input-group input-group-lg">
              <span class="input-group-text"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input class="form-control" name="q" type="search" placeholder="Search products…" aria-label="Search products" autocomplete="off">
              <button class="btn btn-primary" type="submit">Search</button>
            </div>
          </form>

          <?php if (!empty($categories)): ?>
            <div class="d-flex flex-wrap gap-2 mt-2">
              <?php foreach (array_slice($categories, 0, 10) as $c): ?>
                <a class="btn btn-sm btn-outline-primary rounded-pill" href="<?= SITE_URL ?>/category/<?= (int)($c['category_id'] ?? 0) ?>">
                  <?= htmlspecialchars($c['category_title'] ?? 'Category') ?>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="rounded-4 border bg-white shadow-sm overflow-hidden">
          <?php if ($heroProduct): ?>
            <img
              style="height: 320px; object-fit: cover; width: 100%;"
              src="<?= SITE_URL ?>/uploads/<?= htmlspecialchars($heroProduct['product_image1'] ?? 'default.png') ?>"
              alt="<?= htmlspecialchars($heroProduct['product_title'] ?? 'Featured product') ?>"
            />
            <div class="p-3">
              <div class="fw-semibold text-truncate"><?= htmlspecialchars($heroProduct['product_title'] ?? 'Featured pick') ?></div>
              <div class="text-muted">$<?= htmlspecialchars($heroProduct['product_price'] ?? '0.00') ?></div>
              <div class="mt-2 d-flex gap-2">
                <a class="btn btn-primary btn-sm" href="<?= SITE_URL ?>/product/<?= (int)($heroProduct['product_id'] ?? 0) ?>">View details</a>
                <a class="btn btn-outline-primary btn-sm" href="<?= SITE_URL ?>/products">Browse all</a>
              </div>
            </div>
          <?php else: ?>
            <div class="p-5 text-center">
              <div class="h4 fw-bold">Today’s picks</div>
              <div class="text-muted">Fresh products added regularly.</div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- PROMO BANNER -->
  <section class="mb-4">
    <div class="p-4 rounded-4 border text-white" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
      <div class="row align-items-center g-3">
        <div class="col-lg-8">
          <div class="small opacity-75"><i class="fas fa-badge-percent me-2"></i>Limited-time offer</div>
          <div class="h3 fw-bold mb-1">Up to <span class="text-warning">25% OFF</span> selected items</div>
          <div class="opacity-75">New deals every week. Grab them before they’re gone.</div>
        </div>
        <div class="col-lg-4 text-lg-end">
          <a class="btn btn-light fw-semibold" href="<?= SITE_URL ?>/products"><i class="fas fa-bolt me-2"></i>Shop deals</a>
        </div>
      </div>
    </div>
  </section>

  <!-- BRAND STRIP -->
  <?php if (!empty($brands)): ?>
    <section class="mb-4">
      <div class="d-flex justify-content-between align-items-end mb-2">
        <div>
          <h2 class="h4 fw-bold mb-0">Top brands</h2>
          <div class="text-muted small">Browse by brand</div>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="<?= SITE_URL ?>/products">Explore all</a>
      </div>
      <div class="brand-strip d-flex gap-2 overflow-auto pb-2">
        <?php foreach (array_slice($brands, 0, 18) as $b): ?>
          <a class="btn btn-sm btn-light border rounded-pill" href="<?= SITE_URL ?>/brand/<?= (int)($b['brand_id'] ?? 0) ?>">
            <i class="fas fa-tag me-2 text-primary" aria-hidden="true"></i><?= htmlspecialchars($b['brand_title'] ?? 'Brand') ?>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- BEST SELLERS (FILTER TABS + BADGES) -->
  <section class="mb-4" id="best-sellers">
    <div class="d-flex justify-content-between align-items-end mb-2">
      <div>
        <h2 class="h4 fw-bold mb-0">Best sellers</h2>
        <div class="text-muted small">Customer favorites this week</div>
      </div>
      <a class="btn btn-outline-primary btn-sm" href="<?= SITE_URL ?>/products">View all</a>
    </div>

    <div class="best-seller-tabs mb-3" data-best-sellers>
      <div class="nav nav-pills flex-wrap gap-2" role="tablist" aria-label="Best sellers filter">
        <button class="nav-link active" type="button" data-filter="all">
          All <span class="badge text-bg-light border ms-2"><?= (int)($bestCounts['all'] ?? 0) ?></span>
        </button>
        <?php foreach ($filterCats as $c): ?>
          <?php $cid = (int)($c['category_id'] ?? 0); ?>
          <button class="nav-link" type="button" data-filter="<?= $cid ?>">
            <?= htmlspecialchars($c['category_title'] ?? 'Category') ?>
            <span class="badge text-bg-light border ms-2"><?= (int)($bestCounts[(string)$cid] ?? 0) ?></span>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="row g-3">
      <?php foreach ($bestSellers as $i => $p): ?>
        <div class="col-12 col-sm-6 col-lg-3 best-seller-item" data-cat="<?= (int)($p['category_id'] ?? 0) ?>">
          <div class="card product-card h-100">
            <?php if ($i < 3): ?>
              <div class="position-absolute top-0 start-0 m-2 badge text-bg-warning">Hot</div>
            <?php endif; ?>
            <img class="product-img" src="<?= SITE_URL ?>/uploads/<?= htmlspecialchars($p['product_image1'] ?? 'default.png') ?>" alt="">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1 text-truncate"><?= htmlspecialchars($p['product_title'] ?? '') ?></h5>
              <div class="text-muted mb-2">$<?= htmlspecialchars($p['product_price'] ?? '0.00') ?></div>
              <div class="mt-auto d-flex gap-2">
                <a class="btn btn-primary btn-sm" href="<?= SITE_URL ?>/product/<?= (int)$p['product_id'] ?>">Details</a>
                <form method="POST" action="<?= SITE_URL ?>/cart/add/<?= (int)$p['product_id'] ?>">
                  <button class="btn btn-success btn-sm" type="submit"><i class="fas fa-cart-plus me-1"></i>Add</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- NEW ARRIVALS (RESPONSIVE CAROUSEL 4/2/1) -->
  <section class="mb-4" id="new-arrivals">
    <div class="d-flex justify-content-between align-items-end mb-2">
      <div>
        <h2 class="h4 fw-bold mb-0">New arrivals</h2>
        <div class="text-muted small">Fresh drops — don’t miss out</div>
      </div>
      <a class="btn btn-outline-primary btn-sm" href="<?= SITE_URL ?>/products">See more</a>
    </div>

    <div id="newArrivalsCarousel" class="carousel slide" data-bs-ride="carousel" data-multi-carousel>
      <div class="carousel-indicators" data-carousel-indicators></div>

      <div class="carousel-inner" data-carousel-items>
        <?php foreach ($newArrivals as $idx => $p): ?>
          <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
            <div class="card product-card">
              <img class="product-img" src="<?= SITE_URL ?>/uploads/<?= htmlspecialchars($p['product_image1'] ?? 'default.png') ?>" alt="">
              <div class="card-body">
                <h5 class="card-title mb-1 text-truncate"><?= htmlspecialchars($p['product_title'] ?? '') ?></h5>
                <div class="text-muted mb-2">$<?= htmlspecialchars($p['product_price'] ?? '0.00') ?></div>
                <div class="d-flex gap-2">
                  <a class="btn btn-primary btn-sm" href="<?= SITE_URL ?>/product/<?= (int)$p['product_id'] ?>">Details</a>
                  <form method="POST" action="<?= SITE_URL ?>/cart/add/<?= (int)$p['product_id'] ?>">
                    <button class="btn btn-success btn-sm" type="submit"><i class="fas fa-cart-plus me-1"></i>Add</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#newArrivalsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#newArrivalsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </section>

  <!-- NEWSLETTER (REAL SUBMIT) -->
  <section class="mb-4" id="newsletter">
    <div class="p-4 p-lg-5 rounded-4 border bg-white shadow-sm">
      <div class="row align-items-center g-3">
        <div class="col-lg-6">
          <div class="small text-muted"><i class="fas fa-envelope-open-text me-2"></i>Newsletter</div>
          <div class="h4 fw-bold mb-1">Get deals in your inbox</div>
          <div class="text-muted">Weekly drops, coupons, and early access to new arrivals. No spam.</div>
        </div>
        <div class="col-lg-6">
          <form method="POST" action="<?= SITE_URL ?>/newsletter/subscribe" class="newsletter-form">
            <div class="input-group input-group-lg">
              <span class="input-group-text"><i class="fas fa-at" aria-hidden="true"></i></span>
              <input class="form-control" type="email" name="email" placeholder="Enter your email" aria-label="Email" required>
              <button class="btn btn-primary" type="submit">Subscribe</button>
            </div>
            <div class="form-text">You can unsubscribe anytime.</div>
          </form>
        </div>
      </div>
    </div>
  </section>

</div>
