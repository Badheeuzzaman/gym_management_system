<?php

$page_title = "Equipment";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO equipment
        (
            name,
            category,
            purchase_date,
            cost,
            status,
            last_maintenance
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
        $_POST['category'],
        $_POST['purchase_date'],
        $_POST['cost'],
        $_POST['status'],
        !empty($_POST['last_maintenance']) ? $_POST['last_maintenance'] : null
    ]);
}

$stmt = $pdo->query("
    SELECT *
    FROM equipment
    ORDER BY id DESC
");

$eq = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Equipment Management</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus"></i> Add Equipment</button>
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
        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Equipment</th>
                            <th>Category</th>
                            <th>Purchase Date</th>
                            <th>Cost</th>
                            <th>Last Maintenance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eq as $e) { ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= $e['name'] ?>
                                    </strong>
                                </td>
                                <td>
                                    <?= $e['category'] ?>
                                </td>
                                <td>
                                    <?= $e['purchase_date'] ?>
                                </td>
                                <td>
                                    <?= formatCurrency($e['cost']) ?>
                                </td>
                                <td>
                                    <?= $e['last_maintenance'] ?: '-' ?>
                                </td>
                                <td>
                                    <?= getStatusBadge($e['status']) ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($eq)) { ?>
                            <tr>
                                <td colspan="6" class="text-center p-4">
                                    No equipment yet. Add treadmills, dumbbells etc.
                                </td>
                            </tr>
                        <?php } ?>
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
                <h5>Add Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required placeholder="Treadmill X200">
                </div>
                <div class="col-6">
                    <label class="form-label">Category</label >
                    <select name="category" class="form-select">
                        <option>Cardio</option >
                        <option>Strength</option >
                        <option>Free Weights</option >
                        <option>Other</option >
                    </select >
                </div >
                <div class="col-6">
                    <label class="form-label">Status</label >
                    <select name="status" class="form-select">
                        <option value="working">Working</option >
                        <option value="maintenance">Maintenance</option >
                        <option value="broken">Broken</option >
                    </select >
                </div >
                <div class="col-6">
                    <label class="form-label">Purchase Date</label>
                    <input
                        type="date"
                        name="purchase_date"
                        value="<?= date('Y-m-d') ?>"
                        class="form-control"
                    >
                </div>
                <div class="col-6">
                    <label class="form-label">Cost</label>
                    <input
                        type="number"
                        step="0.01"
                        name="cost"
                        class="form-control"
                    >
                </div>
                <div class="col-12">
                    <label class="form-label">Last Maintenance</label>
                    <input
                        type="date"
                        name="last_maintenance"
                        class="form-control"
                    >
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Equipment</button>
            </div>
        </form>

   	</div >
</div >
<?php require_once '../includes/footer.php'; ?>
