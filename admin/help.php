<?php

$page_title = "Help & Docs";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Help & Documentation</h4>
        </div>
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
    <div class="content-wrapper">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-modern p-4">
                    <h4>FlexFit Gym Management System - User Guide</h4>
                    <p style="color:#636e72;">Complete guide to manage your gym efficiently.</p>
                    <h5 class="mt-4">Quick Start</h5>
                    <ol style="font-size:14px;">
                        <li>Import <code>sql/schema.sql</code> into MySQL (database: gym_management)</li>
                        <li>Configure <code>config/database.php</code> with your DB credentials</li>
                        <li>Login with admin / password</li>
                        <li>Add Plans, Trainers, Members</li>
                        <li>Start marking attendance via QR or Biometric</li>
                    </ol>
                    <h5 class="mt-4">Modules Overview</h5>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="p-3" style="background:#f8f9fc;border-radius:10px;">
                                <strong>Members</strong><br>
                                <small>Add, edit, track memberships, assign workouts, birthdays, attendance via QR/ZKTeco</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3" style="background:#f8f9fc;border-radius:10px;">
                                <strong>Finance</strong><br>
                                <small>Payments, Expenses, Day Closing, Cash & Bank transfers, Checklist</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3" style="background:#f8f9fc;border-radius:10px;">
                                <strong>Store (POS)</strong><br>
                                <small>Sell supplements, inventory, sales history, approval for online payments</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3" style="background:#f8f9fc;border-radius:10px;">
                                <strong>Gym</strong><br>
                                <small>Trainers, Classes, Workouts, Diet Templates, Plans</small>
                            </div>
                        </div>
                    </div>
                    <h5 class="mt-4">Biometric Setup</h5>
                    <pre style="background:#1e1e2f;color:#a29bfe;padding:14px;border-radius:10px;font-size:12px;">Device: ZKTeco K40 / iClock
SDK: PHP-ZKLib
Steps: Connect device to LAN -> Set IP 192.168.1.201 -> Enable in Settings -> Use zkteco.php to Sync</pre>
<h5 class="mt-4">
    QR Attendance
</h5>
<p style="font-size:13px;color:#636e72;">
    Each member gets a QR code (member_code). Print member cards with QR. Scan via attendance_scan.php page. Works offline too, requires HTTPS for camera.
</p>
</div>
</div>
<div class="col-lg-4">
    <div class="card-modern p-4">
        <h6>Support Contacts</h6>
        <p style="font-size:13px;color:#636e72;">Need help? Contact our team.</p>
        <div class="d-grid gap-2 mt-3">
            <div class="p-3" style="background:#f0edff;border-radius:10px;">
                <i class="fas fa-envelope text-primary"></i> support@flexfit.com
            </div>
            <div class="p-3" style="background:#e8f8f5;border-radius:10px;">
                <i class="fas fa-phone text-success"></i> +94 77 123 4567
            </div>
        </div>
        <h6 class="mt-4">Changelog</h6>
        <ul style="font-size:12px;color:#636e72;">
            <li>v2.0 - Full 40 modules, POS, Biometric, QR</li>
            <li>v1.5 - Inventory, Staff HR</li>
            <li>v1.0 - Basic Members & Payments</li>
        </ul>
        <div class="mt-4">
            <h6>License</h6>
            <p style="font-size:12px;color:#636e72;">Single gym license. Lifetime updates. Commercial use allowed for your gym only.</p>
        </div>
    </div>
</div>
</div>
</div>
</div>
<?php require_once '../includes/footer.php'; ?>
