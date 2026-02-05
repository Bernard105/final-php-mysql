<div class="container">
  <h1 class="h3 mb-3">Shopping Cart</h1>
  <?php if (empty($cartItems)): ?>
    <div class="alert alert-info">Your cart is empty.</div>
    <a class="btn btn-primary" href="<?= SITE_URL ?>/products">Continue shopping</a>
  <?php else: ?>
    <form method="POST" action="<?= SITE_URL ?>/cart/update" id="cartForm">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Product</th><th>Price</th><th style="width:120px;">Qty</th><th>Total</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item): 
              $price = (float)($item['product_price'] ?? 0);
              $qty   = (int)($item['quantity'] ?? 0);
              $line  = $price * $qty;
            ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <img class="cart-img rounded" src="<?= SITE_URL ?>/uploads/<?= htmlspecialchars($item['product_image1'] ?? 'default.png') ?>" alt="">
                    <div>
                      <div class="fw-semibold"><?= htmlspecialchars($item['product_title'] ?? '') ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  $<span class="js-unit-price" data-unit-price="<?= htmlspecialchars(number_format($price, 2, '.', '')) ?>"><?= number_format($price, 2) ?></span>
                </td>
                <td>
                  <input
                    class="form-control js-qty"
                    type="number"
                    min="0"
                    name="quantity[<?= (int)$item['product_id'] ?>]"
                    value="<?= $qty ?>"
                    data-product-id="<?= (int)$item['product_id'] ?>"
                  >
                </td>
                <td>$<span class="js-line-total"><?= number_format($line, 2) ?></span></td>
                <td><a class="btn btn-outline-danger btn-sm" href="<?= SITE_URL ?>/cart/remove/<?= (int)$item['product_id'] ?>">Remove</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <button class="btn btn-outline-primary" type="submit" name="action" value="update">Update cart</button>
        <div class="text-end">
          <div class="h5 mb-2">Total: $<span class="js-cart-total"><?= number_format((float)$total, 2) ?></span></div>
          <button class="btn btn-success" type="submit" name="action" value="checkout">Checkout</button>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>
