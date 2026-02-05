<div class="container">
  <h1 class="h3 mb-3">Order #<?= (int)($order['order_id'] ?? 0) ?></h1>
  <div class="mb-3">
    <div><strong>Status:</strong> <?= htmlspecialchars($order['order_status'] ?? '') ?></div>
    <div><strong>Payment:</strong> <?= htmlspecialchars($order['payment_status'] ?? '') ?> (<?= htmlspecialchars($order['payment_method'] ?? '') ?>)</div>
    <div><strong>Shipping address:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'] ?? '')) ?></div>
  </div>
  <h5>Items</h5>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach (($order['items'] ?? []) as $it): ?>
          <tr>
            <td><?= htmlspecialchars($it['product_title'] ?? ('#'.$it['product_id'])) ?></td>
            <td>$<?= number_format((float)$it['price'], 2) ?></td>
            <td><?= (int)$it['quantity'] ?></td>
            <td>$<?= number_format(((float)$it['price']) * ((int)$it['quantity']), 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="h5 text-end">Total: $<?= number_format((float)($order['total_amount'] ?? 0), 2) ?></div>
</div>
