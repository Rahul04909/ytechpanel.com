<?php
/**
 * YTech Panels — Admin Product Enquiries Management
 */
include './header.php';
$db = getDB();

$enquiries = $db->query("
    SELECT pe.*, p.title as product_title 
    FROM product_enquiries pe 
    LEFT JOIN products p ON pe.product_id = p.id 
    ORDER BY pe.created_at DESC
")->fetchAll();
?>
<style>
    .admin-card { background: #fff; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .admin-card-header { background: #f1f1f1; border-bottom: 1px solid #e2e8f0; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
    .admin-card-header h5 { margin: 0; font-weight: 600; font-size: 15px; color: #1e293b; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #f1f1f1; border-bottom: 2px solid #e2e8f0; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #646970; text-transform: uppercase; text-align: left; }
    .data-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; vertical-align: middle; }
    .data-table tr:hover { background: #f8fafc; }
    .badge-verified { display: inline-block; padding: 2px 8px; font-size: 10px; font-weight: 600; border-radius: 8px; background: #d1fae5; color: #065f46; }
    .badge-unverified { display: inline-block; padding: 2px 8px; font-size: 10px; font-weight: 600; border-radius: 8px; background: #fee2e2; color: #991b1b; }
</style>

<div class="admin-card">
    <div class="admin-card-header">
        <h5><i class="fas fa-box-open" style="color:#2271b1;"></i> Product Enquiries <span class="badge bg-primary ms-2"><?= count($enquiries) ?></span></h5>
    </div>
    <?php if (empty($enquiries)): ?>
        <div class="text-center p-5 text-muted"><p>No product enquiries yet.</p></div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Date</th><th>Product</th><th>Name</th><th>Email</th><th>Phone</th><th>Qty</th><th>Message</th><th>Verified</th></tr></thead>
            <tbody>
                <?php foreach ($enquiries as $e): ?>
                <tr>
                    <td style="white-space:nowrap;font-size:12px;color:#64748b;"><?= date('d M Y<br>h:i A', strtotime($e['created_at'])) ?></td>
                    <td><strong><?= htmlspecialchars($e['product_title'] ?? 'Unknown') ?></strong></td>
                    <td><?= htmlspecialchars($e['name']) ?></td>
                    <td><a href="mailto:<?= htmlspecialchars($e['email']) ?>" style="color:#2271b1;"><?= htmlspecialchars($e['email']) ?></a></td>
                    <td><?= !empty($e['phone']) ? htmlspecialchars($e['phone']) : '—' ?></td>
                    <td><?= !empty($e['quantity']) ? htmlspecialchars($e['quantity']) : '—' ?></td>
                    <td style="max-width:200px;font-size:13px;color:#64748b;"><?= htmlspecialchars(substr($e['message'] ?? '', 0, 100)) ?><?= !empty($e['message']) && strlen($e['message']) > 100 ? '...' : '' ?></td>
                    <td><span class="<?= $e['is_verified'] ? 'badge-verified' : 'badge-unverified' ?>"><?= $e['is_verified'] ? '✓ Verified' : '✗ Not' ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php include './footer.php'; ?>
