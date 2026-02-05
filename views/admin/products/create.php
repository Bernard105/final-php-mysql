<?php
// views/admin/products/create.php
?>
<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Insert Product</h1>
    <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/products"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
  </div>

  <form method="post" action="<?= SITE_URL ?>/admin/products" enctype="multipart/form-data" class="row g-3">
    <div class="col-12 col-lg-6">
      <label class="form-label">Product Title</label>
      <input type="text" name="product_title" class="form-control" placeholder="Enter product title" required>
    </div>

    <div class="col-12 col-lg-6">
      <label class="form-label">Product Price</label>
      <input type="number" step="0.01" min="0" name="product_price" class="form-control" placeholder="Enter product price" required>
    </div>

    <div class="col-12">
      <label class="form-label">Description</label>
      <textarea name="product_description" class="form-control" rows="4" placeholder="Enter product description" required></textarea>
    </div>

    <div class="col-12">
      <label class="form-label">Keywords</label>
      <input type="text" name="product_keywords" class="form-control" placeholder="Enter keywords (comma separated)">
    </div>

    <div class="col-12 col-lg-6">
      <label class="form-label">Category</label>
      <select name="category_id" class="form-select" required>
        <option value="">Select a category</option>
        <?php foreach (($categories ?? []) as $c): ?>
          <option value="<?= (int)$c['category_id'] ?>"><?= htmlspecialchars($c['category_title'] ?? '') ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12 col-lg-6">
      <label class="form-label">Brand</label>
      <select name="brand_id" class="form-select" required>
        <option value="">Select a brand</option>
        <?php foreach (($brands ?? []) as $b): ?>
          <option value="<?= (int)$b['brand_id'] ?>"><?= htmlspecialchars($b['brand_title'] ?? '') ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12 col-lg-4">
      <label class="form-label">Product Image 1</label>
      <input type="file" name="product_image1" class="form-control" accept="image/*" required>
    </div>
    <div class="col-12 col-lg-4">
      <label class="form-label">Product Image 2</label>
      <input type="file" name="product_image2" class="form-control" accept="image/*" required>
    </div>
    <div class="col-12 col-lg-4">
      <label class="form-label">Product Image 3</label>
      <input type="file" name="product_image3" class="form-control" accept="image/*" required>
    </div>

    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Insert Product</button>
      <a href="<?= SITE_URL ?>/admin/products" class="btn btn-light">Cancel</a>
    </div>
  </form>
</div>
