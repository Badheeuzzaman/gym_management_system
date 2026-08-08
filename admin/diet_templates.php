<?php

$page_title = "Diet Templates";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $meals = json_encode([
        'breakfast' => $_POST['breakfast'],
        'lunch'     => $_POST['lunch'],
        'dinner'    => $_POST['dinner'],
        'snacks'    => $_POST['snacks']
    ]);

    $stmt = $pdo->prepare("
        INSERT INTO diet_templates
        (
            name,
            goal,
            description,
            meals
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['goal'],
        $_POST['description'],
        $meals
    ]);
}

$stmt = $pdo->query("
    SELECT *
    FROM diet_templates
    ORDER BY id DESC
");

$diets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Diet Templates</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Template
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
            <?php foreach ($diets as $d) {
                $meals = json_decode($d['meals'], true);
            ?>
            <div class="col-md-6">
                <div class="card-modern p-4">
                    <div class="d-flex justify-content-between">
                        <h5>
                            <i class="fas fa-apple-alt text-success me-2"></i>
                            <?= $d['name'] ?>
                        </h5>
                        <span class="badge bg-success">
                            <?= $d['goal'] ?>
                        </span>
                    </div>
                    <p style="font-size:13px;color:#636e72;">
                        <?= $d['description'] ?>
                    </p>
                    <?php if ($meals) { ?>
                        <div class="row g-2 mt-2">
                            <?php foreach ($meals as $k => $v) { ?>
                                <div class="col-6">
                                    <div class="p-2" style="background:#f8f9fc;border-radius:8px;">
                                        <small style="color:#636e72;text-transform:uppercase;font-size:10px;">
                                            <?= $k ?>
                                        </small>
                                        <div style="font-size:12px;">
                                            <?= $v ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php if (empty($diets)) { ?>
                <div class="col-12">
                    <div class="card-modern p-5 text-center">
                        <i class="fas fa-utensils fa-2x mb-2" style="color:#dfe6e9;"></i>
                        <p>
                            No diet templates yet. Add one.
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
</div>
</div>

<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>Add Diet Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-6">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required placeholder="Weight Loss Plan">
                </div>
                <div class="col-6">
                    <label class="form-label">Goal</label>
                    <select name="goal" class="form-select">
                        <option>Weight Loss</option>
                        <option>Muscle Gain</option>
                        <option>Maintenance</option>
                        <option>Keto</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="col-6">
                    <label class="form-label">Breakfast</label>
                    <input name="breakfast" class="form-control" placeholder="Oats + Eggs">
                </div>
                <div class="col-6">
                    <label class="form-label">Lunch</label>
                    <input name="lunch" class="form-control" placeholder="Chicken + Rice">
                </div>
                <div class="col-6">
                    <label class="form-label">Dinner</label>
                    <input name="dinner" class="form-control" placeholder="Chicken + Rice">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Template</button>
            </div>
        </form>
    </div>
</div>
