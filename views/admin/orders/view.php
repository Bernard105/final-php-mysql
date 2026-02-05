<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Order #<?= (int)($order['order_id'] ?? 0) ?></h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/orders/<?= (int)($order['order_id'] ?? 0) ?>/invoice"><i class="fa-solid fa-file-invoice me-1"></i>Invoice</a>
      <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/orders">Back</a>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4"><div><strong>Status:</strong> <?= htmlspecialchars($order['order_status'] ?? '') ?></div></div>
    <div class="col-12 col-md-4"><div><strong>Payment:</strong> <?= htmlspecialchars($order['payment_status'] ?? '') ?></div></div>
    <div class="col-12 col-md-4"><div><strong>Total:</strong> $<?= number_format((float)($order['total_amount'] ?? 0), 2) ?></div></div>
    <div class="col-12"><div><strong>Shipping:</strong> <?= htmlspecialchars($order['shipping_address'] ?? '') ?></div></div>
  </div>

  <form method="POST" action="<?= SITE_URL ?>/admin/orders/<?= (int)($order['order_id'] ?? 0) ?>/status" class="mb-4">
    <label class="form-label">Update status</label>
    <select class="form-select" name="status" style="max-width:320px;">
      <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $st): ?>
        <option value="<?= $st ?>" <?= (($order['order_status'] ?? '') === $st) ? 'selected' : '' ?>><?= $st ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-primary mt-2" type="submit">Save</button>
  </form>

  <h5 class="mb-2">Items</h5>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>Product</th><th class="text-end">Price</th><th class="text-end">Qty</th><th class="text-end">Line total</th></tr></thead>
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
        <tr><th colspan="3" class="text-end">Subtotal</th><th class="text-end">$<?= number_format((float)$sum, 2) ?></th></tr>
      </tfoot>
    </table>
  </div>
</div>
