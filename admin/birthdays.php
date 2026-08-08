<?php

$page_title = "Birthdays";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$filter = $_GET['filter'] ?? 'today';

if ($filter == 'today') {

    $q = "
        SELECT *
        FROM members
        WHERE MONTH(dob) = MONTH(CURDATE())
        AND DAY(dob) = DAY(CURDATE())
    ";

} elseif ($filter == 'month') {

    $q = "
        SELECT *
        FROM members
        WHERE MONTH(dob) = MONTH(CURDATE())
        ORDER BY DAY(dob)
    ";

} else {

    $q = "
        SELECT *
        FROM members
        WHERE dob IS NOT NULL
        ORDER BY MONTH(dob), DAY(dob)
    ";
}

$stmt = $pdo->query($q);

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$todayCount = $pdo->query("
    SELECT COUNT(*)
    FROM members
    WHERE MONTH(dob) = MONTH(CURDATE())
    AND DAY(dob) = DAY(CURDATE())
")->fetchColumn();

$monthCount = $pdo->query("
    SELECT COUNT(*)
    FROM members
    WHERE MONTH(dob) = MONTH(CURDATE())
")->fetchColumn();

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4><i class="fas fa-birthday-cake text-danger"></i> Birthdays</h4>
        </div>
        <div class="topbar-actions">
            <a href="?filter=today" class="btn btn-sm <?php if ($filter == 'today') { echo 'btn-primary'; } else { echo 'btn-light'; } ?>">
                Today (<?= $todayCount ?>)
            </a>
            <a href="?filter=month" class="btn btn-sm <?php if ($filter == 'month') { echo 'btn-primary'; } else { echo 'btn-light'; } ?>">
                This Month (<?= $monthCount ?>)
            </a>
            <a href="?filter=all" class="btn btn-sm <?php if ($filter == 'all') { echo 'btn-primary'; } else { echo 'btn-light'; } ?>">
                All
            </a>
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
        <div class="row g-3">
<?php foreach ($members as $m) { ?>
            <div class="col-md-4 col-lg-3">
                <div class="card-modern p-4 text-center" style="border-top:4px solid #e84393;">
                    <div class="avatar mx-auto mb-3" style="width:64px;height:64px;font-size:24px;background:linear-gradient(135deg,#e84393,#fd79a8);color:white;">
                        <?= strtoupper(substr($m['name'], 0, 1)) ?>
                    </div>
                    <h6 style="font-weight:700;">
                        <?= $m['name'] ?>
                    </h6>
                    <small style="color:#636e72;">
                        <?= date('M d, Y', strtotime($m['dob'])) ?>
                    </small>
                    <div class="mt-2">
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-gift me-1"></i>
                            <?= $m['phone'] ?>
                        </span>
                    </div>
                    <div class="mt-3 d-flex gap-2 justify-content-center">
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-sms"></i>
                            Send Wishes
                        </button>
                        <a href="members.php" class="btn btn-sm btn-light">
                            View
                        </a>
                    </div>
                </div>
            </div>
<?php } ?>
<?php if (empty($members)) { ?>
            <div class="col-12">
                <div class="card-modern p-5 text-center">
                    <i class="fas fa-birthday-cake fa-3x mb-3" style="color:#dfe6e9;"></i>
                    <h5>
                        No birthdays <?= $filter ?>
                    </h5>
                    <p style="color:#636e72;">
                        No members have birthdays <?php if ($filter == 'today') { echo 'today'; } else { echo 'this month'; } ?>.
                    </p>
                </div>
            </div>
<?php } ?>
        </div>
</div>
</div>
<?php require_once '../includes/footer.php'; ?>
