<?php

$page_title = "Members";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';


// =========================================
// Handle Add / Edit Member
// =========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {

        $memberCode = generateMemberCode();

        $stmt = $pdo->prepare("
            INSERT INTO members
            (
                member_code,
                name,
                email,
                phone,
                gender,
                dob,
                join_date,
                status,
                address,
                goal,
                height,
                weight
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
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
            $memberCode,
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['gender'],
            !empty($_POST['dob']) ? $_POST['dob'] : null,
            $_POST['join_date'],
            $_POST['status'],
            $_POST['address'],
            $_POST['goal'],
            !empty($_POST['height']) ? $_POST['height'] : null,
            !empty($_POST['weight']) ? $_POST['weight'] : null
        ]);

        $msg = "Member added successfully!";

    } elseif ($action === 'edit') {

        $stmt = $pdo->prepare("
            UPDATE members
            SET
                name = ?,
                email = ?,
                phone = ?,
                gender = ?,
                dob = ?,
                join_date = ?,
                status = ?,
                address = ?,
                goal = ?,
                height = ?,
                weight = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['gender'],
            !empty($_POST['dob']) ? $_POST['dob'] : null,
            $_POST['join_date'],
            $_POST['status'],
            $_POST['address'],
            $_POST['goal'],
            !empty($_POST['height']) ? $_POST['height'] : null,
            !empty($_POST['weight']) ? $_POST['weight'] : null,
            $_POST['id']
        ]);

        $msg = "Member updated successfully!";
    }
}


// =========================================
// Delete Member
// =========================================

if (isset($_GET['delete'])) {

    $stmt = $pdo->prepare("
        DELETE FROM members
        WHERE id = ?
    ");

    $stmt->execute([
        $_GET['delete']
    ]);

    header("Location: members.php");
    exit;
}


// =========================================
// Get All Members
// =========================================

$stmt = $pdo->query("
    SELECT *
    FROM members
    ORDER BY id DESC
");

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <h4>Members <span class="badge bg-light text-dark border ms-2"><?= count($members) ?></span></h4>
        </div>
        <div class="topbar-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search members...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus me-1"></i> Add Member
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
        <?php if (isset($msg)) { ?>
            <div class="alert alert-success">
                <?= $msg ?>
            </div>
        <?php } ?>
        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern table-hover mb-0" id="membersTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Member</th>
                            <th>Contact</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th>Goal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $m) { ?>
                        <tr>
                            <td>
                                <code>
                                    <?= $m['member_code'] ?>
                                </code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar">
                                        <?= strtoupper(substr($m['name'], 0, 2)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:600;">
                                            <?= $m['name'] ?>
                                        </div>
                                        <small style="color:#636e72;">
                                            <?= $m['gender'] ?>
                                            |
                                            <?php if (!empty($m['dob'])) { ?>
                                                <?= date('M d, Y', strtotime($m['dob'])) ?>
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $m['phone'] ?>
                                </div>
                                <small>
                                    <?= $m['email'] ?>
                                </small>
                            </td>
                            <td>
                                <?= date('M d, Y', strtotime($m['join_date'])) ?>
                            </td>
                            <td>
                                <?= getStatusBadge($m['status']) ?>
                            </td>
                            <td>
                                <?= $m['goal'] ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button
                                        class="btn btn-light editBtn"
                                        data-data='<?= json_encode($m) ?>'
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a
                                        href="attendance.php?member_id=<?= $m['id'] ?>"
                                        class="btn btn-light"
                                    >
                                        <i class="fas fa-calendar"></i>
                                    </a>
                                    <a
                                        href="?delete=<?= $m['id'] ?>"
                                        onclick="return confirm('Delete?')"
                                        class="btn btn-light text-danger"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add New Member</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body row g-3">
        <input type="hidden" name="action" value="add">
        <div class="col-md-6">
            <label class="form-label">Full Name *</label>
            <input name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone *</label>
            <input name="phone" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">DOB</label>
            <input name="dob" type="date" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Join Date</label>
            <input name="join_date" type="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Goal</label>
            <input name="goal" class="form-control" placeholder="Weight Loss">
        </div>
        <div class="col-md-3">
            <label class="form-label">Height (cm)</label>
            <input name="height" type="number" step="0.1" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Weight (kg)</label>
            <input name="weight" type="number" step="0.1" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input name="address" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Member</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="edit_id">
        <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input name="name" id="edit_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input name="phone" id="edit_phone" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input name="email" id="edit_email" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select name="gender" id="edit_gender" class="form-select">
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">DOB</label>
            <input name="dob" id="edit_dob" type="date" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Join Date</label>
            <input name="join_date" id="edit_join" type="date" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="expired">Expired</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Goal</label>
            <input name="goal" id="edit_goal" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Height</label>
            <input name="height" id="edit_height" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Weight</label>
            <input name="weight" id="edit_weight" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input name="address" id="edit_address" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    filterTable('searchInput', 'membersTable');

    var editButtons = document.querySelectorAll('.editBtn');

    editButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var data = JSON.parse(btn.getAttribute('data-data'));

            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_phone').value = data.phone;
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_gender').value = data.gender;
            document.getElementById('edit_dob').value = data.dob || '';
            document.getElementById('edit_join').value = data.join_date;
            document.getElementById('edit_status').value = data.status;
            document.getElementById('edit_goal').value = data.goal || '';
            document.getElementById('edit_height').value = data.height || '';
            document.getElementById('edit_weight').value = data.weight || '';
            document.getElementById('edit_address').value = data.address || '';

            var editModalElement = document.getElementById('editModal');
            var editModal = new bootstrap.Modal(editModalElement);
            editModal.show();
        });
    });
});
</script>
<?php require_once '../includes/footer.php'; ?>
