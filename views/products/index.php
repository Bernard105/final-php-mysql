<?php
  $filters = $filters ?? [];
  $pagination = $pagination ?? ['page'=>1,'pages'=>1,'perPage'=>9,'total'=>0];
  $q = (string)($filters['q'] ?? '');
  $categoryId = (int)($filters['category_id'] ?? 0);
  $brandId = (int)($filters['brand_id'] ?? 0);
  $minPrice = (string)($filters['min_price'] ?? '');
  $maxPrice = (string)($filters['max_price'] ?? '');
  $sort = (string)($filters['sort'] ?? 'newest');

  function build_query(array $overrides = []) {
    $params = $_GET ?? [];
    foreach ($overrides as $k => $v) {
      if ($v === null || $v === '') {
        unset($params[$k]);
      } else {
        $params[$k] = $v;
      }
    }
    return http_build_query($params);
  }
?>
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">All Products</h1>
    <small class="text-muted">
      Showing <?= (int)count($products ?? []) ?> / <?= (int)($pagination['total'] ?? 0) ?>
    </small>
  </div>

  <div class="row g-3">
    <!-- Filters -->
    <div class="col-12 col-lg-3">
      <div class="card">
        <div class="card-body">
          <form method="get" action="<?= SITE_URL ?>/products">
            <div class="mb-2">
              <label class="form-label">Keyword</label>
              <input class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search name, description...">
            </div>

            <div class="mb-2">
              <label class="form-label">Category</label>
              <select class="form-select" name="category_id">
                <option value="">All</option>
                <?php foreach (($categories ?? []) as $c): ?>
                  <option value="<?= (int)$c['category_id'] ?>" <?= ((int)$c['category_id'] === $categoryId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['category_title'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-2">
              <label class="form-label">Brand</label>
              <select class="form-select" name="brand_id">
                <option value="">All</option>
                <?php foreach (($brands ?? []) as $b): ?>
                  <option value="<?= (int)$b['brand_id'] ?>" <?= ((int)$b['brand_id'] === $brandId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['brand_title'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row g-2 mb-2">
              <div class="col-6">
                <label class="form-label">Min price</label>
                <input class="form-control" type="number" step="0.01" name="min_price" value="<?= htmlspecialchars($minPrice) ?>">
              </div>
              <div class="col-6">
                <label class="form-label">Max price</label>
                <input class="form-control" type="number" step="0.01" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>">
              </div>
            </div>

            <div class="mb-2">
              <label class="form-label">Sort</label>
              <select class="form-select" name="sort">
                <option value="newest" <?= $sort==='newest'?'selected':'' ?>>Newest</option>
                <option value="oldest" <?= $sort==='oldest'?'selected':'' ?>>Oldest</option>
                <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price: Low → High</option>
                <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High → Low</option>
                <option value="title_asc" <?= $sort==='title_asc'?'selected':'' ?>>Title: A → Z</option>
              </select>
            </div>

            <input type="hidden" name="per_page" value="<?= (int)($pagination['perPage'] ?? 9) ?>">
            <button class="btn btn-primary w-100" type="submit">Apply</button>

            <a class="btn btn-outline-secondary w-100 mt-2" href="<?= SITE_URL ?>/products">Reset</a>
          </form>
        </div>
      </div>
    </div>

    <!-- Products -->
    <div class="col-12 col-lg-9">
      <?php if (empty($products)): ?>
        <div class="alert alert-info">No products found.</div>
      <?php else: ?>
        <!-- Instant (client-side) controls – progressive enhancement (does NOT replace server filters) -->
        <div class="card mb-3 product-toolbar" data-products-toolbar>
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-end gap-2">
              <div class="flex-grow-1" style="min-width: 220px;">
                <label class="form-label mb-1">Quick search (in this page)</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-search" aria-hidden="true"></i></span>
                  <input class="form-control" type="search" placeholder="Type to filter…" data-products-q>
                </div>
              </div>

              <div style="min-width: 190px;">
                <label class="form-label mb-1">Sort (in this page)</label>
                <select class="form-select" data-products-sort>
                  <option value="relevance">Relevance</option>
                  <option value="price_asc">Price: Low → High</option>
                  <option value="price_desc">Price: High → Low</option>
                  <option value="title_asc">Title: A → Z</option>
                  <option value="title_desc">Title: Z → A</option>
                </select>
              </div>

              <div class="ms-auto text-muted" style="min-width: 160px;">
                <div class="small">Showing</div>
                <div class="fw-semibold"><span data-products-visible>0</span> / <span data-products-total>0</span></div>
              </div>
            </div>

            <div class="mt-3" data-products-price>
              <div class="d-flex align-items-center justify-content-between">
                <label class="form-label mb-1">Price range (in this page)</label>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-products-reset>Reset</button>
              </div>
              <div class="row g-2 align-items-center">
                <div class="col-12 col-md-8">
                  <div class="range-2">
                    <input class="form-range" type="range" data-price-min>
                    <input class="form-range" type="range" data-price-max>
                  </div>
                </div>
                <div class="col-6 col-md-2">
                  <input class="form-control" type="number" step="0.01" data-price-min-input>
                </div>
                <div class="col-6 col-md-2">
                  <input class="form-control" type="number" step="0.01" data-price-max-input>
                </div>
              </div>
              <div class="form-text">Tip: server-side filters (left) search the whole catalog; this bar only filters the current page.</div>
            </div>
          </div>
        </div>

        <div class="row g-3">
          <?php foreach (($products ?? []) as $p): ?>
            <div class="col-12 col-sm-6 col-xl-4">
              <div
                class="card product-card h-100"
                data-product-card
                data-product-id="<?= (int)($p['product_id'] ?? 0) ?>"
                data-product-title="<?= htmlspecialchars($p['product_title'] ?? '') ?>"
                data-product-price="<?= htmlspecialchars((string)($p['product_price'] ?? '0')) ?>"
                data-product-img="<?= SITE_URL ?>/uploads/<?= htmlspecialchars($p['product_image1'] ?? 'default.png') ?>"
                data-product-url="<?= SITE_URL ?>/product/<?= (int)($p['product_id'] ?? 0) ?>"
              >
                <img class="product-img img-skeleton" src="<?= SITE_URL ?>/uploads/<?= htmlspecialchars($p['product_image1'] ?? 'default.png') ?>" alt="">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($p['product_title'] ?? '') ?></h5>
                  <p class="card-text text-muted mb-2">$<?= htmlspecialchars($p['product_price'] ?? '0.00') ?></p>
                  <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-primary btn-sm" href="<?= SITE_URL ?>/product/<?= (int)$p['product_id'] ?>">Details</a>
                    <a class="btn btn-outline-primary btn-sm" href="<?= SITE_URL ?>/product/<?= (int)$p['product_id'] ?>" data-quick-view>
                      Quick view
                    </a>
                    <button class="btn btn-success btn-sm" type="button" data-add-to-cart data-product-id="<?= (int)$p['product_id'] ?>">
                      Add
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if (($pagination['pages'] ?? 1) > 1): ?>
          <?php
            $cur = (int)($pagination['page'] ?? 1);
            $pages = (int)($pagination['pages'] ?? 1);
            $start = max(1, $cur - 2);
            $end = min($pages, $cur + 2);
          ?>
          <nav class="mt-4" aria-label="Product pagination">
            <ul class="pagination justify-content-center">
              <li class="page-item <?= $cur<=1?'disabled':'' ?>">
                <a class="page-link" href="<?= SITE_URL ?>/products?<?= htmlspecialchars(build_query(['page'=>max(1,$cur-1)])) ?>">Prev</a>
              </li>

              <?php if ($start > 1): ?>
                <li class="page-item"><a class="page-link" href="<?= SITE_URL ?>/products?<?= htmlspecialchars(build_query(['page'=>1])) ?>">1</a></li>
                <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
              <?php endif; ?>

              <?php for ($i=$start; $i<=$end; $i++): ?>
                <li class="page-item <?= $i===$cur?'active':'' ?>">
                  <a class="page-link" href="<?= SITE_URL ?>/products?<?= htmlspecialchars(build_query(['page'=>$i])) ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($end < $pages): ?>
                <?php if ($end < $pages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                <li class="page-item"><a class="page-link" href="<?= SITE_URL ?>/products?<?= htmlspecialchars(build_query(['page'=>$pages])) ?>"><?= $pages ?></a></li>
              <?php endif; ?>

              <li class="page-item <?= $cur>=$pages?'disabled':'' ?>">
                <a class="page-link" href="<?= SITE_URL ?>/products?<?= htmlspecialchars(build_query(['page'=>min($pages,$cur+1)])) ?>">Next</a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
