<?php
/**
 * YTech Panels — Admin General Enquiries Management
 */
include './header.php';
$db = getDB();

// Status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_enquiry_status') {
    $id = (int)($_POST['id'] ?? 0);
    $status = (int)($_POST['status'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE general_enquiries SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $successMsg = 'Enquiry status updated.';
    }
}

$enquiries = $db->query("SELECT * FROM general_enquiries ORDER BY created_at DESC")->fetchAll();
?>
<style>
    .admin-card { background: #fff; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .admin-card-header { background: #f1f1f1; border-bottom: 1px solid #e2e8f0; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
    .admin-card-header h5 { margin: 0; font-weight: 600; font-size: 15px; color: #1e293b; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #f1f1f1; border-bottom: 2px solid #e2e8f0; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #646970; text-transform: uppercase; text-align: left; }
    .data-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; vertical-align: middle; }
    .data-table tr:hover { background: #f8fafc; }
    .badge-status { display: inline-block; padding: 3px 10px; font-size: 11px; font-weight: 600; border-radius: 10px; }
    .badge-new { background: #dbeafe; color: #1e40af; }
    .badge-read { background: #fef3c7; color: #92400e; }
    .badge-replied { background: #d1fae5; color: #065f46; }
    .status-select { padding: 4px 8px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 3px; }
</style>

<?php if (!empty($successMsg)): ?><div class="alert alert-success"><?= $successMsg ?></div><?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h5><i class="fas fa-envelope" style="color:#2271b1;"></i> General Enquiries <span class="badge bg-primary ms-2"><?= count($enquiries) ?></span></h5>
    </div>
    <?php if (empty($enquiries)): ?>
        <div class="text-center p-5 text-muted"><p>No enquiries yet.</p></div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Message</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($enquiries as $e): ?>
                <tr>
                    <td style="white-space:nowrap;font-size:12px;color:#64748b;"><?= date('d M Y<br>h:i A', strtotime($e['created_at'])) ?></td>
                    <td><strong><?= htmlspecialchars($e['name']) ?></strong></td>
                    <td><a href="mailto:<?= htmlspecialchars($e['email']) ?>" style="color:#2271b1;"><?= htmlspecialchars($e['email']) ?></a></td>
                    <td><?= !empty($e['phone']) ? htmlspecialchars($e['phone']) : '—' ?></td>
                    <td><?= !empty($e['subject']) ? htmlspecialchars($e['subject']) : '—' ?></td>
                    <td style="max-width:200px;font-size:13px;color:#64748b;"><?= htmlspecialchars(substr($e['message'], 0, 100)) ?><?= strlen($e['message']) > 100 ? '...' : '' ?></td>
                    <td>
                        <span class="badge-status <?= $e['status'] == 0 ? 'badge-new' : ($e['status'] == 1 ? 'badge-read' : 'badge-replied') ?>">
                            <?= $e['status'] == 0 ? 'New' : ($e['status'] == 1 ? 'Read' : 'Replied') ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" style="display:flex;gap:4px;">
                            <input type="hidden" name="action" value="update_enquiry_status">
                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                            <select name="status" class="status-select">
                                <option value="0" <?= $e['status'] == 0 ? 'selected' : '' ?>>New</option>
                                <option value="1" <?= $e['status'] == 1 ? 'selected' : '' ?>>Read</option>
                                <option value="2" <?= $e['status'] == 2 ? 'selected' : '' ?>>Replied</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-check"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php include './footer.php'; ?>
