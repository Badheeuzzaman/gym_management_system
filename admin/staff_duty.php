<?php

$page_title = "Duty Roster";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';


// Assign Duty Roster

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO duty_roster
        (
            staff_id,
            shift_id,
            duty_date
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $_POST['staff_id'],
        $_POST['shift_id'],
        $_POST['duty_date']
    ]);

    $msg = "Duty assigned successfully!";
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

// Get Shift List

$stmt = $pdo->query("
    SELECT *
    FROM staff_shifts
    ORDER BY start_time ASC
");

$shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Upcoming Duty Roster

$stmt = $pdo->query("
    SELECT
        dr.*,
        s.name AS staff_name,
        sh.name AS shift_name,
        sh.start_time,
        sh.end_time
    FROM duty_roster dr
    INNER JOIN staff s
        ON dr.staff_id = s.id
    INNER JOIN staff_shifts sh
        ON dr.shift_id = sh.id
    WHERE dr.duty_date >= CURDATE()
    ORDER BY
        dr.duty_date ASC,
        sh.start_time ASC
");

$roster = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Duty Roster</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Assign Duty
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
                            <th>Date</th>
                            <th>Staff</th>
                            <th>Shift</th>
                            <th>Timing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roster as $r): ?>
                            <tr>
                                <td>
                                    <?= date('D, M d', strtotime($r['duty_date'])) ?>
                                </td>
                                <td>
                                    <?= $r['staff_name'] ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= $r['shift_name'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $r['start_time'] ?> - <?= $r['end_time'] ?>
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
                <h5>Assign Duty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-6">
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
                    <label class="form-label">Shift</label>
                    <select name="shift_id" class="form-select">
                        <?php foreach ($shifts as $sh): ?>
                            <option value="<?= $sh['id'] ?>">
                                <?= $sh['name'] ?> (<?= $sh['start_time'] ?> - <?= $sh['end_time'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Date</label>
                    <input type="date" name="duty_date" value="<?= date('Y-m-d') ?>" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
