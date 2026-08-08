<?php

$page_title = "Classes";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO classes
        (
            name,
            trainer_id,
            time_slot,
            days,
            capacity
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
        $_POST['name'],
        !empty($_POST['trainer_id']) ? $_POST['trainer_id'] : null,
        $_POST['time_slot'],
        $_POST['days'],
        $_POST['capacity']
    ]);
}

$stmt = $pdo->query("
    SELECT
        id,
        name
    FROM trainers
    ORDER BY name ASC
");

$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT
        c.*,
        t.name AS trainer_name
    FROM classes c
    LEFT JOIN trainers t
        ON c.trainer_id = t.id
    ORDER BY c.id DESC
");

$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Classes</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Class
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
            <?php foreach ($classes as $c) { ?>
                <div class="col-md-4">
                    <div class="card-modern p-4">
                        <div class="d-flex justify-content-between">
                            <h5>
                                <?= $c['name'] ?>
                            </h5>
                            <span class="badge bg-light text-dark border">
                                <?= $c['capacity'] ?> seats
                            </span>
                        </div>
                        <div class="mt-2">
                            <small style="color:#636e72;">
                                <i class="fas fa-user-tie me-1"></i>
                                <?= $c['trainer_name'] ?? 'No Trainer' ?>
                            </small>
                            <br>
                            <small style="color:#636e72;">
                                <i class="fas fa-clock me-1"></i>
                                <?= $c['time_slot'] ?>
                            </small>
                            <br>
                            <small style="color:#636e72;">
                                <i class="fas fa-calendar me-1"></i>
                                <?= $c['days'] ?>
                            </small>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-primary" style="width:<?php if ($c['capacity']) { echo ($c['enrolled'] / $c['capacity'] * 100); } else { echo 0; } ?>%;"></div>
                            </div>
                            <small style="color:#636e72;">
                                <?= $c['enrolled'] ?>
                                /
                                <?= $c['capacity'] ?>
                                enrolled
                            </small>
                        </div>
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
                <h5>Add Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Class Name</label>
                    <input name="name" class="form-control" required placeholder="Morning Yoga">
                </div>
                <div class="col-12">
                    <label class="form-label">Trainer</label>
                    <select name="trainer_id" class="form-select">
                        <option value="">Select Trainer</option>
                        <?php foreach ($trainers as $t) { ?>
                            <option value="<?= $t['id'] ?>">
                                <?= $t['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Time Slot</label>
                    <input name="time_slot" class="form-control" placeholder="06:00-07:00 AM">
                </div>
                <div class="col-6">
                    <label class="form-label">Capacity</label>
                    <input name="capacity" type="number" class="form-control" value="20">
                </div>
                <div class="col-12">
                    <label class="form-label">Days</label>
                    <input name="days" class="form-control" placeholder="Mon, Wed, Fri">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Class</button>
            </div>

        </form>

    </div>

</div>

<?php require_once '../includes/footer.php'; ?>
