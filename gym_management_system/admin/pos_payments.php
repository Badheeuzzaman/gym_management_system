<?php

$page_title = "Payment Approvals";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';


// =========================================
// Approve Payment
// =========================================

if (isset($_GET['approve'])) {

    $stmt = $pdo->prepare(
        "UPDATE pos_payments
        SET
            status = 'approved',
            approved_by = ?
        WHERE id = ?"
    );

    $stmt->execute([
        $_SESSION['user_id'],
        $_GET['approve']
    ]);

    header("Location: pos_payments.php");
    exit;
}


// =========================================
// Reject Payment
// =========================================

if (isset($_GET['reject'])) {

    $stmt = $pdo->prepare(
        "UPDATE pos_payments
        SET
            status = 'rejected'
        WHERE id = ?"
    );

    $stmt->execute([
        $_GET['reject']
    ]);

    header("Location: pos_payments.php");
    exit;
}


// =========================================
// Get Payment Approval List
// =========================================

$stmt = $pdo->query(
    "SELECT
        pp.*,
        s.invoice_no
    FROM pos_payments pp
    INNER JOIN store_sales s
        ON pp.sale_id = s.id
    ORDER BY pp.id DESC"
);

$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pendingPayments = array_filter(
    $payments,
    function ($payment) {
        return $payment['status'] === 'pending';
    }
);

$pendingCount = count($pendingPayments);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>POS Payment Approvals</h4>
        </div>
        <div class="topbar-actions">
            <span class="badge bg-warning text-dark">
                <?= $pendingCount ?> Pending
            </span>
             <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color:inherit;">
                    <div class="avatar" style="background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white;"><?= strtoupper(substr($_SESSION['username'] ?? 'A',0,1)) ?></div>
                    <div class="d-none d-md-block text-start" style="line-height:1.1;">
                        <div style="font-size:13px; font-weight:600;color:#2d3436;"><?= $_SESSION['full_name'] ?? 'Admin' ?></div>
                        <small style="font-size:11px; color:#636e72;">Administrator</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;min-width:220px;margin-top:8px;">
                    <li class="px-3 py-2">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="avatar" style="background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;"><?= strtoupper(substr($_SESSION['username'] ?? 'A',0,1)) ?></div>
                            <div><strong style="font-size:13px;"><?= $_SESSION['full_name'] ?? 'Admin' ?></strong><br><small style="color:#636e72;font-size:11px;"><?= $_SESSION['username'] ?? 'admin' ?> • Admin</small></div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2" style="width:18px;"></i> My Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2" style="width:18px;"></i> Settings</a></li>
                    <li><a class="dropdown-item" href="my_store_account.php"><i class="fas fa-store me-2" style="width:18px;"></i> My Store Account</a></li>
                    <li><a class="dropdown-item" href="form_fields.php"><i class="fas fa-wpforms me-2" style="width:18px;"></i> Form Builder</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2" style="width:18px;"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p) { ?>
                            <tr>
                                <td>
                                    <code>
                                        <?= $p['invoice_no'] ?>
                                    </code>
                                </td>
                                <td>
                                    <?= formatCurrency($p['amount']) ?>
                                </td>
                                <td>
                                    <?= $p['method'] ?>
                                </td>
                                <td>
                                    <?= getStatusBadge($p['status']) ?>
                                </td>
                                <td>
                                    <?= $p['created_at'] ?>
                                </td>
                                <td>
                                    <?php if ($p['status'] === 'pending') { ?>
                                        <div class="btn-group btn-group-sm">
                                            <a
                                                href="?approve=<?= $p['id'] ?>"
                                                class="btn btn-success"
                                            >
                                                <i class="fas fa-check"></i>
                                                Approve
                                            </a>
                                            <a
                                                href="?reject=<?= $p['id'] ?>"
                                                class="btn btn-danger"
                                            >
                                                <i class="fas fa-times"></i>
                                                Reject
                                            </a>
                                        </div>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($payments)) { ?>
                            <tr>
                                <td colspan="6" class="text-center p-4">
                                    No pending online payments. This page shows UPI/Card verifications.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
