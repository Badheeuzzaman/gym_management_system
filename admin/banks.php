<?php

$page_title = "Bank Accounts";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO bank_accounts
        (
            bank_name,
            account_name,
            account_number,
            balance,
            type
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $_POST['bank_name'],
        $_POST['account_name'],
        $_POST['account_number'],
        $_POST['balance'],
        $_POST['type']
    ]);
}

if (isset($_GET['delete'])) {

    $stmt = $pdo->prepare("
        DELETE FROM bank_accounts
        WHERE id = ?
    ");

    $stmt->execute([
        $_GET['delete']
    ]);

    header("Location: banks.php");
    exit;
}

$stmt = $pdo->query("
    SELECT *
    FROM bank_accounts
    ORDER BY id DESC
");

$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Bank Accounts</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Account
            </button>
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
            <?php foreach ($accounts as $a) { ?>
            <div class="col-md-4">
                <div class="card-modern p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge bg-light text-dark border mb-2">
                                <?= ucfirst($a['type']) ?>
                            </span>
                            <h5>
                                <?= $a['bank_name'] ?>
                            </h5>
                            <small style="color:#636e72;">
                                <?= $a['account_name'] ?>
                                •
                                <?= $a['account_number'] ?>
                            </small>
                        </div>
                        <a href="?delete=<?= $a['id'] ?>" class="btn btn-sm btn-light text-danger">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                    <h3 class="mt-3">
                        <?= formatCurrency($a['balance']) ?>
                    </h3>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>Add Bank Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-6">
                    <label class="form-label">Bank Name</label>
                    <input name="bank_name" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Account Name</label>
                    <input name="account_name" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Account Number</label>
                    <input name="account_number" class="form-control">
                </div>
                <div class="col-3">
                    <label class="form-label">Balance</label>
                    <input
                        name="balance"
                        type="number"
                        step="0.01"
                        value="0"
                        class="form-control"
                    >
                </div>
                <div class="col-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="bank">Bank</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Account</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
