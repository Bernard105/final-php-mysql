<div class="container">
  <h1 class="h3 mb-3">My Orders</h1>
  <?php if (empty($orders)): ?>
    <div class="alert alert-info">You have no orders yet.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table">
        <thead><tr><th>ID</th><th>Total</th><th>Status</th><th>Created</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td>#<?= (int)$o['order_id'] ?></td>
              <td>$<?= number_format((float)$o['total_amount'], 2) ?></td>
              <td><?= htmlspecialchars($o['order_status'] ?? '') ?></td>
              <td><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="<?= SITE_URL ?>/orders/<?= (int)$o['order_id'] ?>">View</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
