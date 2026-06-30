<?php include './header.php';

$db = getDB();

// ── Real-time stats ──
$totalProducts = $db->query("SELECT COUNT(*) FROM products WHERE status = 1")->fetchColumn();
$totalClients  = $db->query("SELECT COUNT(*) FROM clients WHERE status = 1")->fetchColumn();
$totalQuotes   = $db->query("SELECT COUNT(*) FROM quotes")->fetchColumn();
$totalEnquiries = $db->query("SELECT COUNT(*) FROM product_enquiries")->fetchColumn()
                + $db->query("SELECT COUNT(*) FROM general_enquiries")->fetchColumn();

// ── Recent quote requests (latest 8) ──
$recentQuotes = $db->query("SELECT * FROM quotes ORDER BY created_at DESC LIMIT 8")->fetchAll();
?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $totalProducts ?></h3>
                <p>Total Products</p>
            </div>
            <div class="icon">
                <i class="ion ion-cube"></i>
            </div>
            <a href="products.php" class="small-box-footer">More info
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $totalClients ?></h3>
                <p>Total Clients</p>
            </div>
            <div class="icon">
                <i class="ion ion-ios-people"></i>
            </div>
            <a href="clients.php" class="small-box-footer">More info
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $totalQuotes ?></h3>
                <p>Quote Requests</p>
            </div>
            <div class="icon">
                <i class="ion ion-document-text"></i>
            </div>
            <a href="quotes.php" class="small-box-footer">More info
                <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $totalEnquiries ?></h3>
                <p>Enquiries</p>
            </div>
            <div class="icon">
                <i class="ion ion-ios-email"></i>
            </div>
            <a href="enquiries.php" class="small-box-footer">More info
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Recent Quote Requests Table -->
<div class="card mt-4">
    <div class="card-header" style="background:#f1f1f1;border-bottom:1px solid #e2e8f0;padding:14px 20px;">
        <h5 style="margin:0;font-weight:600;font-size:15px;color:#1e293b;">
            <i class="fas fa-file-invoice-dollar" style="color:#2271b1;"></i> Recent Quote Requests
            <a href="quotes.php" class="btn btn-sm btn-outline-primary float-end">View All</a>
        </h5>
    </div>
    <?php if (empty($recentQuotes)): ?>
        <div class="text-center p-5 text-muted"><p>No quote requests yet.</p></div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table class="table table-hover mb-0" style="font-size:14px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#646970;text-transform:uppercase;border-bottom:2px solid #e2e8f0;">Date</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#646970;text-transform:uppercase;border-bottom:2px solid #e2e8f0;">Name</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#646970;text-transform:uppercase;border-bottom:2px solid #e2e8f0;">Email</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#646970;text-transform:uppercase;border-bottom:2px solid #e2e8f0;">Phone</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#646970;text-transform:uppercase;border-bottom:2px solid #e2e8f0;">Company</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#646970;text-transform:uppercase;border-bottom:2px solid #e2e8f0;">Product Interest</th>
                    <th style="padding:12px 16px;font-size:12px;font-weight:600;color:#646970;text-transform:uppercase;border-bottom:2px solid #e2e8f0;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentQuotes as $q): ?>
                <tr>
                    <td style="padding:12px 16px;white-space:nowrap;font-size:12px;color:#64748b;border-bottom:1px solid #f1f5f9;"><?= date('d M Y \a\t h:i A', strtotime($q['created_at'])) ?></td>
                    <td style="padding:12px 16px;border-bottom:1px solid #f1f5f9;"><strong><?= htmlspecialchars($q['name']) ?></strong></td>
                    <td style="padding:12px 16px;border-bottom:1px solid #f1f5f9;"><a href="mailto:<?= htmlspecialchars($q['email']) ?>" style="color:#2271b1;"><?= htmlspecialchars($q['email']) ?></a></td>
                    <td style="padding:12px 16px;border-bottom:1px solid #f1f5f9;"><?= !empty($q['phone']) ? htmlspecialchars($q['phone']) : '—' ?></td>
                    <td style="padding:12px 16px;border-bottom:1px solid #f1f5f9;"><?= !empty($q['company']) ? htmlspecialchars($q['company']) : '—' ?></td>
                    <td style="padding:12px 16px;border-bottom:1px solid #f1f5f9;"><?= !empty($q['product_interest']) ? htmlspecialchars($q['product_interest']) : '—' ?></td>
                    <td style="padding:12px 16px;border-bottom:1px solid #f1f5f9;">
                        <span style="display:inline-block;padding:3px 10px;font-size:11px;font-weight:600;border-radius:10px;<?php
                            if ($q['status'] == 0) echo 'background:#dbeafe;color:#1e40af;';
                            elseif ($q['status'] == 1) echo 'background:#fef3c7;color:#92400e;';
                            else echo 'background:#d1fae5;color:#065f46;';
                        ?>"><?= $q['status'] == 0 ? 'New' : ($q['status'] == 1 ? 'Contacted' : 'Quoted') ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include './footer.php'; ?>