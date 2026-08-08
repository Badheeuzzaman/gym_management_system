<?php

$page_title = "Staff";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Add Staff Member

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO staff
        (
            name,
            role,
            phone,
            email,
            salary,
            join_date,
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
        $_POST['name'],
        $_POST['role'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['salary'],
        $_POST['join_date'],
        $_POST['status']
    ]);

    $msg = "Staff member added successfully!";
}

// Get Staff List

$stmt = $pdo->query("
    SELECT *
    FROM staff
    ORDER BY id DESC
");

$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Staff</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Staff
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
        <div class="row g-3">
            <?php foreach ($staff as $s): ?>
                <div class="col-md-4">
                    <div class="card-modern p-4">
                        <div class="d-flex gap-3 align-items-center">
                            <div
                                class="avatar"
                                style="width:48px;height:48px;background:#dfe6e9;"
                            >
                                <?= strtoupper(substr($s['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h6>
                                    <?= $s['name'] ?>
                                </h6>
                                <small style="color:#636e72;">
                                    <?= $s['role'] ?>
                                </small>
                            </div>
                            <span class="badge bg-success ms-auto">
                                <?= $s['status'] ?>
                            </span>
                        </div>
                        <div class="mt-3" style="font-size:12px;color:#636e72;">
                            <i class="fas fa-phone me-1"></i>
                            <?= $s['phone'] ?>
                            <br>
                            <i class="fas fa-envelope me-1"></i>
                            <?= $s['email'] ?>
                            <br>
                            <i class="fas fa-money-bill me-1"></i>
                            <?= formatCurrency($s['salary']) ?> / month
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>Add Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option>Trainer</option>
                        <option>Receptionist</option>
                        <option>Manager</option>
                        <option>Cleaner</option>
                        <option>Accountant</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Phone</label>
                    <input name="phone" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">Email</label>
                    <input name="email" class="form-control">
                </div>
                <div class="col-3">
                    <label class="form-label">Salary</label>
                    <input name="salary" type="number" step="0.01" class="form-control">
                </div>
                <div class="col-3">
                    <label class="form-label">Join Date</label>
                    <input type="date" name="join_date" value="<?= date('Y-m-d') ?>" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Staff</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
