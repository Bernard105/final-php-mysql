<?php
// views/admin/categories/create.php
?>
<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Add Category</h1>
    <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/categories"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
  </div>

  <form method="post" action="<?= SITE_URL ?>/admin/categories" class="row g-3" style="max-width:720px;">
    <div class="col-12">
      <label class="form-label">Category Title</label>
      <input type="text" name="category_title" class="form-control" placeholder="Enter category title" required>
    </div>

    <div class="col-12">
      <label class="form-label">Description (optional)</label>
      <textarea name="description" class="form-control" rows="3" placeholder="Short description"></textarea>
    </div>

    <div class="col-12 form-check">
      <input class="form-check-input" type="checkbox" name="status" id="cat_status" checked>
      <label class="form-check-label" for="cat_status">Active</label>
    </div>

    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Save</button>
      <a href="<?= SITE_URL ?>/admin/categories" class="btn btn-light">Cancel</a>
    </div>
  </form>
</div>
