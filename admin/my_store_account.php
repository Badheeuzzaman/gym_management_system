<?php

$page_title = "My Store Account";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';


// =========================================
// Current Staff Account
// =========================================

// Replace this with the logged-in staff ID from the session
$staffId = 1;
// Example:
// $staffId = $_SESSION['staff_id'];


// =========================================
// Get Staff Details
// =========================================

$stmt = $pdo->prepare(
    "SELECT *
    FROM staff
    WHERE id = ?"
);

$stmt->execute([
    $staffId
]);

$staff = $stmt->fetch(PDO::FETCH_ASSOC);


// =========================================
// Today's Sales Count
// =========================================

$stmt = $pdo->query(
    "SELECT COUNT(*)
    FROM store_sales
    WHERE DATE(sale_date) = CURDATE()"
);

$sales = $stmt->fetchColumn();


// =========================================
// Today's Earnings
// =========================================

$stmt = $pdo->query(
    "SELECT SUM(final_total)
    FROM store_sales
    WHERE DATE(sale_date) = CURDATE()"
);

$earnings = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query(
    "SELECT *
    FROM store_sales
    ORDER BY id DESC
    LIMIT 10"
);

$recentSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>My Store Account</h4>
        </div>
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
    <div class="content-wrapper">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card-modern p-4 text-center">
                    <div
                        class="avatar mx-auto mb-3"
                        style="width:80px;height:80px;font-size:28px;"
                    >
                        <?= strtoupper(substr($staff['name'] ?? 'S', 0, 1)) ?>
                    </div>
                    <h5>
                        <?= $staff['name'] ?? 'Store Staff' ?>
                    </h5>
                    <small style="color:#636e72;">
                        <?= $staff['role'] ?? 'Sales Executive' ?>
                    </small>
                    <div class="mt-4 row g-2">
                        <div class="col-6">
                            <div
                                class="p-2"
                                style="background:#f8f9fc;border-radius:10px;"
                            >
                                <h5>
                                    <?= $sales ?>
                                </h5>
                                <small>Today Sales</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div
                                class="p-2"
                                style="background:#f8f9fc;border-radius:10px;"
                            >
                                <h5>
                                    <?= formatCurrency($earnings) ?>
                                </h5>
                                <small>Earnings</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="card-header">
                        My Recent Sales
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentSales as $row) { ?>
                                    <tr>
                                        <td>
                                            <?= $row['invoice_no'] ?>
                                        </td>
                                        <td>
                                            <?= formatCurrency($row['final_total']) ?>
                                        </td>
                                        <td>
                                            <?= date('H:i', strtotime($row['sale_date'])) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
