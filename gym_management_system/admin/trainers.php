<?php
$page_title = "Trainers";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare(
        "INSERT INTO trainers (name,phone,email,specialization,experience,salary,status) VALUES (?,?,?,?,?,?,?)"
    );
    $stmt->execute([
        $_POST['name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['specialization'],
        $_POST['experience'],
        $_POST['salary'],
        $_POST['status']
    ]);
}

if (isset($_GET['delete'])) {
    $deleteStmt = $pdo->prepare(
        "DELETE FROM trainers WHERE id=?"
    );
    $deleteStmt->execute([
        $_GET['delete']
    ]);
    header("Location: trainers.php");
    exit;
}

$trainers = $pdo->query(
    "SELECT * FROM trainers ORDER BY id DESC"
)->fetchAll();

$counts = $pdo->query(
    "SELECT trainer_id, COUNT(*) as cnt FROM members GROUP BY trainer_id"
)->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button
                class="btn btn-light d-lg-none"
                id="sidebarToggle"
            >
                <i class="fas fa-bars"></i>
            </button>
            <h4>Trainers</h4>
        </div>
        <div class="topbar-actions">
            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addModal"
            >
                <i class="fas fa-plus"></i> Add Trainer
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
            <?php foreach ($trainers as $t): ?>
                <div class="col-md-4">
                    <div class="card-modern p-4">
                        <div class="d-flex gap-3">
                            <div
                                class="avatar"
                                style="width:56px;height:56px;background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;font-size:20px;"
                            >
                                <?= strtoupper(substr($t['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h6 style="font-weight:700;"><?= $t['name'] ?></h6>
                                <small style="color:#636e72;">
                                    <?= $t['specialization'] ?> • <?= $t['experience'] ?> yrs exp
                                </small>
                                <div>
                                    <span class="badge bg-light text-dark border">
                                        <?= $t['phone'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-between">
                            <div>
                                <small style="color:#636e72;">Clients</small>
                                <h5><?= $counts[$t['id']] ?? 0 ?></h5>
                            </div>
                            <div>
                                <small style="color:#636e72;">Salary</small>
                                <h5><?= formatCurrency($t['salary']) ?></h5>
                            </div>
                            <div>
                                <span class="badge bg-success"><?= $t['status'] ?></span>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <a
                                href="assign_workout.php"
                                class="btn btn-sm btn-light w-100"
                            >
                                Assign Work
                            </a>
                            <a
                                href="?delete=<?= $t['id'] ?>"
                                class="btn btn-sm btn-light text-danger"
                            >
                                <i class="fas fa-trash"></i>
                            </a>
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
                <h5>Add Trainer</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Phone</label>
                    <input name="phone" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">Email</label>
                    <input name="email" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">Specialization</label>
                    <input
                        name="specialization"
                        class="form-control"
                        placeholder="Strength Training"
                    >
                </div>
                <div class="col-3">
                    <label class="form-label">Experience (yrs)</label>
                    <input
                        name="experience"
                        type="number"
                        class="form-control"
                        value="1"
                    >
                </div>
                <div class="col-3">
                    <label class="form-label">Salary</label>
                    <input
                        name="salary"
                        type="number"
                        step="0.01"
                        class="form-control"
                    >
                </div>
                <div class="col-12">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    Add Trainer
                </button>
            </div>
        </form>
    </div>
</div>
