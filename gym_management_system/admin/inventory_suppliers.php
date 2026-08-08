<?php

$page_title = "Suppliers";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO suppliers
        (
            name,
            contact,
            email,
            address
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
        $_POST['contact'],
        $_POST['email'],
        $_POST['address']
    ]);
}

$stmt = $pdo->query("
    SELECT *
    FROM suppliers
    ORDER BY id DESC
");

$sup = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Suppliers</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Supplier
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
            <?php foreach ($sup as $s) { ?>
            <div class="col-md-4">
                <div class="card-modern p-4">
                    <h6>
                        <?= $s['name'] ?>
                    </h6>
                    <small style="color:#636e72;">
                        <i class="fas fa-phone"></i>
                        <?= $s['contact'] ?>
                        |
                        <?= $s['email'] ?>
                    </small>
                    <p style="font-size:12px;color:#636e72;" class="mt-2">
                        <?= $s['address'] ?>
                    </p>
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
                <h5>Add Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required />
                </div>
                <div class="col-6">
                    <label class="form-label">Contact</label>
                    <input name="contact" class="form-control" />
                </div>
                <div class="col-6">
                    <label class="form-label">Email</label>
                    <input name="email" class="form-control" />
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Supplier</button>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
