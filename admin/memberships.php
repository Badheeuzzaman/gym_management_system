<?php

$page_title = "Memberships";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// =========================================
// Add Membership
// =========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare(
        "
        INSERT INTO memberships
        (
            member_id,
            plan_id,
            start_date,
            end_date,
            amount,
            payment_status
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
        "
    );

    $stmt->execute([
        $_POST['member_id'],
        $_POST['plan_id'],
        $_POST['start_date'],
        $_POST['end_date'],
        $_POST['amount'],
        $_POST['payment_status']
    ]);

    $msg = "Membership added successfully!";
}

// =========================================
// Delete Membership
// =========================================

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare(
        "
        DELETE FROM memberships
        WHERE id = ?
        "
    );

    $stmt->execute([
        $_GET['delete']
    ]);

    header("Location: memberships.php");
    exit;
}

// =========================================
// Get Members
// =========================================

$stmt = $pdo->query(
    "
    SELECT
        id,
        name,
        member_code
    FROM members
    ORDER BY name ASC
    "
);

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =========================================
// Get Active Plans
// =========================================

$stmt = $pdo->query(
    "
    SELECT *
    FROM plans
    WHERE status = 'active'
    ORDER BY name ASC
    "
);

$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =========================================
// Get Membership List
// =========================================

$stmt = $pdo->query(
    "
    SELECT
        ms.*,
        m.name AS member_name,
        m.member_code,
        p.name AS plan_name
    FROM memberships ms
    INNER JOIN members m
        ON ms.member_id = m.id
    INNER JOIN plans p
        ON ms.plan_id = p.id
    ORDER BY ms.end_date DESC
    "
);

$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Memberships</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> New Membership
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
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A',0,1)) ?>
                    </div>
                    <div class="d-none d-md-block text-start" style="line-height:1.1;">
                        <div style="font-size:13px; font-weight:600;color:#2d3436;">
                            <?= $_SESSION['full_name'] ?? 'Admin' ?>
                        </div>
                        <small style="font-size:11px; color:#636e72;">
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
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A',0,1)) ?>
                            </div>
                            <div>
                                <strong style="font-size:13px;">
                                    <?= $_SESSION['full_name'] ?? 'Admin' ?>
                                </strong>
                                <br>
                                <small style="color:#636e72;font-size:11px;">
                                    <?= $_SESSION['username'] ?? 'admin' ?> • Admin
                                </small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user me-2" style="width:18px;"></i> My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog me-2" style="width:18px;"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="my_store_account.php">
                            <i class="fas fa-store me-2" style="width:18px;"></i> My Store Account
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="form_fields.php">
                            <i class="fas fa-wpforms me-2" style="width:18px;"></i> Form Builder
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2" style="width:18px;"></i> Logout
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
                            <th>Member</th>
                            <th>Plan</th>
                            <th>Start - End</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Days Left</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $ms): ?>
                            <?php $days = (strtotime($ms['end_date']) - time()) / 86400; ?>
                            <tr>
                                <td>
                                    <strong><?= $ms['member_name'] ?></strong>
                                    <br>
                                    <code><?= $ms['member_code'] ?></code>
                                </td>
                                <td><?= $ms['plan_name'] ?></td>
                                <td>
                                    <?= date('M d', strtotime($ms['start_date'])) ?> → <?= date('M d, Y', strtotime($ms['end_date'])) ?>
                                </td>
                                <td><?= formatCurrency($ms['amount']) ?></td>
                                <td><?= getStatusBadge($ms['payment_status']) ?></td>
                                <td>
                                    <?php if ($days < 0): ?>
                                        <span class="badge bg-danger">Expired <?= abs(round($days)) ?>d ago</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= round($days) ?> days</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?delete=<?= $ms['id'] ?>" class="btn btn-sm btn-light text-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
                <h5>New Membership</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Member</label>
                    <select name="member_id" class="form-select" required>
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id'] ?>">
                                <?= $m['member_code'] ?> - <?= $m['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Plan</label>
                    <select name="plan_id" id="planSelect" class="form-select" required>
                        <?php foreach ($plans as $p): ?>
                            <option
                                value="<?= $p['id'] ?>"
                                data-price="<?= $p['price'] ?>"
                                data-days="<?= $p['duration_days'] ?>"
                            >
                                <?= $p['name'] ?> - <?= formatCurrency($p['price']) ?> / <?= $p['duration_days'] ?> days
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="startDate" value="<?= date('Y-m-d') ?>" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" id="endDate" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">Amount</label>
                    <input name="amount" id="amount" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">Payment</label>
                    <select name="payment_status" class="form-select">
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="partial">Partial</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Create Membership</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('planSelect')?.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    document.getElementById('amount').value = opt.dataset.price;

    const start = new Date(document.getElementById('startDate').value);
    start.setDate(start.getDate() + parseInt(opt.dataset.days));
    document.getElementById('endDate').value = start.toISOString().split('T')[0];
});

document.getElementById('planSelect')?.dispatchEvent(new Event('change'));
</script>

<?php require_once '../includes/footer.php'; ?>
