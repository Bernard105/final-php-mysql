<div class="container" style="max-width:520px;">
  <h1 class="h3 mb-3">Forgot Password</h1>
  <p class="text-muted">This demo does not include a real reset flow.</p>
  <form method="POST" action="<?= SITE_URL ?>/forgot-password">
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" required>
    </div>
    <button class="btn btn-primary" type="submit">Submit</button>
  </form>
</div>
