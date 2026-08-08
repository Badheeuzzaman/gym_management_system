<?php
$page_title = "Assign Workout";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare("INSERT INTO member_workouts (member_id, workout_id, assigned_by, assigned_date, notes, sets, reps) VALUES (?,?,?,?,?,?,?)")
        ->execute([
            $_POST['member_id'],
            $_POST['workout_id'],
            $_SESSION['user_id'],
            $_POST['assigned_date'] ?: date('Y-m-d'),
            $_POST['notes'],
            $_POST['sets'],
            $_POST['reps']
        ]);

    $msg = "Workout assigned!";
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM member_workouts WHERE id=?")
        ->execute([$_GET['delete']]);

    header("Location: assign_workout.php");
    exit;
}

$members = $pdo->query("SELECT id, name, member_code FROM members WHERE status='active'")->fetchAll();
$workouts = $pdo->query("SELECT * FROM workouts")->fetchAll();
$assigned = $pdo->query("SELECT mw.*, m.name as member_name, w.name as workout_name FROM member_workouts mw JOIN members m ON mw.member_id=m.id JOIN workouts w ON mw.workout_id=w.id ORDER BY mw.id DESC")->fetchAll();
?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Assign Workout</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                <i class="fas fa-plus me-1"></i> Assign Workout
            </button>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color:inherit;">
                    <div class="avatar" style="background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white;">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
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
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;min-width:220px;margin-top:8px;">
                    <li class="px-3 py-2">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="avatar" style="background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;">
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                            </div>
                            <div>
                                <strong style="font-size:13px;"><?= $_SESSION['full_name'] ?? 'Admin' ?></strong><br>
                                <small style="color:#636e72;font-size:11px;"><?= $_SESSION['username'] ?? 'admin' ?> • Admin</small>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
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
                    <li><hr class="dropdown-divider"></li>
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
        <?php if (isset($msg)): ?>
            <div class="alert alert-success"><?= $msg ?></div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card-modern">
                    <div class="card-header">Assigned Workouts (<?= count($assigned) ?>)</div>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Workout</th>
                                    <th>Sets x Reps</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assigned as $a): ?>
                                    <tr>
                                        <td><?= $a['member_name'] ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= $a['workout_name'] ?></span></td>
                                        <td><?= $a['sets'] ?> x <?= $a['reps'] ?></td>
                                        <td><?= $a['assigned_date'] ?></td>
                                        <td><?= getStatusBadge($a['status']) ?></td>
                                        <td>
                                            <a href="?delete=<?= $a['id'] ?>" class="btn btn-sm btn-light text-danger">
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
            <div class="col-md-4">
                <div class="card-modern">
                    <div class="card-header">Workout Library</div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($workouts as $w): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= $w['name'] ?></strong><br>
                                    <small style="color:#636e72;"><?= $w['category'] ?> | <?= $w['difficulty'] ?></small>
                                </div>
                                <span class="badge bg-light text-dark border"><?= $w['duration'] ?>m</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="assignModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>Assign Workout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Member *</label>
                    <select name="member_id" class="form-select" required>
                        <option value="">Select Member</option>
                        <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= $m['member_code'] ?> - <?= $m['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Workout *</label>
                    <select name="workout_id" class="form-select" required>
                        <?php foreach ($workouts as $w): ?>
                            <option value="<?= $w['id'] ?>"><?= $w['name'] ?> (<?= $w['category'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-4">
                    <label class="form-label">Sets</label>
                    <input name="sets" type="number" value="3" class="form-control">
                </div>
                <div class="col-4">
                    <label class="form-label">Reps</label>
                    <input name="reps" type="number" value="12" class="form-control">
                </div>
                <div class="col-4">
                    <label class="form-label">Date</label>
                    <input name="assigned_date" type="date" value="<?= date('Y-m-d') ?>" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" placeholder="Special instructions..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Assign Now</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
