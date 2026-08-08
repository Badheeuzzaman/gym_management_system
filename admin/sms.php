<?php

$page_title = "SMS Center";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Send SMS

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['recipients'] === 'all') {

        $stmt = $pdo->query("
            SELECT phone
            FROM members
            WHERE status = 'active'
              AND phone IS NOT NULL
              AND phone <> ''
        ");

        $recipients = $stmt->fetchAll(PDO::FETCH_COLUMN);

    } else {

        $recipients = [
            $_POST['phone']
        ];
    }

    $stmt = $pdo->prepare("
        INSERT INTO sms_logs
        (
            recipient,
            message,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?
        )
    ");

    foreach ($recipients as $phone) {

        $stmt->execute([
            $phone,
            $_POST['message'],
            'sent'
        ]);
    }

    $msg = "SMS sent to " . count($recipients) . " recipient(s)! (Demo Mode)";
}

// SMS History

$stmt = $pdo->query("
    SELECT *
    FROM sms_logs
    ORDER BY sent_at DESC
    LIMIT 50
");

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Member List

$stmt = $pdo->query("
    SELECT
        id,
        name,
        phone
    FROM members
    WHERE phone IS NOT NULL
      AND phone <> ''
    ORDER BY name ASC
");

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>SMS Center</h4>
        </div>
        <div class="topbar-actions">
            <span class="badge bg-light text-dark border">
                <?= count($logs) ?> Sent
            </span>
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
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card-modern">
                    <div class="card-header">Send SMS</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Recipients</label>
                                <select
                                    name="recipients"
                                    id="recipients"
                                    class="form-select"
                                    onchange="document.getElementById('singlePhone').style.display = this.value == 'single' ? 'block' : 'none'"
                                >
                                    <option value="all">
                                        All Active Members (<?= count($members) ?>)
                                    </option>
                                    <option value="single">
                                        Single Number
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3" id="singlePhone" style="display:none;">
                                <label class="form-label">Phone</label>
                                <input name="phone" class="form-control" placeholder="+9477...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message *</label>
                                <textarea name="message" class="form-control" rows="5" required placeholder="Hi {name}, your membership expires on..."></textarea>
                                <small style="color:#636e72;">Use {name} for personalization</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Templates</label>
                                <select class="form-select" onchange="if(this.value) document.querySelector('[name=message]').value=this.value">
                                    <option value="">Select Template</option>
                                    <option value="Hi {name}, your membership at FlexFit Gym expires in 3 days. Please renew at reception. Thank you!">Expiry Reminder</option>
                                    <option value="Happy Birthday {name}! 🎉 Enjoy 50% off on your next month at FlexFit Gym.">Birthday Wishes</option>
                                    <option value="Dear {name}, we missed you at gym today! Keep up your fitness journey.">Absentee Follow-up</option>
                                    <option value="New offer: Yearly Elite at $299 only this month! Grab now.">Promotional Offer</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-paper-plane"></i> Send SMS</button>
                        </form>

                        <?php if (isset($msg)): ?>
                            <div class="alert alert-success mt-3">
                                <?= $msg ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card-modern">
                    <div class="card-header">SMS Logs</div>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Recipient</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td>
                                            <?= $log['recipient'] ?>
                                        </td>
                                        <td>
                                            <?= $log['message'] ?>
                                        </td>
                                        <td>
                                            <?= $log['status'] ?>
                                        </td>
                                        <td>
                                            <?= $log['sent_at'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<?php require_once '../includes/footer.php'; ?>
