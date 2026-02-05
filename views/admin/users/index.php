<div class="card p-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 m-0">Users</h1>
    <form class="d-flex gap-2" method="GET" action="<?= SITE_URL ?>/admin/users">
      <input class="form-control" style="max-width:280px;" type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search name/email/mobile">
      <button class="btn btn-outline-secondary" type="submit">Search</button>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Role</th>
          <th>Created</th>
          <th style="width:220px;"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($users ?? []) as $u): ?>
          <tr>
            <td>#<?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['username'] ?? '') ?></td>
            <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($u['mobile'] ?? '') ?></td>
            <td>
              <span class="badge <?= (($u['role'] ?? 'user') === 'admin') ? 'text-bg-dark' : 'text-bg-secondary' ?>">
                <?= htmlspecialchars($u['role'] ?? 'user') ?>
              </span>
            </td>
            <td><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="<?= SITE_URL ?>/admin/users/<?= (int)$u['id'] ?>">View/Edit</a>
              <a class="btn btn-sm btn-outline-info" href="<?= SITE_URL ?>/admin/users/<?= (int)$u['id'] ?>/orders">Orders</a>
              <a class="btn btn-sm btn-outline-danger" href="<?= SITE_URL ?>/admin/users/<?= (int)$u['id'] ?>/delete"
                 onclick="return confirm('Delete this user?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
