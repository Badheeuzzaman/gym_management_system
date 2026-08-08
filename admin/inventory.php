<?php

$page_title = "Inventory";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO inventory
        (
            name,
            category,
            quantity,
            unit,
            min_stock,
            supplier_id,
            cost
        )
        VALUES
        (
            ?,
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
        $_POST['quantity'],
        $_POST['unit'],
        $_POST['min_stock'],
        !empty($_POST['supplier_id']) ? $_POST['supplier_id'] : null,
        $_POST['cost']
    ]);
}

$stmt = $pdo->query("
    SELECT
        id,
        name
    FROM suppliers
    ORDER BY name ASC
");

$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT
        i.*,
        s.name AS supplier_name
    FROM inventory i
    LEFT JOIN suppliers s
        ON i.supplier_id = s.id
    ORDER BY i.id DESC
");

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Inventory</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Item
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
        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Min Stock</th>
                            <th>Cost</th>
                            <th>Supplier</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it) { ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= $it['name'] ?>
                                </strong>
                            </td>
                            <td>
                                <?= $it['category'] ?>
                            </td>
                            <td>
                                <?= $it['quantity'] ?>
                                <?= $it['unit'] ?>
                            </td>
                            <td>
                                <?= $it['min_stock'] ?>
                            </td>
                            <td>
                                <?= formatCurrency($it['cost']) ?>
                            </td>
                            <td>
                                <?= $it['supplier_name'] ?? '-' ?>
                            </td>
                            <td>
                                <?php if ($it['quantity'] <= $it['min_stock']) { ?>
                                    <span class="badge bg-danger">
                                        Low Stock
                                    </span>
                                <?php } else { ?>
                                    <span class="badge bg-success">
                                        OK
                                    </span>
                                <?php } ?>
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
                <h5>Add Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required placeholder="Protein Powder">
                </div>
                <div class="col-6">
                    <label class="form-label">Category</label>
                    <input name="category" class="form-control" placeholder="Supplements">
                </div>
                <div class="col-3">
                    <label class="form-label">Qty</label>
                    <input name="quantity" type="number" class="form-control" value="0">
                </div>
                <div class="col-3">
                    <label class="form-label">Unit</label>
                    <input name="unit" type="text" class="form-control" placeholder="pcs">
                </div>
                <div class="col-4">
                    <label class="form-label">Min Stock</label>
                    <input name="min_stock" type="number" class="form-control" value="5" placeholder="5">
                </div>
                <div class="col-4">
                    <label class="form-label">Cost</label>
                    <input name="cost" type="number" step="0.01" class="form-control" placeholder="0.00">
                </div>
                <div class="col-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">None</option>
                        <?php foreach ($suppliers as $s) { ?>
                            <option value="<?= $s['id'] ?>">
                                <?= $s['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
