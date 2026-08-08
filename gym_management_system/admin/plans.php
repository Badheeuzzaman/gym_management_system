<?php

$page_title = "Membership Plans";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';


// =========================================
// Add Membership Plan
// =========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $features = !empty($_POST['features'])
        ? json_encode(explode(',', $_POST['features']))
        : json_encode([]);

    $stmt = $pdo->prepare("
        INSERT INTO plans
        (
            name,
            duration_days,
            price,
            description,
            features,
            status
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
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['duration_days'],
        $_POST['price'],
        $_POST['description'],
        $features,
        $_POST['status']
    ]);

    $msg = "Membership plan added successfully!";
}


// =========================================
// Delete Membership Plan
// =========================================

if (isset($_GET['delete'])) {

    $stmt = $pdo->prepare("
        DELETE FROM plans
        WHERE id = ?
    ");

    $stmt->execute([
        $_GET['delete']
    ]);

    header("Location: plans.php");
    exit;
}


// =========================================
// Get Membership Plans
// =========================================

$stmt = $pdo->query("
    SELECT *
    FROM plans
    ORDER BY price ASC
");

$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Plans</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Plan
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
        <div class="row g-4">
            <?php foreach ($plans as $pl) { ?>
                <?php $feats = json_decode($pl['features'], true); ?>
                <div class="col-md-4">
                    <div
                        class="card-modern p-4"
                        style="<?php echo $pl['name'] === 'Quarterly Pro' ? 'border:2px solid #6c5ce7;' : ''; ?>"
                    >
                        <div class="d-flex justify-content-between">
                            <h5>
                                <?= $pl['name'] ?>
                            </h5>
                            <span
                                class="badge <?php echo $pl['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>"
                            >
                                <?= $pl['status'] ?>
                            </span>
                        </div>
                        <h2 class="mt-2" style="font-weight:800;">
                            <?= formatCurrency($pl['price']) ?>
                            <small style="font-size:14px;color:#636e72;">
                                /<?= $pl['duration_days'] ?>d
                            </small>
                        </h2>
                        <p style="font-size:12px;color:#636e72;">
                            <?= $pl['description'] ?>
                        </p>
                        <ul style="font-size:13px;padding-left:18px;">
                            <?php if ($feats) {
                                foreach ($feats as $f) { ?>
                                    <li>
                                        <?= $f ?>
                                    </li>
                                <?php }
                            } ?>
                        </ul>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-primary w-100">
                                Select Plan
                            </button>
                            <a
                                href="?delete=<?= $pl['id'] ?>"
                                class="btn btn-light"
                            >
                                <i class="fas fa-trash"></i>
                            </a>
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
                <h5>Add Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Duration (days) *</label>
                    <input name="duration_days" type="number" class="form-control" required value="30">
                </div>
                <div class="col-6">
                    <label class ="form-label">Price *</label>
                    <input name ="price" type ="number" step ="0.01" class ="form-control" required>
                </div>
                <div class ="col-12">
                    <label class ="form-label">Description</label>
                    <input name ="description" class ="form-control">
                </div>
                <div class ="col-12">
                    <label class ="form-label">Features (comma separated)</label>
                    <input name ="features" class ="form-control" placeholder ="Gym Access, Cardio, Free Wifi">
                </div>
                <div class ="col-12">
                    <label class ="form-label">Status</label>
                    <select name= "status" class= "form-select">
                        <option value= "active">Active</option>
                        <option value= "inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class ="modal-footer">
                <button type="submit" class="btn btn-primary">Add Plan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
