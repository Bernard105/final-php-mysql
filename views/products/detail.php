<div class="container">
  <a class="btn btn-link px-0 mb-3" href="<?= SITE_URL ?>/products">&larr; Back to products</a>
  <div class="row g-4">
    <div class="col-md-5">
      <img class="img-fluid rounded" src="<?= SITE_URL ?>/uploads/<?= htmlspecialchars($product['product_image1'] ?? 'default.png') ?>" alt="">
    </div>
    <div class="col-md-7">
      <h1 class="h3"><?= htmlspecialchars($product['product_title'] ?? '') ?></h1>
      <p class="text-muted">$<?= htmlspecialchars($product['product_price'] ?? '0.00') ?></p>
      <p><?= nl2br(htmlspecialchars($product['product_description'] ?? '')) ?></p>
      <form method="POST" action="<?= SITE_URL ?>/cart/add/<?= (int)$product['product_id'] ?>">
        <button class="btn btn-success" type="submit"><i class="fas fa-cart-plus me-1"></i>Add to cart</button>
      </form>
    </div>
  </div>
</div>
