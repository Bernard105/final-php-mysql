<div class="container" style="max-width:720px;">
  <h1 class="h3 mb-3">Profile</h1>
  <div class="card">
    <div class="card-body">
      <div class="mb-3"><strong>Username:</strong> <?= htmlspecialchars($user['username'] ?? '') ?></div>
      <div class="mb-3"><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></div>
      <form method="POST" action="<?= SITE_URL ?>/profile">
        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea class="form-control" name="address" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Mobile</label>
          <input class="form-control" name="mobile" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>">
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>
</div>
