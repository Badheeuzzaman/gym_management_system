<?php
$page_title = "Cash & Bank";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $insertTransfer = $pdo->prepare(
        "INSERT INTO transfers (from_account, to_account, amount, transfer_date, notes) VALUES (?,?,?,?,?)"
    );
    $insertTransfer->execute([
        $_POST['from_account'],
        $_POST['to_account'],
        $_POST['amount'],
        $_POST['transfer_date'],
        $_POST['notes']
    ]);

    $debitAccount = $pdo->prepare("UPDATE bank_accounts SET balance = balance - ? WHERE id = ?");
    $debitAccount->execute([$_POST['amount'], $_POST['from_account']]);

    $creditAccount = $pdo->prepare("UPDATE bank_accounts SET balance = balance + ? WHERE id = ?");
    $creditAccount->execute([$_POST['amount'], $_POST['to_account']]);
}

$accounts = $pdo->query("SELECT * FROM bank_accounts")->fetchAll();
$transfers = $pdo->query(
    "SELECT t.*, b1.bank_name as from_name, b2.bank_name as to_name FROM transfers t JOIN bank_accounts b1 ON t.from_account=b1.id JOIN bank_accounts b2 ON t.to_account=b2.id ORDER BY t.id DESC"
)->fetchAll();
?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Cash & Bank Transfers</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferModal"><i class="fas fa-exchange-alt"></i> New Transfer</button>
            <div class="dropdown">
                <a
                    href="#"
                    class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    style="color:inherit;"
                >
                    <div
                        class="avatar"
                        style="background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white;"
                    >
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div
                        class="d-none d-md-block text-start"
                        style="line-height:1.1;"
                    >
                        <div
                            style="font-size:13px; font-weight:600;color:#2d3436;"
                        >
                            <?= $_SESSION['full_name'] ?? 'Admin' ?>
                        </div>
                        <small
                            style="font-size:11px; color:#636e72;"
                        >
                            Administrator
                        </small>
                    </div>
                </a>
                <ul
                    class="dropdown-menu dropdown-menu-end shadow border-0"
                    style="border-radius:12px;min-width:220px;margin-top:8px;"
                >
                    <li class="px-3 py-2">
                        <div class="d-flex gap-2 align-items-center">
                            <div
                                class="avatar"
                                style="background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;"
                            >
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                            </div>
                            <div>
                                <strong style="font-size:13px;">
                                    <?= $_SESSION['full_name'] ?? 'Admin' ?>
                                </strong>
                                <br>
                                <small
                                    style="color:#636e72;font-size:11px;"
                                >
                                    <?= $currentUser['email'] ?? 'admin@gym.com' ?>
                                </small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item active" href="profile.php">
                            <i class="fas fa-user me-2" style="width:18px;"></i>
                            My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog me-2" style="width:18px;"></i>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="my_store_account.php">
                            <i class="fas fa-store me-2" style="width:18px;"></i>
                            My Store Account
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2" style="width:18px;"></i>
                            Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <div class="row g-3 mb-4">
            <?php foreach ($accounts as $a): ?>
                <div class="col-md-3">
                    <div class="card-modern p-3">
                        <small style="color:#636e72;"><?= $a['type'] ?> • <?= $a['bank_name'] ?></small>
                        <h5><?= $a['account_name'] ?></h5>
                        <h4><?= formatCurrency($a['balance']) ?></h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-modern">
            <div class="card-header">Transfer History</div>
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transfers as $t): ?>
                            <tr>
                                <td><?= $t['transfer_date'] ?></td>
                                <td><?= $t['from_name'] ?></td>
                                <td><i class="fas fa-arrow-right text-muted"></i> <?= $t['to_name'] ?></td>
                                <td><strong><?= formatCurrency($t['amount']) ?></strong></td>
                                <td><?= $t['notes'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="transferModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>New Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-6">
                    <label class="form-label">From Account</label>
                    <select name="from_account" class="form-select">
                        <?php foreach ($accounts as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= $a['bank_name'] ?> - <?= formatCurrency($a['balance']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">To Account</label>
                    <select name="to_account" class="form-select">
                        <?php foreach ($accounts as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= $a['bank_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Amount</label>
                    <input name="amount" type="number" step="0.01" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Date</label>
                    <input type="date" name="transfer_date" value="<?= date('Y-m-d') ?>" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <input name="notes" type="text" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Transfer</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
