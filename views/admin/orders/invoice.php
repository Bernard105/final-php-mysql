<div class="card p-4">
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h1 class="h4 mb-1">Invoice #<?= (int)($order['order_id'] ?? 0) ?></h1>
      <div class="text-muted">Created: <?= htmlspecialchars($order['created_at'] ?? '') ?></div>
      <div class="text-muted">Status: <?= htmlspecialchars($order['order_status'] ?? '') ?> | Payment: <?= htmlspecialchars($order['payment_status'] ?? '') ?></div>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print me-1"></i>Print</button>
      <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/orders/<?= (int)($order['order_id'] ?? 0) ?>">Back</a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
      <div class="p-3 bg-light rounded">
        <div class="fw-semibold mb-1">Billed To</div>
        <div>User ID: <?= htmlspecialchars($order['user_id'] ?? '') ?></div>
        <div class="text-muted small">(You can extend this to show user name/address.)</div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="p-3 bg-light rounded">
        <div class="fw-semibold mb-1">Ship To</div>
        <div><?= nl2br(htmlspecialchars($order['shipping_address'] ?? '')) ?></div>
      </div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead>
        <tr><th>Product</th><th class="text-end">Price</th><th class="text-end">Qty</th><th class="text-end">Line total</th></tr>
      </thead>
      <tbody>
        <?php $sum = 0; foreach (($order['items'] ?? []) as $it): $line = ((float)$it['price']) * ((int)$it['quantity']); $sum += $line; ?>
          <tr>
            <td><?= htmlspecialchars($it['product_title'] ?? ('#'.$it['product_id'])) ?></td>
            <td class="text-end">$<?= number_format((float)$it['price'], 2) ?></td>
            <td class="text-end"><?= (int)$it['quantity'] ?></td>
            <td class="text-end">$<?= number_format((float)$line, 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><th colspan="3" class="text-end">Total</th><th class="text-end">$<?= number_format((float)($order['total_amount'] ?? $sum), 2) ?></th></tr>
      </tfoot>
    </table>
  </div>
</div>

<style>
@media print {
  nav, aside, .btn { display:none !important; }
  body { padding-top: 0 !important; background: #fff !important; }
  .card { box-shadow:none !important; }
}
</style>
