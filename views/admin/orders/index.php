<div class="card p-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 m-0">Orders</h1>
    <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/orders/export"><i class="fa-solid fa-file-export me-1"></i>Export CSV</a>
  </div>

  <form class="row g-2 align-items-end mb-3" method="GET" action="<?= SITE_URL ?>/admin/orders">
    <div class="col-12 col-md-3">
      <label class="form-label">Status</label>
      <?php $st = $status ?? 'all'; ?>
      <select class="form-select" name="status">
        <?php foreach (['all','pending','processing','shipped','delivered','cancelled'] as $s): ?>
          <option value="<?= $s ?>" <?= ($st === $s) ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Search (order id)</label>
      <input class="form-control" type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="e.g. 12">
    </div>
    <div class="col-12 col-md-2">
      <button class="btn btn-outline-primary" type="submit">Filter</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Created</th><th style="width:220px;"></th></tr></thead>
      <tbody>
        <?php foreach (($orders ?? []) as $o): ?>
          <tr>
            <td>#<?= (int)$o['order_id'] ?></td>
            <td><?= htmlspecialchars($o['user_id'] ?? '') ?></td>
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
          <tr><td colspan="6" class="text-center text-muted py-4">No orders.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
