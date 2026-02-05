<?php
  $m = $stats['status_map'] ?? [];
  $pending = (int)($m['pending'] ?? 0);
  $processing = (int)($m['processing'] ?? 0);
  $shipped = (int)($m['shipped'] ?? 0);
  $delivered = (int)($m['delivered'] ?? 0);
  $cancelled = (int)($m['cancelled'] ?? 0);
?>

<div class="card p-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 m-0">Dashboard</h1>
    <div class="text-muted small">Updated: <?= date('Y-m-d H:i') ?></div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-muted">Products</div>
          <div class="h3 mb-0"><?= (int)($stats['total_products'] ?? 0) ?></div>
          <a class="small" href="<?= SITE_URL ?>/admin/products">Manage</a>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-muted">Orders</div>
          <div class="h3 mb-0"><?= (int)($stats['total_orders'] ?? 0) ?></div>
          <a class="small" href="<?= SITE_URL ?>/admin/orders">Manage</a>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-muted">Users</div>
          <div class="h3 mb-0"><?= (int)($stats['total_users'] ?? 0) ?></div>
          <a class="small" href="<?= SITE_URL ?>/admin/users">Manage</a>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-muted">Revenue (total)</div>
          <div class="h3 mb-0">$<?= number_format((float)($stats['total_revenue'] ?? 0), 2) ?></div>
          <div class="small text-muted">Today: $<?= number_format((float)($stats['today_revenue'] ?? 0), 2) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-12 col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white"><strong>Order status</strong></div>
        <div class="card-body">
          <div class="d-flex justify-content-between"><span>pending</span><span class="fw-semibold"><?= $pending ?></span></div>
          <div class="d-flex justify-content-between"><span>processing</span><span class="fw-semibold"><?= $processing ?></span></div>
          <div class="d-flex justify-content-between"><span>shipped</span><span class="fw-semibold"><?= $shipped ?></span></div>
          <div class="d-flex justify-content-between"><span>delivered</span><span class="fw-semibold"><?= $delivered ?></span></div>
          <div class="d-flex justify-content-between"><span>cancelled</span><span class="fw-semibold"><?= $cancelled ?></span></div>
          <hr>
          <a class="btn btn-sm btn-outline-primary" href="<?= SITE_URL ?>/admin/orders">Go to Orders</a>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <strong>Recent orders</strong>
          <a class="small" href="<?= SITE_URL ?>/admin/orders">View all</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Created</th><th></th></tr></thead>
            <tbody>
              <?php foreach (($recent_orders ?? []) as $o): ?>
                <tr>
                  <td>#<?= (int)($o['order_id'] ?? 0) ?></td>
                  <td><?= htmlspecialchars($o['user_id'] ?? '') ?></td>
                  <td>$<?= number_format((float)($o['total_amount'] ?? 0), 2) ?></td>
                  <td><?= htmlspecialchars($o['order_status'] ?? '') ?></td>
                  <td><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
                  <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?= SITE_URL ?>/admin/orders/<?= (int)($o['order_id'] ?? 0) ?>">Open</a></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($recent_orders)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No orders yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <strong>Top products (qty sold)</strong>
          <a class="small" href="<?= SITE_URL ?>/admin/products">Products</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead><tr><th>Product</th><th class="text-end">Qty</th></tr></thead>
            <tbody>
              <?php foreach (($top_products ?? []) as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['product_title'] ?? ('Product #' . (int)($p['product_id'] ?? 0))) ?></td>
                  <td class="text-end"><?= (int)($p['qty_sold'] ?? 0) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($top_products)): ?>
                <tr><td colspan="2" class="text-center text-muted py-4">No sales data.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <strong>New users</strong>
          <a class="small" href="<?= SITE_URL ?>/admin/users">Users</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead><tr><th>User</th><th>Role</th><th>Created</th><th></th></tr></thead>
            <tbody>
              <?php foreach (($recent_users ?? []) as $u): ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= htmlspecialchars($u['username'] ?? '') ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($u['email'] ?? '') ?></div>
                  </td>
                  <td><?= htmlspecialchars($u['role'] ?? 'user') ?></td>
                  <td><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
                  <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?= SITE_URL ?>/admin/users/<?= (int)($u['id'] ?? 0) ?>">Open</a></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($recent_users)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">No users yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
