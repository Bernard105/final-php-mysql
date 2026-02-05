<div class="container" style="max-width:520px;">
  <h1 class="h3 mb-3">Login</h1>
  <form method="POST" action="<?= SITE_URL ?>/login">
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" name="email" type="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input class="form-control" name="password" type="password" required>
    </div>
    <button class="btn btn-primary" type="submit">Continue</button>
    <a class="btn btn-link" href="<?= SITE_URL ?>/register">Register</a>
  </form>
</div>
