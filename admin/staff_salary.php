<?php

$page_title = "Salary & Payroll";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Process Payroll

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $salary = (float) $_POST['salary'];
    $deductions = (float) $_POST['deductions'];
    $bonus = (float) $_POST['bonus'];

    $netPay = $salary - $deductions + $bonus;

    $stmt = $pdo->prepare("
        INSERT INTO staff_salary
        (
            staff_id,
            month,
            salary,
            deductions,
            bonus,
            net_pay,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $_POST['staff_id'],
        $_POST['month'],
        $salary,
        $deductions,
        $bonus,
        $netPay,
        $_POST['status']
    ]);

    $msg = "Payroll processed successfully!";
}

// Get Staff List

$stmt = $pdo->query("
    SELECT
        id,
        name
    FROM staff
    ORDER BY name ASC
");

$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Payroll Records

$stmt = $pdo->query("
    SELECT
        ss.*,
        st.name AS staff_name
    FROM staff_salary ss
    INNER JOIN staff st
        ON ss.staff_id = st.id
    ORDER BY
        ss.month DESC,
        ss.id DESC
");

$payrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Salary & Payroll</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Payroll
            </button>
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
    <div class="card-modern">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Staff</th>
                        <th>Month</th>
                        <th>Salary</th>
                        <th>Deductions</th>
                        <th>Bonus</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payrolls as $p): ?>
                        <tr>
                            <td>
                                <?= $p['staff_name'] ?>
                            </td>
                            <td>
                                <?= $p['month'] ?>
                            </td>
                            <td>
                                <?= formatCurrency($p['salary']) ?>
                            </td>
                            <td>
                                <?= formatCurrency($p['deductions']) ?>
                            </td>
                            <td>
                                <?= formatCurrency($p['bonus']) ?>
                            </td>
                            <td>
                                <strong>
                                    <?= formatCurrency($p['net_pay']) ?>
                                </strong>
                            </td>
                            <td>
                                <?= getStatusBadge($p['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>Add Payroll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Staff</label>
                    <select name="staff_id" class="form-select">
                        <?php foreach ($staff as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= $s['name'] ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Month</label>
                    <input name="month" type="month" value="<?= date('Y-m') ?>" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="col-4">
                    <label class="form-label">Salary</label>
                    <input name="salary" type="number" step="0.01" class="form-control">
                </div>
                <div class="col-4">
                    <label class="form-label">Deductions</label>
                    <input name="deductions" type="number" step="0.01" value="0" class="form-control">
                </div>
                <div class="col-4">
                    <label class="form-label">Bonus</label>
                    <input name="bonus" type="number" step="0.01" value="0" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Payroll</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
