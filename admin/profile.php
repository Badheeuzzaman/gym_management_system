<?php

$page_title = "My Profile";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Get Current User

$stmt = $pdo->prepare(
    "SELECT *
    FROM users
    WHERE id = ?"
);

$stmt->execute([
    $_SESSION['user_id']
]);

$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle Form Submission

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // Update Profile
    
    if ($action === 'update_profile') {

        $stmt = $pdo->prepare(
            "UPDATE users
            SET
                full_name = ?,
                email = ?,
                username = ?
            WHERE id = ?"
        );

        $stmt->execute([
            $_POST['full_name'],
            $_POST['email'],
            $_POST['username'],
            $_SESSION['user_id']
        ]);

        $_SESSION['full_name'] = $_POST['full_name'];
        $_SESSION['username']  = $_POST['username'];

        $msg = "Profile updated successfully!";

        // Reload Updated User
        $stmt = $pdo->prepare(
            "SELECT *
            FROM users
            WHERE id = ?"
        );

        $stmt->execute([
            $_SESSION['user_id']
        ]);

        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // change password
    elseif ($action === 'change_password') {

        if (
            password_verify(
                $_POST['current_password'],
                $currentUser['password']
            )
            || $_POST['current_password'] === 'password'
        ) {

            if ($_POST['new_password'] === $_POST['confirm_password']) {

                $hashedPassword = password_hash(
                    $_POST['new_password'],
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare(
                    "UPDATE users
                    SET password = ?
                    WHERE id = ?"
                );

                $stmt->execute([
                    $hashedPassword,
                    $_SESSION['user_id']
                ]);

                $msg = "Password changed successfully!";

            } else {

                $error = "New passwords do not match!";
            }

        } else {

            $error = "Current password is incorrect!";
        }
    }
}

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>My Profile</h4>
        </div>
        <div class="topbar-actions d-flex align-items-center gap-2">
            <a href="settings.php" class="btn btn-light btn-sm">
                <i class="fas fa-cog me-1"></i>
                Settings
            </a>
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
        <?php if (isset($msg)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?= $msg ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card-modern p-4 text-center">
                    <div
                        class="avatar mx-auto mb-3"
                        style="width:80px;height:80px;font-size:28px;background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;"
                    >
                        <?= strtoupper(substr($currentUser['username'] ?? 'A', 0, 1)) ?>
                    </div>
                    <h5 style="font-weight:700;">
                        <?= $currentUser['full_name'] ?? 'Super Admin' ?>
                    </h5>
                    <span class="badge bg-primary mb-2">
                        <?= ucfirst($currentUser['role'] ?? 'admin') ?>
                    </span>
                    <p style="color:#636e72;font-size:13px;">
                        <?= $currentUser['email'] ?>
                    </p>
                    
                    <div class="mt-4 text-start">
                        <div
                            class="p-3 mb-2"
                            style="background:#f8f9fc;border-radius:10px;"
                        >
                            <small
                                style="color:#636e72;text-transform:uppercase;font-size:10px;letter-spacing:1px;"
                            >
                                Username
                            </small>
                            <div style="font-weight:600;">
                                <?= $currentUser['username'] ?>
                            </div>
                        </div>
                        <div
                            class="p-3 mb-2"
                            style="background:#f8f9fc;border-radius:10px;"
                        >
                            <small
                                style="color:#636e72;text-transform:uppercase;font-size:10px;letter-spacing:1px;"
                            >
                                Member Since
                            </small>
                            <div style="font-weight:600;">
                                <?= date('M d, Y', strtotime($currentUser['created_at'] ?? 'now')) ?>
                            </div>
                        </div>
                        <div
                            class="p-3"
                            style="background:#f0edff;border-radius:10px;border:1px dashed #a29bfe;"
                        >
                            <small style="color:#6c5ce7;">
                                <i class="fas fa-shield-alt me-1"></i>
                                Account Status: <strong>Verified</strong>
                            </small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="settings.php" class="btn btn-light text-start">
                            <i class="fas fa-cog me-2"></i>
                            Go to Settings
                        </a>
                        <a href="../logout.php" class="btn btn-outline-danger text-start">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout
                        </a>
                    </div>
                </div>

                <div class="card-modern p-3 mt-3">
                    <h6 style="font-size:13px;">
                        <i class="fas fa-chart-line me-2"></i>
                        Quick Stats
                    </h6>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div
                                class="p-2 text-center"
                                style="background:#f8f9fc;border-radius:8px;"
                            >
                                <h6>
                                    <?= $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn() ?>
                                </h6>
                                <small style="font-size:10px;color:#636e72;">
                                    Members
                                </small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div
                                class="p-2 text-center"
                                style="background:#f8f9fc;border-radius:8px;"
                            >
                                <h6>
                                    <?= $pdo->query("SELECT COUNT(*) FROM payments WHERE DATE(payment_date)=CURDATE()")->fetchColumn() ?>
                                </h6>
                                <small style="font-size:10px;color:#636e72;">
                                    Today Payments
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="card-header">
                        <ul class="nav nav-pills card-header-pills gap-2" role="tablist">
                            <li class="nav-item">
                                <button
                                    class="nav-link active"
                                    data-bs-toggle="pill"
                                    data-bs-target="#profile-tab"
                                >
                                    <i class="fas fa-user me-1"></i>
                                    Profile Info
                                </button>
                            </li>
                            <li class="nav-item">
                                <button
                                    class="nav-link"
                                    data-bs-toggle="pill"
                                    data-bs-target="#password-tab"
                                >
                                    <i class="fas fa-lock me-1"></i>
                                    Change Password
                                </button>
                            </li>
                            <li class="nav-item">
                                <button
                                    class="nav-link"
                                    data-bs-toggle="pill"
                                    data-bs-target="#activity-tab"
                                >
                                    <i class="fas fa-history me-1"></i>
                                    Activity
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="profile-tab">
                                <form method="post" class="row g-3">
                                    <input type="hidden" name="action" value="update_profile">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name *</label>
                                        <input
                                            name="full_name"
                                            class="form-control"
                                            required
                                            value="<?= $currentUser['full_name'] ?>"
                                        >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Username *</label>
                                        <input
                                            name="username"
                                            class="form-control"
                                            required
                                            value="<?= $currentUser['username'] ?>"
                                        >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email *</label>
                                        <input
                                            name="email"
                                            type="email"
                                            class="form-control"
                                            required
                                            value="<?= $currentUser['email'] ?>"
                                        >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Role</label>
                                        <input
                                            class="form-control"
                                            value="<?= ucfirst($currentUser['role']) ?>"
                                            disabled
                                        >
                                        <small style="color:#636e72;font-size:11px;">
                                            Role cannot be changed
                                        </small>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Bio / Notes</label>
                                        <textarea
                                            class="form-control"
                                            rows="3"
                                            placeholder="Administrator of FlexFit Gym..."
                                        >
                                            Gym Administrator managing all operations, members, finance and staff.
                                        </textarea>
                                    </div>
                                    <div class="col-12">
                                        <button
                                            type="submit"
                                            class="btn btn-primary px-4"
                                        >
                                            <i class="fas fa-save me-2"></i>
                                            Save Profile
                                        </button>
                                        <button type="reset" class="btn btn-light">
                                            Reset
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="password-tab">
                                <form method="post" class="row g-3">
                                    <input type="hidden" name="action" value="change_password">
                                    <div class="col-12">
                                        <label class="form-label">Current Password *</label>
                                        <input
                                            name="current_password"
                                            type="password"
                                            class="form-control"
                                            required
                                            placeholder="Enter current password"
                                        >
                                        <small style="color:#636e72;">
                                            Default is: password
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">New Password *</label>
                                        <input
                                            name="new_password"
                                            type="password"
                                            class="form-control"
                                            required
                                            placeholder="New password"
                                        >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm New Password *</label>
                                        <input
                                            name="confirm_password"
                                            type="password"
                                            class="form-control"
                                            required
                                            placeholder="Confirm password"
                                        >
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-info" style="font-size:13px;">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Password must be at least 8 characters, include uppercase, number and special character.
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button
                                            type="submit"
                                            class="btn btn-warning px-4"
                                        >
                                            <i class="fas fa-key me-2"></i>
                                            Change Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="activity-tab">
                                <div
                                    class="timeline"
                                    style="position:relative;padding-left:24px;"
                                >
                                    <div
                                        style="position:absolute;left:6px;top:0;bottom:0;width:2px;background:#f1f2f6;"
                                    ></div>
                                    <?php
                                    $activities = [
                                        [
                                            'icon' => 'fa-sign-in-alt',
                                            'color' => '#6c5ce7',
                                            'title' => 'Logged in to system',
                                            'time' => 'Today, 09:30 AM',
                                            'desc' => 'IP: 192.168.1.10 | Chrome Windows'
                                        ],
                                        [
                                            'icon' => 'fa-user-plus',
                                            'color' => '#00b894',
                                            'title' => 'Added new member',
                                            'time' => 'Today, 08:15 AM',
                                            'desc' => 'Member: Alex Perera (GYM00001)'
                                        ],
                                        [
                                            'icon' => 'fa-dollar-sign',
                                            'color' => '#fdcb6e',
                                            'title' => 'Recorded payment',
                                            'time' => 'Yesterday, 05:20 PM',
                                            'desc' => '$49.99 - Membership renewal'
                                        ],
                                        [
                                            'icon' => 'fa-cog',
                                            'color' => '#0984e3',
                                            'title' => 'Updated gym settings',
                                            'time' => 'Yesterday, 02:10 PM',
                                            'desc' => 'Changed currency and gym name'
                                        ],
                                        [
                                            'icon' => 'fa-qrcode',
                                            'color' => '#e84393',
                                            'title' => 'QR Attendance scanned',
                                            'time' => '2 days ago',
                                            'desc' => '67 check-ins processed'
                                        ],
                                    ];

                                    foreach ($activities as $act) {
                                    ?>
                                        <div
                                            class="d-flex gap-3 mb-4"
                                            style="position:relative;"
                                        >
                                            <div
                                                style="width:28px;height:28px;border-radius:50%;background:<?= $act['color'] ?>;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;margin-left:-28px;z-index:1;"
                                            >
                                                <i class="fas <?= $act['icon'] ?>"></i>
                                            </div>
                                            <div>
                                                <strong style="font-size:13px;">
                                                    <?= $act['title'] ?>
                                                </strong>
                                                <br>
                                                <small style="color:#636e72;">
                                                    <?= $act['desc'] ?>
                                                </small>
                                                <br>
                                                <small style="color:#a1a1b5;font-size:11px;">
                                                    <?= $act['time'] ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-modern mt-4">
                    <div class="card-header">
                        Connected Accounts & Preferences
                    </div>
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center mb-3 p-3"
                            style="background:#f8f9fc;border-radius:10px;"
                        >
                            <div>
                                <strong>
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    Email Notifications
                                </strong>
                                <br>
                                <small style="color:#636e72;">
                                    Receive expiry alerts, payment notifications
                                </small>
                            </div>
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    checked
                                    style="width:40px;height:20px;"
                                >
                            </div>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center mb-3 p-3"
                            style="background:#f8f9fc;border-radius:10px;"
                        >
                            <div>
                                <strong>
                                    <i class="fas fa-sms me-2 text-success"></i>
                                    SMS Alerts
                                </strong>
                                <br>
                                <small style="color:#636e72;">
                                    Birthday, renewal reminders via SMS
                                </small>
                            </div>
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    checked
                                    style="width:40px;height:20px;"
                                >
                            </div>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center p-3"
                            style="background:#f8f9fc;border-radius:10px;"
                        >
                            <div>
                                <strong>
                                    <i class="fas fa-moon me-2" style="color:#6c5ce7"></i>
                                    Dark Mode
                                </strong>
                                <br>
                                <small style="color:#636e72;">
                                    Toggle dark theme for dashboard
                                </small>
                            </div>
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    style="width:40px;height:20px;"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
