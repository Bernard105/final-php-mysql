<div class="container" style="max-width:520px;">
  <h1 class="h3 mb-3">Reset Password</h1>
  <p class="text-muted">Token: <?= htmlspecialchars($token ?? '') ?></p>
  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label">New password</label>
      <input class="form-control" type="password" name="password" required>
    </div>
    <button class="btn btn-primary" type="submit">Reset</button>
  </form>
</div>
