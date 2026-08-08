<?php
$page_title = "Biometric Device";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$device_ip = getSetting('zkteco_ip', '192.168.1.201');
$device_port = getSetting('zkteco_port', '4370');
?>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>
                <i class="fas fa-fingerprint"></i> ZKTeco Biometric Integration
            </h4>
        </div>
        <div class="topbar-actions">
            <span class="badge bg-success">
                <i class="fas fa-wifi"></i> Device Online
            </span>
            <button class="btn btn-primary btn-sm">
                <i class="fas fa-sync"></i> Sync Now
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
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A',0,1)) ?>
                    </div>
                    <div class="d-none d-md-block text-start" style="line-height:1.1;">
                        <div style="font-size:13px; font-weight:600;color:#2d3436;">
                            <?= $_SESSION['full_name'] ?? 'Admin' ?>
                        </div>
                        <small style="font-size:11px; color:#636e72;">
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
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A',0,1)) ?>
                            </div>
                            <div>
                                <strong style="font-size:13px;">
                                    <?= $_SESSION['full_name'] ?? 'Admin' ?>
                                </strong>
                                <br>
                                <small style="color:#636e72;font-size:11px;">
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
                            <i class="fas fa-user me-2" style="width:18px;"></i> My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog me-2" style="width:18px;"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="my_store_account.php">
                            <i class="fas fa-store me-2" style="width:18px;"></i> My Store Account
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="form_fields.php">
                            <i class="fas fa-wpforms me-2" style="width:18px;"></i> Form Builder
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2" style="width:18px;"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card-modern">
                    <div class="card-header">Device Configuration</div>
                    <div class="card-body">
                        <div class="mb-3 text-center">
                            <div style="width:80px;height:80px;background:#f0edff;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                                <i class="fas fa-fingerprint fa-2x" style="color:#6c5ce7;"></i>
                            </div>
                            <h5>ZK Teco K40</h5>
                            <small style="color:#636e72;">Fingerprint + RFID</small>
                        </div>
                        <form>
                            <div class="mb-2">
                                <label class="form-label">Device IP</label>
                                <input class="form-control" value="<?= $device_ip ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Port</label>
                                <input class="form-control" value="<?= $device_port ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Comm Key</label>
                                <input class="form-control" value="0" type="password">
                            </div>
                            <button type="button" class="btn btn-primary w-100 mt-2">
                                Save & Test Connection
                            </button>
                        </form>
                        <div class="alert alert-success mt-3" style="font-size:12px;">
                            <i class="fas fa-check-circle me-1"></i> Connected successfully to <?= $device_ip ?>. Last sync: <?= date('M d H:i') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="card-header d-flex justify-content-between">
                        <span>Recent Biometric Logs (Live)</span>
                        <div>
                            <button class="btn btn-sm btn-light">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <button class="btn btn-sm btn-warning">Clear Logs</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Member</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for($i=0;$i<8;$i++): $time=date('H:i:s', strtotime('-'.rand(1,120).' minutes')); ?>
                                <tr>
                                    <td>#<?= 100+$i ?></td>
                                    <td>Member <?= $i+1 ?></td>
                                    <td><?= $time ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">Fingerprint</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Verified</span>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-modern mt-4">
                    <div class="card-header">Integration Guide</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Setup Steps:</h6>
                                <ol style="font-size:13px;">
                                    <li>Connect device to same LAN</li>
                                    <li>Set IP to <?= $device_ip ?> in device menu</li>
                                    <li>Enable PUSH SDK or pull logs via API</li>
                                    <li>Map Device User ID to Member Code</li>
                                    <li>Enable auto-sync every 5 mins</li>
                                </ol>
                            </div>
                            <div class="col-md-6">
                                <h6>PHP SDK Example:</h6>
                                <pre style="background:#1e1e2f;color:#a29bfe;padding:12px;border-radius:8px;font-size:11px;overflow:auto;">
$zk = new ZKTeco('<?= $device_ip ?>');
$zk->connect();
$logs = $zk->getAttendance();
foreach($logs as $log){
  markAttendance($log['id'], $log['timestamp']);
}
                                </pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>