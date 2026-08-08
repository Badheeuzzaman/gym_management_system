<?php

$page_title = "Staff Attendance";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Mark Staff Attendance

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO staff_attendance
        (
            staff_id,
            date,
            check_in,
            check_out,
            status
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
        $_POST['staff_id'],
        $_POST['date'],
        $_POST['check_in'],
        $_POST['check_out'],
        $_POST['status']
    ]);

    $msg = "Staff attendance recorded successfully!";
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

// Selected Date

$date = $_GET['date'] ?? date('Y-m-d');

// Get Attendance Records

$stmt = $pdo->prepare("
    SELECT
        sa.*,
        s.name
    FROM staff_attendance sa
    INNER JOIN staff s
        ON sa.staff_id = s.id
    WHERE sa.date = ?
    ORDER BY sa.check_in ASC
");

$stmt->execute([
    $date
]);

$att = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Staff Attendance - <?= $date ?></h4>
        </div>
        <div class="topbar-actions">
            <input
                type="date"
                class="form-control"
                value="<?= $date ?>"
                onchange="location.href='?date='+this.value"
            >
            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#markModal"
            >
                Mark
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
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($att as $a): ?>
                            <?php
                                $hrs = $a['check_in'] && $a['check_out']
                                    ? round((strtotime($a['check_out']) - strtotime($a['check_in'])) / 3600, 1)
                                    : '-';
                            ?>
                            <tr>
                                <td>
                                    <?= $a['name'] ?>
                                </td>
                                <td>
                                    <?= $a['check_in'] ?>
                                </td>
                                <td>
                                    <?= $a['check_out'] ?: 'Active' ?>
                                </td>
                                <td>
                                    <?= getStatusBadge($a['status']) ?>
                                </td>
                                <td>
                                    <?= $hrs ?> hrs
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="markModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>Mark Staff Attendance</h5>
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
                    <label class="form-label">Date</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="form-control">
                </div>
                <div class="col-3">
                    <label class="form-label">Check In</label>
                    <input type="time" name="check_in" value="<?= date('H:i') ?>" class="form-control">
                </div>
                <div class="col-3">
                    <label class="form-label">Check Out</label>
                    <input type="time" name="check_out" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="half_day">Half Day</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Mark</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
