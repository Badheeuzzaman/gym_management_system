<?php
$page_title = "Attendance";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] == 'mark') {
    $stmt = $pdo->prepare("
        INSERT INTO attendance (member_id, date, check_in, method)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            check_in = VALUES(check_in),
            method = VALUES(method)
    ");

    $stmt->execute([
        $_POST['member_id'],
        $_POST['date'],
        $_POST['check_in'],
        $_POST['method']
    ]);
}

$date = $_GET['date'] ?? date('Y-m-d');

$members = $pdo->query("
    SELECT id, name, member_code
    FROM members
    WHERE status='active'
")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT a.*, m.name, m.member_code
    FROM attendance a
    JOIN members m ON a.member_id = m.id
    WHERE a.date = ?
    ORDER BY a.check_in DESC
");

$stmt->execute([$date]);

$attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalToday = count($attendance);
?>
<div class="main-content">
  <div class="topbar">
    <div class="d-flex gap-3 align-items-center">
      <button class="btn btn-light d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
      <h4>Attendance - <?= date('M d, Y', strtotime($date)) ?></h4>
    </div>
    <div class="topbar-actions">
      <input type="date" class="form-control" value="<?= $date ?>" onchange="location.href='?date='+this.value">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#markModal">
        <i class="fas fa-plus"></i> Mark
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
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card-modern p-3 d-flex justify-content-between align-items-center">
      <div>
        <small style="color:#636e72;">Present Today</small>
        <h3><?= $totalToday ?></h3>
      </div>
      <div class="stat-icon" style="background:#e8f8f5;color:#00b894;">
        <i class="fas fa-user-check"></i>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-modern p-3 d-flex justify-content-between align-items-center">
      <div>
        <small style="color:#636e72;">Total Members</small>
        <h3><?= count($members) ?></h3>
      </div>
      <div class="stat-icon" style="background:#f0edff;color:#6c5ce7;">
        <i class="fas fa-users"></i>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card-modern p-3 d-flex justify-content-between align-items-center">
      <div>
        <small style="color:#636e72;">Attendance %</small>
        <h3>
          <?php if (count($members)) { ?>
            <?= round($totalToday / count($members) * 100) ?>
          <?php } else { ?>
            0
          <?php } ?>
          %
        </h3>
      </div>
      <div class="stat-icon" style="background:#fef9e7;color:#f1c40f;">
        <i class="fas fa-chart-line"></i>
      </div>
    </div>
  </div>
</div>

<div class="card-modern">
  <div class="table-responsive">
    <table class="table table-modern mb-0">
      <thead>
        <tr>
          <th>Member</th>
          <th>Code</th>
          <th>Check-In</th>
          <th>Check-Out</th>
          <th>Method</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($attendance as $a) { ?>
          <tr>
            <td>
              <strong>
                <?= $a['name'] ?>
              </strong>
            </td>
            <td>
              <code>
                <?= $a['member_code'] ?>
              </code>
            </td>
            <td>
              <span class="badge bg-success">
                <?= $a['check_in'] ?>
              </span>
            </td>
            <td>
              <?php if (!empty($a['check_out'])) { ?>
                <?= $a['check_out'] ?>
              <?php } else { ?>
                <span class="badge bg-light text-dark">
                  Still In
                </span>
              <?php } ?>
            </td>
            <td>
              <span class="badge bg-light text-dark border">
                <?= ucfirst($a['method']) ?>
              </span>
            </td>
            <td>
              <?= getStatusBadge('present') ?>
            </td>
          </tr>
        <?php } ?>
        <?php if (empty($attendance)) { ?>
          <tr>
            <td colspan="6" class="text-center p-4">
              No attendance marked for this date.
              <a href="attendance_scan.php">Use QR Scanner</a> for quick check-in.
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
  <div class="modal fade" id="markModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
            <input type="hidden" name="action" value="mark">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Mark Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Member *</label>

                        <select name="member_id" class="form-select" required>

                            <option value="">Select</option>

                            <?php foreach($members as $m): ?>

                                <option value="<?= $m['id'] ?>">
                                    <?= $m['member_code'] ?> - <?= $m['name'] ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>

                        <input
                            type="date"
                            name="date"
                            value="<?= date('Y-m-d') ?>"
                            class="form-control">

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Check In</label>

                        <input
                            type="time"
                            name="check_in"
                            value="<?= date('H:i') ?>"
                            class="form-control">

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Method</label>

                        <select name="method" class="form-select">

                            <option value="manual">Manual</option>

                            <option value="qr">QR Code</option>

                            <option value="biometric">Biometric</option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary">
                        Mark Present
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>