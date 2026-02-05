<p>Hi <?= htmlspecialchars($username ?? 'User') ?>,</p>
<p>Click the link below to reset your password:</p>
<p><a href="<?= htmlspecialchars($reset_link ?? '#') ?>"><?= htmlspecialchars($reset_link ?? '#') ?></a></p>
<p>— <?= htmlspecialchars($site_name ?? SITE_NAME) ?></p>
