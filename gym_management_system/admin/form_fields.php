<?php

$page_title = "Form Builder";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $options = !empty($_POST['options'])
        ? json_encode(explode(',', $_POST['options']))
        : null;

    $stmt = $pdo->prepare("
        INSERT INTO form_fields
        (
            form_name,
            field_name,
            field_type,
            is_required,
            options
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
        $_POST['form_name'],
        $_POST['field_name'],
        $_POST['field_type'],
        isset($_POST['is_required']) ? 1 : 0,
        $options
    ]);
}

$stmt = $pdo->query("
    SELECT *
    FROM form_fields
    ORDER BY form_name ASC, id ASC
");

$fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grouped = [];

foreach ($fields as $field) {

    $grouped[$field['form_name']][] = $field;
}

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Form Builder</h4>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Field
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
            <?php foreach ($grouped as $form => $flds) { ?>
                <div class="col-md-6">
                    <div class="card-modern">
                        <div class="card-header d-flex justify-content-between">
                            <span>
                                <?= ucfirst(str_replace('_', ' ', $form)) ?> Form
                            </span>
                            <span class="badge bg-light text-dark border">
                                <?= count($flds) ?> fields
                            </span>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php foreach ($flds as $fld) { ?>
                                <div class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <strong>
                                            <?= $fld['field_name'] ?>
                                        </strong>
                                        <br>
                                        <small style="color:#636e72;">
                                            Type: <?= $fld['field_type'] ?>
                                            <?php if ($fld['is_required']) { ?>
                                                • Required
                                            <?php } ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-light text-dark border">
                                        <?= $fld['field_type'] ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (empty($grouped)) { ?>
                <div class="col-12">
                    <div class="card-modern p-5 text-center">
                        <i class="fas fa-wpforms fa-2x mb-3" style="color:#dfe6e9;"></i>
                        <p>
                            No custom fields yet. Create fields for member registration, leads, etc.
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
                <h5>Add Form Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-6">
                    <label class="form-label">Form Name</label>
                    <select name="form_name" class="form-select">
                        <option value="member">Member Registration</option>
                        <option value="lead">Lead Form</option>
                        <option value="trainer">Trainer Form</option>
                        <option value="staff">Staff Form</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Field Name</label>
                    <input name="field_name" type="text" placeholder="Emergency Contact" required class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">Field Type</label>
                    <select name="field_type" class="form-select">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="date">Date</option>
                        <option value="select">Dropdown</option>
                        <option value="textarea">Textarea</option>
                        <option value="checkbox">Checkbox</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>