<?php

$page_title = "Shift Timings";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Add Shift

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO staff_shifts
        (
            name,
            start_time,
            end_time
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['start_time'],
        $_POST['end_time']
    ]);

    $msg = "Shift added successfully!";
}

// Get Shift List

$stmt = $pdo->query("
    SELECT *
    FROM staff_shifts
    ORDER BY start_time ASC
");

$shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Shift Timings</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Shift
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
            <?php foreach ($shifts as $sh): ?>
                <div class="col-md-4">
                    <div class="card-modern p-4">
                        <h5>
                            <i class="fas fa-clock text-primary me-2"></i>
                            <?= $sh['name'] ?>
                        </h5>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark border">
                                <?= $sh['start_time'] ?>
                            </span>
                            <i class="fas fa-arrow-right mx-2"></i>
                            <span class="badge bg-light text-dark border">
                                <?= $sh['end_time'] ?>
                            </span>
                        </div>
                        <small style="color:#636e72;" class="mt-2 d-block">
                            <?= round((strtotime($sh['end_time']) - strtotime($sh['start_time'])) / 3600, 1) ?> hrs
                        </small>
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
                <h5>Add Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Shift Name</label>
                    <input name="name" class="form-control" required placeholder="Morning">
                </div>
                <div class="col-6">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-control" required="">
                </div>
                <div class="col-6">
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" class="form-control" required="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Shift</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
