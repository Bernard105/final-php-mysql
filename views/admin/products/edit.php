<?php
// views/admin/products/edit.php
$p = $product ?? [];
?>
<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Edit Product #<?= (int)($p['product_id'] ?? 0) ?></h1>
    <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/products"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
  </div>

  <form method="post" action="<?= SITE_URL ?>/admin/products/<?= (int)($p['product_id'] ?? 0) ?>" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-6">
      <label class="form-label">Product Title</label>
      <input type="text" name="product_title" class="form-control" value="<?= htmlspecialchars($p['product_title'] ?? '') ?>" required>
    </div>

    <div class="col-12 col-lg-6">
      <label class="form-label">Product Price</label>
      <input type="number" step="0.01" min="0" name="product_price" class="form-control" value="<?= htmlspecialchars($p['product_price'] ?? '') ?>" required>
    </div>

    <div class="col-12">
      <label class="form-label">Description</label>
      <textarea name="product_description" class="form-control" rows="4" required><?= htmlspecialchars($p['product_description'] ?? '') ?></textarea>
    </div>

    <div class="col-12">
      <label class="form-label">Keywords</label>
      <input type="text" name="product_keywords" class="form-control" value="<?= htmlspecialchars($p['product_keywords'] ?? '') ?>">
    </div>

    <div class="col-12 col-lg-6">
      <label class="form-label">Category</label>
      <select name="category_id" class="form-select" required>
        <option value="">Select a category</option>
        <?php foreach (($categories ?? []) as $c): ?>
          <?php $selected = ((int)($p['category_id'] ?? 0) === (int)$c['category_id']) ? 'selected' : ''; ?>
          <option value="<?= (int)$c['category_id'] ?>" <?= $selected ?>><?= htmlspecialchars($c['category_title'] ?? '') ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12 col-lg-6">
      <label class="form-label">Brand</label>
      <select name="brand_id" class="form-select" required>
        <option value="">Select a brand</option>
        <?php foreach (($brands ?? []) as $b): ?>
          <?php $selected = ((int)($p['brand_id'] ?? 0) === (int)$b['brand_id']) ? 'selected' : ''; ?>
          <option value="<?= (int)$b['brand_id'] ?>" <?= $selected ?>><?= htmlspecialchars($b['brand_title'] ?? '') ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12 col-lg-4">
      <label class="form-label">Replace Image 1 (optional)</label>
      <input type="file" name="product_image1" class="form-control" accept="image/*">
      <?php if (!empty($p['product_image1'])): ?>
        <div class="form-text">Current: <?= htmlspecialchars($p['product_image1']) ?></div>
      <?php endif; ?>
    </div>
    <div class="col-12 col-lg-4">
      <label class="form-label">Replace Image 2 (optional)</label>
      <input type="file" name="product_image2" class="form-control" accept="image/*">
      <?php if (!empty($p['product_image2'])): ?>
        <div class="form-text">Current: <?= htmlspecialchars($p['product_image2']) ?></div>
      <?php endif; ?>
    </div>
    <div class="col-12 col-lg-4">
      <label class="form-label">Replace Image 3 (optional)</label>
      <input type="file" name="product_image3" class="form-control" accept="image/*">
      <?php if (!empty($p['product_image3'])): ?>
        <div class="form-text">Current: <?= htmlspecialchars($p['product_image3']) ?></div>
      <?php endif; ?>
    </div>

    <div class="col-12 col-lg-4">
      <label class="form-label">Status</label>
      <?php $status = (int)($p['status'] ?? 1); ?>
      <select name="status" class="form-select">
        <option value="1" <?= $status===1?'selected':''; ?>>Active</option>
        <option value="0" <?= $status===0?'selected':''; ?>>Inactive</option>
      </select>
    </div>

    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Save Changes</button>
      <a href="<?= SITE_URL ?>/admin/products" class="btn btn-light">Cancel</a>
    </div>
  </form>
</div>
