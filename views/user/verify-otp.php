<div class="container" style="max-width:520px;">
  <h1 class="h3 mb-3">Verify OTP</h1>
  <p class="text-muted">We sent a 6-digit code to your email.</p>
<?php if (defined('APP_ENV') && APP_ENV === 'development'): ?>
  <div class="alert alert-info py-2">
    <strong>DEV:</strong> OTP is <code>123456</code>
  </div>
<?php endif; ?>
  <form method="POST" action="<?= SITE_URL ?>/verify-otp">
    <div class="mb-3">
      <label class="form-label">OTP code</label>
      <input class="form-control" name="otp" inputmode="numeric" required>
    </div>
    <button class="btn btn-primary" type="submit">Verify</button>
  </form>
</div>
