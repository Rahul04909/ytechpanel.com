<?php
/**
 * YTech Panels - Admin Edit Client Page
 */
include './header.php';

$db = getDB();
$clientId = (int) ($_GET['id'] ?? 0);

if ($clientId <= 0) {
    header('Location: clients.php');
    exit();
}

$stmt = $db->prepare("SELECT * FROM clients WHERE id = :id");
$stmt->execute([':id' => $clientId]);
$client = $stmt->fetch();

if (!$client) {
    header('Location: clients.php');
    exit();
}

$logoSrc = '';
if (!empty($client['logo'])) {
    $logoSrc = strpos($client['logo'], 'data:') === 0 ? $client['logo'] : './uploads/client_logos/' . htmlspecialchars($client['logo']);
}
?>

<style>
    .form-card { background: #fff; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .form-card-header { background: #f1f1f1; border-bottom: 1px solid #e2e8f0; padding: 16px 24px; font-weight: 600; font-size: 15px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    .form-card-header i { color: #2271b1; }
    .form-card-body { padding: 24px; }
    .form-card .form-group label { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 6px; display: block; }
    .form-card .form-control { border: 1.5px solid #e2e8f0; border-radius: 4px; padding: 10px 14px; font-size: 14px; transition: border-color 0.2s; width: 100%; }
    .form-card .form-control:focus { border-color: #2271b1; box-shadow: 0 0 0 3px rgba(0, 58, 140, 0.08); outline: none; }
    .form-card textarea.form-control { min-height: 100px; resize: vertical; }
    .form-card select.form-control { appearance: auto; }
    .btn-primary-custom { background: #2271b1; color: #fff; border: none; padding: 10px 28px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
    .btn-primary-custom:hover { background: #135e96; }
    .btn-secondary-custom { background: #646970; color: #fff; border: none; padding: 10px 28px; font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.2s; }
    .btn-secondary-custom:hover { background: #475569; color: #fff; }
    .logo-upload-area { border: 2px dashed #e2e8f0; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; }
    .logo-upload-area:hover, .logo-upload-area.dragover { border-color: #2271b1; background: #f0f4ff; }
    .logo-upload-area i { font-size: 36px; color: #94a3b8; margin-bottom: 10px; display: block; }
    .logo-upload-area p { color: #646970; font-size: 13px; margin: 0; }
    .logo-preview { max-width: 200px; max-height: 120px; object-fit: contain; margin: 10px auto; display: none; border: 1px solid #e2e8f0; }
</style>

<div class="row">
    <div class="col-lg-8">
        <div class="form-card">
            <div class="form-card-header"><i class="fas fa-edit"></i> Edit Client: <?= htmlspecialchars($client['name']) ?></div>
            <div class="form-card-body">
                <form id="editClientForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_client">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Client Name *</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($client['name']) ?>" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort_order">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= (int) $client['sort_order'] ?>" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="website">Website URL</label>
                                <input type="url" class="form-control" id="website" name="website" value="<?= htmlspecialchars($client['website']) ?>" placeholder="https://example.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" <?= $client['status'] ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= !$client['status'] ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description (optional)</label>
                                <textarea class="form-control" id="description" name="description" placeholder="Brief description..."><?= htmlspecialchars($client['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Client Logo</label>
                                <?php if ($logoSrc): ?>
                                    <div style="margin-bottom:10px;">
                                        <img src="<?= $logoSrc ?>" alt="Current Logo" class="logo-preview" style="display:block;">
                                        <small style="color:#646970;">Current logo. Upload a new one to replace.</small>
                                    </div>
                                <?php endif; ?>
                                <div class="logo-upload-area" id="logoUploadArea">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Click or drag to replace logo<br><small>Allowed: JPG, PNG, GIF, WebP | Max: 5MB</small></p>
                                </div>
                                <input type="file" name="logo" id="logoInput" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;">
                                <img id="newLogoPreview" class="logo-preview" alt="New Logo Preview" style="display:none;">
                                <small id="logoFileName" style="display:block; margin-top:6px; color:#646970;"></small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn-primary-custom" id="submitBtn"><i class="fas fa-save"></i> Update Client</button>
                        <a href="clients.php" class="btn-secondary-custom" style="margin-left:10px;"><i class="fas fa-arrow-left"></i> Back to List</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var uploadArea = $('#logoUploadArea');
    var fileInput = $('#logoInput');
    var preview = $('#newLogoPreview');
    var fileName = $('#logoFileName');

    uploadArea.on('click', function() { fileInput.click(); });
    uploadArea.on('dragover', function(e) { e.preventDefault(); $(this).addClass('dragover'); });
    uploadArea.on('dragleave', function() { $(this).removeClass('dragover'); });
    uploadArea.on('drop', function(e) {
        e.preventDefault(); $(this).removeClass('dragover');
        if (e.originalEvent.dataTransfer.files.length) {
            fileInput[0].files = e.originalEvent.dataTransfer.files;
            fileInput.trigger('change');
        }
    });

    fileInput.on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) { Swal.fire({ icon: 'error', title: 'Too Large', text: 'Logo must be under 5MB.' }); fileInput.val(''); return; }
            var reader = new FileReader();
            reader.onload = function(ev) { preview.attr('src', ev.target.result).show(); };
            reader.readAsDataURL(file);
            fileName.text(file.name);
        }
    });

    $('#editClientForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        $.ajax({
            url: 'handlers/client-handler.php', type: 'POST',
            data: new FormData(this), processData: false, contentType: false, dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, timer: 2000, showConfirmButton: false })
                    .then(function() { window.location.href = 'clients.php'; });
                } else { Swal.fire({ icon: 'error', title: 'Error', text: res.message }); }
            },
            error: function() { Swal.fire({ icon: 'error', title: 'Error', text: 'Network error.' }); },
            complete: function() { btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Client'); }
        });
    });
});
</script>

<?php include './footer.php'; ?>
