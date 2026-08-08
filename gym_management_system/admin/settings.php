<?php

$page_title = "Settings";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Save Settings

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['settings']) && is_array($_POST['settings'])) {

        $stmt = $pdo->prepare(
            "INSERT INTO settings
            (
                setting_key,
                setting_value
            )
            VALUES
            (
                ?,
                ?
            )
            ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value)"
        );

        foreach ($_POST['settings'] as $key => $value) {

            $stmt->execute([
                $key,
                $value
            ]);
        }

        $msg = "Settings updated successfully!";
    }
}

// Load Settings

$stmt = $pdo->query(
    "SELECT
        setting_key,
        setting_value
    FROM settings
    ORDER BY setting_key ASC"
);

$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Settings</h4>
        </div>
        <div class="topbar-actions d-flex align-items-center gap-2">
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
                                    <?= $_SESSION['username'] ?? 'admin' ?> • Admin
                                </small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="profile.php">
                            <i
                                class="fas fa-user me-2"
                                style="width:18px;"
                            ></i>
                            My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item active" href="settings.php">
                            <i
                                class="fas fa-cog me-2"
                                style="width:18px;"
                            ></i>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="my_store_account.php">
                            <i
                                class="fas fa-store me-2"
                                style="width:18px;"
                            ></i>
                            My Store Account
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="../logout.php">
                            <i
                                class="fas fa-sign-out-alt me-2"
                                style="width:18px;"
                            ></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <?php if (isset($msg)): ?>
            <div class="alert alert-success">
                <?= $msg ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-modern">
                        <div class="card-header">General Settings</div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Gym Name</label>
                                <input
                                    name="settings[gym_name]"
                                    value="<?= $all['gym_name'] ?? '' ?>"
                                    class="form-control"
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Currency Symbol</label>
                                <input
                                    name="settings[currency]"
                                    value="<?= $all['currency'] ?? '$' ?>"
                                    class="form-control"
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input
                                    name="settings[gym_email]"
                                    value="<?= $all['gym_email'] ?? '' ?>"
                                    class="form-control"
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input
                                    name="settings[gym_phone]"
                                    value="<?= $all['gym_phone'] ?? '' ?>"
                                    class="form-control"
                                >
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <input
                                    name="settings[gym_address]"
                                    value="<?= $all['gym_address'] ?? '' ?>"
                                    class="form-control"
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SMS API Key</label>
                                <input
                                    name="settings[sms_api_key]"
                                    value="<?= $all['sms_api_key'] ?? '' ?>"
                                    class="form-control"
                                >
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ZKTeco IP</label>
                                <input
                                    name="settings[zkteco_ip]"
                                    value="<?= $all['zkteco_ip'] ?? '192.168.1.201' ?>"
                                    class="form-control"
                                >
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Port</label>
                                <input
                                    name="settings[zkteco_port]"
                                    value="<?= $all['zkteco_port'] ?? '4370' ?>"
                                    class="form-control"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-modern">
                        <div class="card-header">System Info</div>
                        <div class="card-body">
                            <ul style="font-size:13px;color:#636e72;list-style:none;padding:0;">
                                <li class="mb-2">
                                    <strong>Version:</strong> 2.0.1
                                </li>
                                <li class="mb-2">
                                    <strong>PHP:</strong> <?= PHP_VERSION ?>
                                </li>
                                <li class="mb-2">
                                    <strong>DB:</strong> MySQL
                                </li>
                                <li class="mb-2">
                                    <strong>Modules:</strong> 40+
                                </li>
                                <li>
                                    <strong>Last Backup:</strong> Never
                                </li>
                            </ul>
                            <button
                                type="button"
                                class="btn btn-light w-100 mt-3"
                            >
                                <i class="fas fa-database"></i> Backup Database
                            </button>
                        </div>
                    </div>
                    <div class="card-modern mt-3 p-3">
                        <h6>Quick Links</h6>
                        <div class="d-grid gap-2 mt-2">
                            <a href="form_fields.php" class="btn btn-light text-start">
                                <i class="fas fa-wpforms me-2"></i> Form Builder
                            </a>
                            <a href="banks.php" class="btn btn-light text-start">
                                <i class="fas fa-university me-2"></i> Bank Accounts
                            </a>
                            <a href="my_store_account.php" class="btn btn-light text-start">
                                <i class="fas fa-store me-2"></i> Store Account
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-5 py-2">
                        Save All Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
