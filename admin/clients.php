<?php
/**
 * YTech Panels - Admin Clients List Page
 */
include './header.php';

$db = getDB();

// Handle delete via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_client') {
    // CSRF validation for delete
    if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        $errorMsg = 'Invalid security token.';
    } else {
        $deleteId = (int) ($_POST['client_id'] ?? 0);
        if ($deleteId > 0) {
            try {
                $stmt = $db->prepare("SELECT logo FROM clients WHERE id = :id");
                $stmt->execute([':id' => $deleteId]);
                $client = $stmt->fetch();
                if ($client) {
                    if (!empty($client['logo']) && strpos($client['logo'], 'data:') !== 0) {
                        $logoPath = __DIR__ . '/uploads/client_logos/' . $client['logo'];
                        if (file_exists($logoPath)) {
                            unlink($logoPath);
                        }
                    }
                    $stmt = $db->prepare("DELETE FROM clients WHERE id = :id");
                    $stmt->execute([':id' => $deleteId]);
                    $successMsg = 'Client deleted successfully.';
                }
            } catch (Exception $e) {
                $errorMsg = 'Failed to delete client.';
            }
        }
    }
}

// Fetch all clients
$clients = $db->query("SELECT * FROM clients ORDER BY sort_order ASC, id ASC")->fetchAll();

// Generate CSRF token for delete forms
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<style>
    .clients-admin-card { background: #fff; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .clients-admin-header { background: #f1f1f1; border-bottom: 1px solid #e2e8f0; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
    .clients-admin-header h5 { margin: 0; font-weight: 600; font-size: 15px; color: #1e293b; }
    .btn-add-client { background: #2271b1; color: #fff; border: none; padding: 8px 20px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
    .btn-add-client:hover { background: #135e96; color: #fff; }
    .clients-table { width: 100%; border-collapse: collapse; }
    .clients-table th { background: #f1f1f1; border-bottom: 2px solid #e2e8f0; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #646970; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
    .clients-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; vertical-align: middle; }
    .clients-table tr:hover { background: #f1f1f1; }
    .client-logo-cell { width: 60px; }
    .client-logo-thumb { width: 48px; height: 48px; object-fit: contain; border: 1px solid #e2e8f0; background: #fff; padding: 4px; }
    .client-logo-thumb.no-logo { background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 20px; }
    .status-badge { display: inline-block; padding: 3px 10px; font-size: 12px; font-weight: 600; border-radius: 12px; }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    .action-btns { display: flex; gap: 6px; }
    .btn-edit { background: #2271b1; color: #fff; border: none; padding: 6px 12px; font-size: 12px; font-weight: 600; text-decoration: none; transition: background 0.2s; }
    .btn-edit:hover { background: #135e96; color: #fff; }
    .btn-delete { background: #dc2626; color: #fff; border: none; padding: 6px 12px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-delete:hover { background: #b91c1c; }
    .empty-state { text-align: center; padding: 60px 24px; color: #94a3b8; }
    .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }
    .empty-state p { font-size: 14px; margin-bottom: 20px; }
    .search-box { display: flex; gap: 12px; align-items: center; }
    .search-box input { border: 1.5px solid #e2e8f0; border-radius: 4px; padding: 7px 14px; font-size: 13px; width: 260px; }
    .search-box input:focus { border-color: #2271b1; outline: none; }
    .count-badge { background: #2271b1; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; margin-left: 8px; }
</style>

<?php if (!empty($successMsg)): ?>
    <div style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:12px 16px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-check-circle"></i> <?= $successMsg ?>
    </div>
<?php endif; ?>
<?php if (!empty($errorMsg)): ?>
    <div style="background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:12px 16px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-exclamation-circle"></i> <?= $errorMsg ?>
    </div>
<?php endif; ?>

<div class="clients-admin-card">
    <div class="clients-admin-header">
        <h5>
            <i class="fas fa-building" style="color:#2271b1;"></i>
            Clients
            <span class="count-badge"><?= count($clients) ?></span>
        </h5>
        <div class="search-box">
            <input type="text" id="clientSearch" placeholder="Search clients..." onkeyup="filterClients()">
            <a href="client-add.php" class="btn-add-client"><i class="fas fa-plus"></i> Add Client</a>
        </div>
    </div>

    <?php if (empty($clients)): ?>
        <div class="empty-state">
            <i class="fas fa-building"></i>
            <p>No clients found. Add your first client to get started.</p>
            <a href="client-add.php" class="btn-add-client"><i class="fas fa-plus"></i> Add Client</a>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="clients-table" id="clientsTable">
                <thead>
                    <tr>
                        <th style="width:60px;">Logo</th>
                        <th>Name</th>
                        <th>Website</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr class="client-row">
                            <td class="client-logo-cell">
                                <?php if (!empty($client['logo'])): ?>
                                    <img src="<?= strpos($client['logo'], 'data:') === 0 ? htmlspecialchars($client['logo']) : './uploads/client_logos/' . htmlspecialchars($client['logo']) ?>" alt="<?= htmlspecialchars($client['name']) ?>" class="client-logo-thumb">
                                <?php else: ?>
                                    <div class="client-logo-thumb no-logo"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($client['name']) ?></strong></td>
                            <td>
                                <?php if (!empty($client['website'])): ?>
                                    <a href="<?= htmlspecialchars($client['website']) ?>" target="_blank" style="color:#2271b1; text-decoration:none;"><?= htmlspecialchars($client['website']) ?> <i class="fas fa-external-link-alt" style="font-size:10px;"></i></a>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= (int) $client['sort_order'] ?></td>
                            <td>
                                <span class="status-badge <?= $client['status'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $client['status'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td><?= date('d M Y', strtotime($client['created_at'])) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="client-edit.php?id=<?= (int) $client['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                    <button class="btn-delete" onclick="deleteClient(<?= (int) $client['id'] ?>, '<?= htmlspecialchars(addslashes($client['name'])) ?>')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
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
var csrfToken = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>';

function filterClients() {
    var query = document.getElementById('clientSearch').value.toLowerCase();
    var rows = document.querySelectorAll('.client-row');
    rows.forEach(function(row) {
        var name = row.cells[1].textContent.toLowerCase();
        var website = row.cells[2].textContent.toLowerCase();
        row.style.display = (name.indexOf(query) > -1 || website.indexOf(query) > -1) ? '' : 'none';
    });
}

function deleteClient(id, name) {
    Swal.fire({
        title: 'Delete Client?',
        html: 'Are you sure you want to delete <strong>' + name + '</strong>?<br><small style="color:#dc2626;">This action cannot be undone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#646970',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = '<input type="hidden" name="action" value="delete_client"><input type="hidden" name="client_id" value="' + id + '"><input type="hidden" name="csrf_token" value="' + csrfToken + '">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?php include './footer.php'; ?>
