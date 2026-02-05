<div class="container">
  <h1 class="h3 mb-3">Checkout</h1>
  <div class="row g-4">
    <div class="col-lg-7">
      <form method="POST" action="<?= SITE_URL ?>/checkout">
        <div class="mb-3">
          <label class="form-label">Shipping address</label>
          <textarea class="form-control" name="shipping_address" rows="3"><?= htmlspecialchars($_SESSION['user']['address'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Payment method</label>
          <select class="form-select" name="payment_method">
            <option value="cod">Cash on delivery</option>
            <option value="bank">Bank transfer</option>
          </select>
        </div>
        <button class="btn btn-primary" type="submit">Place order</button>
      </form>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Order summary</h5>
          <ul class="list-group list-group-flush mb-3">
            <?php foreach (($cartItems ?? []) as $item): ?>
              <li class="list-group-item d-flex justify-content-between">
                <span><?= htmlspecialchars($item['product_title'] ?? '') ?> × <?= (int)$item['quantity'] ?></span>
                <span>$<?= number_format(((float)$item['product_price']) * ((int)$item['quantity']), 2) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="d-flex justify-content-between fw-bold">
            <span>Total</span>
            <span>$<?= number_format((float)$total, 2) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
