<?php
$page_title="Workouts";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $pdo->prepare("INSERT INTO workouts (name, category, difficulty, duration, description) VALUES (?,?,?,?,?)")
        ->execute([$_POST['name'],$_POST['category'],$_POST['difficulty'],$_POST['duration'],$_POST['description']]);
}
$workouts=$pdo->query("SELECT * FROM workouts ORDER BY id DESC")->fetchAll();
?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Workout Library</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Workout
            </button>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color:inherit;">
                    <div class="avatar" style="background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white;">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A',0,1)) ?>
                    </div>
                    <div class="d-none d-md-block text-start" style="line-height:1.1;">
                        <div style="font-size:13px; font-weight:600;color:#2d3436;">
                            <?= $_SESSION['full_name'] ?? 'Admin' ?>
                        </div>
                        <small style="font-size:11px; color:#636e72;">Administrator</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;min-width:220px;margin-top:8px;">
                    <li class="px-3 py-2">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="avatar" style="background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;">
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A',0,1)) ?>
                            </div>
                            <div>
                                <strong style="font-size:13px;"><?= $_SESSION['full_name'] ?? 'Admin' ?></strong>
                                <br>
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
        <div class="row g-3">
            <?php foreach($workouts as $w): ?>
                <div class="col-md-4">
                    <div class="card-modern p-0 overflow-hidden">
                        <div style="height:120px;background:linear-gradient(135deg,#6c5ce7,#a29bfe);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-dumbbell fa-3x text-white opacity-50"></i>
                        </div>
                        <div class="p-3">
                            <div class="d-flex justify-content-between">
                                <h6><?= $w['name'] ?></h6>
                                <span class="badge bg-light text-dark border"><?= $w['duration'] ?>m</span>
                            </div>
                            <small style="color:#636e72;"><?= $w['category'] ?> • <?= ucfirst($w['difficulty']) ?></small>
                            <p style="font-size:12px;color:#636e72;" class="mt-2"><?= $w['description'] ?: 'No description' ?></p>
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
                <h5>Add Workout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required placeholder="Full Body Blast">
                </div>
                <div class="col-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option>Strength</option>
                        <option>Cardio</option>
                        <option>Flexibility</option>
                        <option>HIIT</option>
                        <option>Yoga</option>
                    </select>
                </div>
                <div class="col-3">
                    <label class="form-label">Difficulty</label>
                    <select name="difficulty" class="form-select">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div class="col-3">
                    <label class="form-label">Duration (min)</label>
                    <input name="duration" type="number" class="form-control" value="60">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Workout</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>