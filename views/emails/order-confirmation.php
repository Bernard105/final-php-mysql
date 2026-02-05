<p>Hi <?= htmlspecialchars($username ?? 'User') ?>,</p>
<p>Thanks for your order. Your order ID is <strong>#<?= htmlspecialchars($order['order_id'] ?? '') ?></strong>.</p>
<p>Total: <strong>$<?= htmlspecialchars($order['total_amount'] ?? '') ?></strong></p>
<p>— <?= htmlspecialchars($site_name ?? SITE_NAME) ?></p>
