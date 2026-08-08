<?php
$page_title = "QR Scanner";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
  <div class="topbar">
    <div class="d-flex gap-3 align-items-center">
      <button class="btn btn-light d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
      <h4><i class="fas fa-qrcode"></i> QR Attendance Scanner</h4>
    </div>
    <div class="topbar-actions">
      <span class="badge bg-success"><i class="fas fa-circle me-1"></i> Live</span>
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
<div class="col-lg-6">
  <div class="card-modern">
    <div class="card-header d-flex justify-content-between">
      <span>Scan Member QR</span>
      <button class="btn btn-sm btn-light" id="toggleCamera">
        <i class="fas fa-camera"></i> Toggle Camera
      </button>
    </div>
    <div class="card-body">
      <div class="qr-scanner-box" id="reader" style="min-height:400px; background:#111;">
        <div class="text-center text-white p-5">
          <i class="fas fa-qrcode fa-4x mb-3"></i>
          <p>Camera feed will appear here</p>
          <small style="color:#aaa;">Allow camera access to start scanning</small>
        </div>
      </div>
      <div class="mt-3">
        <label class="form-label">Or Enter Member Code Manually</label>
        <div class="input-group">
          <input id="manualCode" class="form-control" placeholder="GYM00001">
          <button class="btn btn-primary" onclick="checkInManual()">Check-In</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-lg-6">
  <div class="card-modern">
    <div class="card-header">Recent Check-Ins</div>
    <div class="card-body" id="checkinLog">
      <div class="text-center p-4" style="color:#636e72;">
        <i class="fas fa-clock fa-2x mb-2"></i>
        <p>No check-ins yet. Scan a QR to see live updates.</p>
      </div>
    </div>
  </div>
</div>
<div class="card-modern mt-4">
  <div class="card-header">Today's Stats</div>
  <div class="card-body">
    <div class="row g-2">
      <div class="col-6">
        <div class="p-3 text-center" style="background:#f8f9fc;border-radius:12px;">
          <h3 style="color:#6c5ce7;" id="todayCount">0</h3>
          <small>Check-ins Today</small>
        </div>
      </div>
      <div class="col-6">
        <div class="p-3 text-center" style="background:#f8f9fc;border-radius:12px;">
          <h3 style="color:#00b894;">Live</h3>
          <small>Scanner Active</small>
        </div>
      </div>
    </div>
    <div class="mt-3">
      <h6>How QR Check-in Works:</h6>
      <ol style="font-size:13px;color:#636e72;">
        <li>Member shows QR from their app/card</li>
        <li>Scan with this scanner</li>
        <li>Attendance auto-marked with timestamp</li>
        <li>Member gets welcome message</li>
      </ol>
    </div>
  </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let lastResult = '';

function onScanSuccess(decodedText, decodedResult) {
  if (decodedText !== lastResult) {
    lastResult = decodedText;
    handleCheckIn(decodedText);
  }
}

function handleCheckIn(code) {
  const log = document.getElementById('checkinLog');
  const time = new Date().toLocaleTimeString();

  const alertHtml = '<div class="alert alert-success d-flex justify-content-between align-items-center">'
    + '<div><strong>' + code + '</strong> checked in at ' + time + '</div>'
    + '<span class="badge bg-success">Success</span></div>';

  log.innerHTML = alertHtml + log.innerHTML;

  const todayCountElement = document.getElementById('todayCount');
  const currentCount = parseInt(todayCountElement.innerText, 10);
  todayCountElement.innerText = currentCount + 1;

  const today = new Date();
  const dateValue = today.toISOString().split('T')[0];
  const checkInValue = today.toTimeString().split(' ')[0];
  const requestBody = 'action=mark&member_id=1&date=' + dateValue + '&check_in=' + checkInValue + '&method=qr';

  fetch('attendance.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: requestBody
  });

  const audio = new Audio('data:audio/wav;base64/UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRyAgAAAAEA');

  audio.play().catch(function () {
    // audio playback failed quietly
  });
}
function checkInManual() {
  const code = document.getElementById('manualCode').value;

  if (code) {
    handleCheckIn(code);
  }
}

let html5QrcodeScanner;

try {
  html5QrcodeScanner = new Html5QrcodeScanner('reader', {
    fps: 10,
    qrbox: 250
  });

  html5QrcodeScanner.render(onScanSuccess);
} catch (e) {
  console.log('QR lib not loaded', e);

  document.getElementById('reader').innerHTML = '<div class="text-white p-4 text-center">'
    + 'QR Library failed to load. Use manual code entry. <br>'
    + '<small>For production, enable HTTPS for camera.</small></div>';
}
</script>
<?php require_once '../includes/footer.php'; ?>
