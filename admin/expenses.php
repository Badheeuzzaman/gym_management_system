<?php

$page_title = "Expenses";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO expenses
        (
            title,
            category,
            amount,
            expense_date,
            payment_method,
            description
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $_POST['title'],
        $_POST['category'],
        $_POST['amount'],
        $_POST['expense_date'],
        $_POST['payment_method'],
        $_POST['description']
    ]);
}

if (isset($_GET['delete'])) {

    $stmt = $pdo->prepare("
        DELETE FROM expenses
        WHERE id = ?
    ");

    $stmt->execute([
        $_GET['delete']
    ]);

    header("Location: expenses.php");
    exit;
}

$stmt = $pdo->query("
    SELECT *
    FROM expenses
    ORDER BY expense_date DESC
");

$exp = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT SUM(amount)
    FROM expenses
    WHERE MONTH(expense_date) = MONTH(CURDATE())
      AND YEAR(expense_date) = YEAR(CURDATE())
");

$total = $stmt->fetchColumn() ?: 0;

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>
                Expenses
                <small style="color:#d63031;">
                    <?= formatCurrency($total) ?> this month
                </small>
            </h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i>
                Add Expense
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
        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exp as $e) { ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= $e['title'] ?>
                                    </strong>
                                    <br>
                                    <small>
                                        <?= $e['description'] ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= $e['category'] ?>
                                    </span>
                                </td>
                                <td style="color:#d63031;font-weight:700;">
                                    -<?= formatCurrency($e['amount']) ?>
                                </td>
                                <td>
                                    <?= $e['expense_date'] ?>
                                </td>
                                <td>
                                    <?= $e['payment_method'] ?>
                                </td>
                                <td>
                                    <a href="?delete=<?= $e['id'] ?>" class="btn btn-sm btn-light text-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>Add Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Title *</label>
                    <input name="title" class="form-control" required placeholder="Electricity Bill">
                </div>
                <div class="col-6">
                    <label class="form-label">Category</label >
                    <select name="category" class="form-select">
                        <option>Utilities</option >
                        <option>Rent</option >
                        <option>Maintenance</option >
                        <option>Staff</option >
                        <option>Marketing</option >
                        <option>Equipment</option >
                        <option>Other</option >
</select>
                </div>
                <div class="col-6">
                    <label class="form-label">Amount *</label>
                    <input name="amount" type="number" step="0.01" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Date</label>
                    <input
                        type="date"
                        name="expense_date"
                        value="<?= date('Y-m-d') ?>"
                        class="form-control"
                    />
                </div>
                <div class="col-6">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Expense</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
