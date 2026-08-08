<?php

$page_title = "Closing Checklist";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] == 'add') {

        $stmt = $pdo->prepare("
            INSERT INTO closing_checklist
            (
                task,
                closing_date
            )
            VALUES
            (
                ?,
                CURDATE()
            )
        ");

        $stmt->execute([
            $_POST['task']
        ]);

    } elseif ($_POST['action'] == 'toggle') {

        $stmt = $pdo->prepare("
            UPDATE closing_checklist
            SET
                is_completed = !is_completed,
                completed_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['id']
        ]);
    }
}

if (isset($_GET['delete'])) {

    $stmt = $pdo->prepare("
        DELETE FROM closing_checklist
        WHERE id = ?
    ");

    $stmt->execute([
        $_GET['delete']
    ]);

    header("Location: closing_checklist.php");
    exit;
}

$stmt = $pdo->query("
    SELECT *
    FROM closing_checklist
    WHERE closing_date = CURDATE()
    ORDER BY id DESC
");

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($tasks)) {

    $defaults = [
        "Clean equipment",
        "Turn off AC & lights",
        "Lock doors",
        "Cash drawer count",
        "Update attendance",
        "Send day report"
    ];

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO closing_checklist
        (
            task,
            closing_date
        )
        VALUES
        (
            ?,
            CURDATE()
        )
    ");

    foreach ($defaults as $task) {

        $stmt->execute([
            $task
        ]);
    }

    $stmt = $pdo->query("
        SELECT *
        FROM closing_checklist
        WHERE closing_date = CURDATE()
        ORDER BY id DESC
    ");

    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$completed = count(array_filter(
    $tasks,
    fn($task) => $task['is_completed']
));

$percent = count($tasks)
    ? round(($completed / count($tasks)) * 100)
    : 0;

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Closing Checklist 
                <span class="badge bg-success ms-2"><?= $percent ?>% Done</span>
            </h4>
        </div>
        <div class="topbar-actions">
            <span style="font-size:13px;color:#636e72;"><?= date('l, M d') ?></span>
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
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-modern">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Today's Tasks (<?= $completed ?>/<?= count($tasks) ?>)</span>
                    <div class="progress" style="width:120px;height:8px;">
                        <div class="progress-bar bg-success" style="width:<?= $percent ?>%"></div>
                    </div>
                </div>
                <div class="list-group list-group-flush">
<?php foreach ($tasks as $t) { ?>
    <div class="list-group-item d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <form method="post" class="d-inline">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button
                    type="submit"
                    class="btn btn-sm <?= $t['is_completed'] ? 'btn-success' : 'btn-light border' ?>"
                    style="width:32px;height:32px;border-radius:50%;"
                >
                    <i class="fas fa-<?= $t['is_completed'] ? 'check' : 'minus' ?>"></i>
                </button>
            </form>
            <span
                style="<?= $t['is_completed'] ? 'text-decoration:line-through;color:#636e72;' : '' ?>"
            >
                <?= $t['task'] ?>
            </span>
        </div>
        <div>
            <small style="color:#636e72;">
                <?= $t['completed_at'] ? date('H:i', strtotime($t['completed_at'])) : '' ?>
            </small>
            <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-light ms-2 text-danger">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    </div>
<?php } ?>
</div>
<div class="card-body">
    <form method="post" class="input-group">
        <input type="hidden" name="action" value="add">
        <input name="task" class="form-control" placeholder="Add new task... e.g. Check water dispenser" required>
        <button class="btn btn-primary">Add Task</button>
    </form>
</div>
</div>
<?php if ($percent == 100) { ?>
    <div class="alert alert-success mt-4 text-center">
        <i class="fas fa-check-circle fa-2x mb-2"></i>
        <h5>All tasks completed! 🎉</h5>
        <p>You can now close the gym safely. Good job!</p>
    </div>
<?php } ?>
</div>
</div>
</div>
</div>
<?php require_once '../includes/footer.php'; ?>
