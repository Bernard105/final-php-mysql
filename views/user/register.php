<div class="container" style="max-width:720px;">
  <h1 class="h3 mb-3">Register</h1>
  <form method="POST" action="<?= SITE_URL ?>/register">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Username</label>
        <input class="form-control" name="username" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" name="email" type="email" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Password</label>
        <input class="form-control" name="password" type="password" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Confirm password</label>
        <input class="form-control" name="password_confirmation" type="password" required>
      </div>
      <div class="col-12">
        <label class="form-label">Address</label>
        <textarea class="form-control" name="address" rows="2" required></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Mobile</label>
        <input class="form-control" name="mobile" required>
      </div>
    </div>
    <button class="btn btn-primary mt-3" type="submit">Create account</button>
    <a class="btn btn-link mt-3" href="<?= SITE_URL ?>/login">Login</a>
  </form>
</div>
