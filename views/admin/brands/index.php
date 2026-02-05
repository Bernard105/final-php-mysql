<?php
// views/admin/brands/index.php
?>
<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Brands</h1>
    <a class="btn btn-primary" href="<?= SITE_URL ?>/admin/brands/create"><i class="fa-solid fa-plus me-1"></i>Add</a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th style="width:120px">ID</th>
          <th>Brand</th>
          <th style="width:160px">Products</th>
          <th style="width:220px" class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($brands ?? []) as $b): ?>
          <tr>
            <td><?= (int)$b['brand_id'] ?></td>
            <td><?= htmlspecialchars($b['brand_title'] ?? '') ?></td>
            <td><?= (int)($b['product_count'] ?? 0) ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="<?= SITE_URL ?>/admin/brands/<?= (int)$b['brand_id'] ?>/edit"><i class="fa-solid fa-pen-to-square me-1"></i>Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?= SITE_URL ?>/admin/brands/<?= (int)$b['brand_id'] ?>/delete" onclick="return confirm('Delete this brand?')"><i class="fa-solid fa-trash me-1"></i>Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($brands)): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No brands yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
