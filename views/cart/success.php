<div class="container">
  <div class="alert alert-success">
    <h4 class="alert-heading">Order placed!</h4>
    <p>Your order ID is <strong>#<?= htmlspecialchars($orderId ?? '') ?></strong>.</p>
    <hr>
    <a class="btn btn-primary" href="<?= SITE_URL ?>/orders">View my orders</a>
    <a class="btn btn-outline-primary" href="<?= SITE_URL ?>/products">Continue shopping</a>
  </div>
</div>
