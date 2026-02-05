<p>Hi <?= htmlspecialchars($username ?? 'User') ?>,</p>
<p>Your OTP code is: <strong><?= htmlspecialchars($otp ?? '') ?></strong></p>
<p>This code expires in 5 minutes.</p>
<p>— <?= htmlspecialchars($site_name ?? SITE_NAME) ?></p>
