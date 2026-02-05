<?php
// views/admin/categories/edit.php
$c = $category ?? [];
?>
<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Edit Category #<?= (int)($c['category_id'] ?? 0) ?></h1>
    <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/categories"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
  </div>

  <form method="post" action="<?= SITE_URL ?>/admin/categories/<?= (int)($c['category_id'] ?? 0) ?>" class="row g-3" style="max-width:720px;">
    <div class="col-12">
      <label class="form-label">Category Title</label>
      <input type="text" name="category_title" class="form-control" value="<?= htmlspecialchars($c['category_title'] ?? '') ?>" required>
    </div>

    <div class="col-12">
      <label class="form-label">Description (optional)</label>
      <textarea name="description" class="form-control" rows="3" placeholder="Short description"><?= htmlspecialchars($c['description'] ?? '') ?></textarea>
    </div>

    <?php $status = (int)($c['status'] ?? 1); ?>
    <div class="col-12 form-check">
      <input class="form-check-input" type="checkbox" name="status" id="cat_status" <?= $status===1?'checked':''; ?>>
      <label class="form-check-label" for="cat_status">Active</label>
    </div>

    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Save Changes</button>
      <a href="<?= SITE_URL ?>/admin/categories" class="btn btn-light">Cancel</a>
    </div>
  </form>
</div>
