<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">User #<?= (int)($user['id'] ?? 0) ?></h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-info" href="<?= SITE_URL ?>/admin/users/<?= (int)($user['id'] ?? 0) ?>/orders"><i class="fa-solid fa-receipt me-1"></i>Orders</a>
      <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/users">Back</a>
    </div>
  </div>

  <form method="POST" action="<?= SITE_URL ?>/admin/users/<?= (int)($user['id'] ?? 0) ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label class="form-label">Username</label>
        <input class="form-control" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" name="email" type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Mobile</label>
        <input class="form-control" name="mobile" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Role</label>
        <select class="form-select" name="role">
          <?php $r = $user['role'] ?? 'user'; ?>
          <option value="user" <?= $r === 'user' ? 'selected' : '' ?>>user</option>
          <option value="admin" <?= $r === 'admin' ? 'selected' : '' ?>>admin</option>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Address</label>
        <textarea class="form-control" name="address" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Reset password (optional)</label>
        <input class="form-control" name="new_password" type="password" placeholder="Leave blank to keep current">
      </div>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Save</button>
      <a class="btn btn-outline-danger" href="<?= SITE_URL ?>/admin/users/<?= (int)($user['id'] ?? 0) ?>/delete"
         onclick="return confirm('Delete this user?')">Delete user</a>
    </div>
  </form>
</div>
