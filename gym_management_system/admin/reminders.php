<?php

$page_title = "Reminders";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Add Reminder

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare(
        "INSERT INTO reminders
        (
            title,
            member_id,
            reminder_date,
            type,
            message,
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
        )"
    );

    $stmt->execute([
        $_POST['title'],
        !empty($_POST['member_id']) ? $_POST['member_id'] : null,
        $_POST['reminder_date'],
        $_POST['type'],
        $_POST['message'],
        'pending'
    ]);

    $msg = "Reminder added successfully!";
}

// Get Members

$stmt = $pdo->query(
    "SELECT
        id,
        name
    FROM members
    ORDER BY name ASC"
);

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get Reminders

$stmt = $pdo->query(
    "SELECT
        r.*,
        m.name AS member_name
    FROM reminders r
    LEFT JOIN members m
        ON r.member_id = m.id
    ORDER BY r.reminder_date ASC"
);

$reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Reminders</h4>
        </div>
        <div class="topbar-actions">
            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addModal"
            >
                <i class="fas fa-plus"></i>
                Add Reminder
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
        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Member</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reminders as $r) { ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= $r['title'] ?>
                                    </strong>
                                </td>
                                <td>
                                    <?= $r['member_name'] ?? 'All' ?>
                                </td>
                                <td>
                                    <?= $r['reminder_date'] ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= $r['type'] ?>
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <?= $r['message'] ?>
                                    </small>
                                </td>
                                <td>
                                    <?= getStatusBadge($r['status']) ?>
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
                <h5>Add Reminder</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Title *</label>
                    <input
                        name="title"
                        class="form-control"
                        required
                        placeholder="Membership renewal"
                    >
                </div>
                <div class="col-6">
                    <label class="form-label">Member</label>
                    <select name="member_id" class="form-select">
                        <option value="">All Members / General</option>
                        <?php foreach ($members as $m) { ?>
                            <option value="<?= $m['id'] ?>">
                                <?= $m['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Date</label>
                    <input
                        type="date"
                        name="reminder_date"
                        value="<?= date('Y-m-d') ?>"
                        class="form-control"
                    >
                </div>
                <div class="col-6">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option>payment</option>
                        <option>birthday</option>
                        <option>followup</option>
                        <option>maintenance</option>
                        <option>general</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    Add Reminder
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
