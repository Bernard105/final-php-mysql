<div class="card p-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 m-0">Products</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-primary" href="<?= SITE_URL ?>/admin/products/create"><i class="fa-solid fa-plus me-1"></i>Add</a>
      <a class="btn btn-outline-secondary" href="<?= SITE_URL ?>/admin/products/export"><i class="fa-solid fa-file-export me-1"></i>Export CSV</a>
    </div>
  </div>

  <form class="row g-2 align-items-end mb-3" method="POST" action="<?= SITE_URL ?>/admin/products/import" enctype="multipart/form-data">
    <div class="col-12 col-md-6">
      <label class="form-label">Import CSV</label>
      <input class="form-control" type="file" name="csv_file" accept=".csv" required>
      <div class="form-text">
        CSV columns: <code>product_title, product_description, product_keywords, category_id, brand_id, product_price, status</code>.
      </div>
    </div>
    <div class="col-12 col-md-3">
      <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-file-import me-1"></i>Import</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Price</th>
          <th>Status</th>
          <th style="width:180px;"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($products ?? []) as $p): ?>
          <tr>
            <td>#<?= (int)$p['product_id'] ?></td>
            <td><?= htmlspecialchars($p['product_title'] ?? '') ?></td>
            <td><?= htmlspecialchars($p['category_title'] ?? '') ?></td>
            <td>$<?= number_format((float)($p['product_price'] ?? 0), 2) ?></td>
            <td>
              <?php $st = (int)($p['status'] ?? 0); ?>
              <span class="badge <?= $st ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $st ? 'Active' : 'Inactive' ?></span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="<?= SITE_URL ?>/admin/products/<?= (int)$p['product_id'] ?>/edit">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?= SITE_URL ?>/admin/products/<?= (int)$p['product_id'] ?>/delete"
                 onclick="return confirm('Delete this product?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">No products yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
