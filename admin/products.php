<?php
/**
 * YTech Panels - Admin Products List Page
 */
include './header.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_product') {
    if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        $errorMsg = 'Invalid security token.';
    } else {
        $deleteId = (int) ($_POST['product_id'] ?? 0);
        if ($deleteId > 0) {
            try {
                $stmt = $db->prepare("SELECT featured_image, gallery_images, catalog_pdf, og_image FROM products WHERE id = :id");
                $stmt->execute([':id' => $deleteId]);
                $product = $stmt->fetch();
                if ($product) {
                    $uploadDir = dirname(__DIR__) . '/uploads/products/';
                    if ($product['featured_image'] && file_exists($uploadDir . 'featured/' . $product['featured_image'])) unlink($uploadDir . 'featured/' . $product['featured_image']);
                    if ($product['og_image'] && file_exists($uploadDir . 'og/' . $product['og_image'])) unlink($uploadDir . 'og/' . $product['og_image']);
                    if ($product['catalog_pdf'] && file_exists($uploadDir . 'catalogs/' . $product['catalog_pdf'])) unlink($uploadDir . 'catalogs/' . $product['catalog_pdf']);
                    
                    $gallery = json_decode($product['gallery_images'] ?: '[]', true);
                    if (is_array($gallery)) {
                        foreach ($gallery as $img) {
                            if ($img && file_exists($uploadDir . 'gallery/' . $img)) unlink($uploadDir . 'gallery/' . $img);
                        }
                    }

                    $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
                    $stmt->execute([':id' => $deleteId]);
                    $successMsg = 'Product deleted successfully.';
                }
            } catch (Exception $e) {
                $errorMsg = 'Failed to delete product.';
            }
        }
    }
}

$products = $db->query("SELECT id, title, featured_image, sort_order, status, created_at FROM products ORDER BY sort_order ASC, id DESC")->fetchAll();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<style>
    .admin-card { background: #fff; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .admin-header { background: #f1f1f1; border-bottom: 1px solid #e2e8f0; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
    .admin-header h5 { margin: 0; font-weight: 600; font-size: 15px; color: #1e293b; }
    .btn-add { background: #2271b1; color: #fff; border: none; padding: 8px 20px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; border-radius: 3px; }
    .btn-add:hover { background: #135e96; color: #fff; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #f1f1f1; border-bottom: 2px solid #e2e8f0; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #646970; text-transform: uppercase; text-align: left; }
    .data-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; vertical-align: middle; }
    .data-table tr:hover { background: #f1f1f1; }
    .img-thumb { width: 48px; height: 48px; object-fit: cover; border: 1px solid #e2e8f0; background: #fff; padding: 2px; border-radius: 4px; }
    .img-thumb.no-img { background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 20px; }
    .status-badge { display: inline-block; padding: 3px 10px; font-size: 12px; font-weight: 600; border-radius: 12px; }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    .action-btns { display: flex; gap: 6px; }
    .btn-edit { background: #2271b1; color: #fff; padding: 6px 12px; font-size: 12px; font-weight: 600; text-decoration: none; border-radius: 3px; }
    .btn-edit:hover { background: #135e96; color: #fff; }
    .btn-delete { background: #dc2626; color: #fff; border: none; padding: 6px 12px; font-size: 12px; font-weight: 600; cursor: pointer; border-radius: 3px; }
    .btn-delete:hover { background: #b91c1c; }
    .empty-state { text-align: center; padding: 60px 24px; color: #94a3b8; }
    .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }
    .search-box { display: flex; gap: 12px; align-items: center; }
    .search-box input { border: 1.5px solid #e2e8f0; border-radius: 4px; padding: 7px 14px; font-size: 13px; width: 260px; }
</style>

<?php if (!empty($successMsg)): ?>
    <div style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:12px 16px; margin-bottom:20px; border-radius:4px;"><i class="fas fa-check-circle"></i> <?= $successMsg ?></div>
<?php endif; ?>
<?php if (!empty($errorMsg)): ?>
    <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:12px 16px; margin-bottom:20px; border-radius:4px;"><i class="fas fa-exclamation-circle"></i> <?= $errorMsg ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-header">
        <h5><i class="fas fa-box" style="color:#2271b1;"></i> Products</h5>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterTable()">
            <a href="product-add.php" class="btn-add"><i class="fas fa-plus"></i> Add Product</a>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <p>No products found.</p>
            <a href="product-add.php" class="btn-add"><i class="fas fa-plus"></i> Add Product</a>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th style="width:60px;">Image</th>
                        <th>Title</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="width:140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr class="data-row">
                            <td>
                                <?php if (!empty($p['featured_image'])): ?>
                                    <img src="../uploads/products/featured/<?= htmlspecialchars($p['featured_image']) ?>" class="img-thumb">
                                <?php else: ?>
                                    <div class="img-thumb no-img"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                            <td><?= (int) $p['sort_order'] ?></td>
                            <td><span class="status-badge <?= $p['status'] ? 'status-active' : 'status-inactive' ?>"><?= $p['status'] ? 'Active' : 'Inactive' ?></span></td>
                            <td><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                                    <button class="btn-delete" onclick="deleteItem(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['title'])) ?>')"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function filterTable() {
    var query = document.getElementById('searchInput').value.toLowerCase();
    var rows = document.querySelectorAll('.data-row');
    rows.forEach(function(row) {
        var title = row.cells[1].textContent.toLowerCase();
        row.style.display = (title.indexOf(query) > -1) ? '' : 'none';
    });
}

function deleteItem(id, name) {
    Swal.fire({
        title: 'Delete Product?',
        html: 'Are you sure you want to delete <strong>' + name + '</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = '<input type="hidden" name="action" value="delete_product"><input type="hidden" name="product_id" value="' + id + '"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?php include './footer.php'; ?>