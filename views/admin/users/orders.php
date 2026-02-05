<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Orders of <?= htmlspecialchars($user['username'] ?? ('User #' . (int)($user['id'] ?? 0))) ?></h1>
    <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/users/<?= (int)($user['id'] ?? 0) ?>">Back</a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr><th>ID</th><th>Total</th><th>Status</th><th>Created</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach (($orders ?? []) as $o): ?>
          <tr>
            <td>#<?= (int)$o['order_id'] ?></td>
            <td>$<?= number_format((float)($o['total_amount'] ?? 0), 2) ?></td>
            <td><?= htmlspecialchars($o['order_status'] ?? '') ?></td>
            <td><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="<?= SITE_URL ?>/admin/orders/<?= (int)$o['order_id'] ?>">View</a>
              <a class="btn btn-sm btn-outline-secondary" href="<?= SITE_URL ?>/admin/orders/<?= (int)$o['order_id'] ?>/invoice">Invoice</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($orders)): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">No orders for this user.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
