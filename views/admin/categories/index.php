<?php
// views/admin/categories/index.php
?>
<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Categories</h1>
    <a class="btn btn-primary" href="<?= SITE_URL ?>/admin/categories/create"><i class="fa-solid fa-plus me-1"></i>Add</a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th style="width:120px">ID</th>
          <th>Category</th>
          <th style="width:160px">Products</th>
          <th style="width:220px" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($categories ?? []) as $c): ?>
          <tr>
            <td><?= (int)$c['category_id'] ?></td>
            <td><?= htmlspecialchars($c['category_title'] ?? '') ?></td>
            <td><?= (int)($c['product_count'] ?? 0) ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="<?= SITE_URL ?>/admin/categories/<?= (int)$c['category_id'] ?>/edit"><i class="fa-solid fa-pen-to-square me-1"></i>Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?= SITE_URL ?>/admin/categories/<?= (int)$c['category_id'] ?>/delete" onclick="return confirm('Delete this category?')"><i class="fa-solid fa-trash me-1"></i>Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($categories)): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No categories yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
