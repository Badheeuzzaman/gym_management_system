<?php

$page_title = "Leads / Trials";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] === 'add') {

        $stmt = $pdo->prepare("
            INSERT INTO leads
            (
                name,
                phone,
                email,
                source,
                interested_plan,
                status,
                followup_date,
                notes
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
                ?
            )
        ");

        $stmt->execute([
            $_POST['name'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['source'],
            $_POST['interested_plan'],
            $_POST['status'],
            !empty($_POST['followup_date']) ? $_POST['followup_date'] : null,
            $_POST['notes']
        ]);

    } elseif ($_POST['action'] === 'edit') {

        $stmt = $pdo->prepare("
            UPDATE leads
            SET
                name = ?,
                phone = ?,
                email = ?,
                source = ?,
                interested_plan = ?,
                status = ?,
                followup_date = ?,
                notes = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['name'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['source'],
            $_POST['interested_plan'],
            $_POST['status'],
            !empty($_POST['followup_date']) ? $_POST['followup_date'] : null,
            $_POST['notes'],
            $_POST['id']
        ]);
    }
}

if (isset($_GET['delete'])) {

    $stmt = $pdo->prepare("
        DELETE FROM leads
        WHERE id = ?
    ");

    $stmt->execute([
        $_GET['delete']
    ]);

    header("Location: leads.php");
    exit;
}

if (isset($_GET['convert'])) {

    $stmt = $pdo->prepare("
        SELECT *
        FROM leads
        WHERE id = ?
    ");

    $stmt->execute([
        $_GET['convert']
    ]);

    $lead = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lead) {

        $memberCode = 'GYM' . rand(10000, 99999);

        $stmt = $pdo->prepare("
            INSERT INTO members
            (
                member_code,
                name,
                email,
                phone,
                join_date,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                CURDATE(),
                'active'
            )
        ");

        $stmt->execute([
            $memberCode,
            $lead['name'],
            $lead['email'],
            $lead['phone']
        ]);

        $stmt = $pdo->prepare("
            UPDATE leads
            SET status = 'converted'
            WHERE id = ?
        ");

        $stmt->execute([
            $lead['id']
        ]);
    }

    header("Location: leads.php");
    exit;
}

$stmt = $pdo->query("
    SELECT *
    FROM leads
    ORDER BY id DESC
");

$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

$newLeadsCount = 0;
$trialLeadsCount = 0;
$convertedLeadsCount = 0;

foreach ($leads as $lead) {
    if ($lead['status'] === 'new') {
        $newLeadsCount++;
    }

    if ($lead['status'] === 'trial') {
        $trialLeadsCount++;
    }

    if ($lead['status'] === 'converted') {
        $convertedLeadsCount++;
    }
}

$totalLeadsCount = count($leads);

if ($totalLeadsCount > 0) {
    $conversionRate = round(($convertedLeadsCount / $totalLeadsCount) * 100);
} else {
    $conversionRate = 0;
}

?>

<div class="main-content">
  <div class="topbar">
    <div class="d-flex gap-3 align-items-center">
      <button class="btn btn-light d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
      <h4>Leads / Trials 
        <span class="badge bg-warning text-dark"><?= count($leads) ?></span>
      </h4>
    </div>
    <div class="topbar-actions">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-1"></i> Add Lead
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
    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <div class="card-modern p-3">
          <small style="color:#636e72;">New Leads</small>
          <h4>
            <?= $newLeadsCount ?>
          </h4>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-modern p-3">
          <small style="color:#636e72;">Trials</small>
          <h4>
            <?= $trialLeadsCount ?>
          </h4>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-modern p-3">
          <small style="color:#636e72;">Converted</small>
          <h4>
            <?= $convertedLeadsCount ?>
          </h4>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-modern p-3">
          <small style="color:#636e72;">Conversion Rate</small>
          <h4>
            <?= $conversionRate ?>%
          </h4>
        </div>
      </div>
    </div>
    <div class="card-modern">
      <div class="table-responsive">
        <table class="table table-modern table-hover mb-0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Contact</th>
              <th>Interested</th>
              <th>Source</th>
              <th>Follow-up</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($leads as $l) { ?>
            <tr>
              <td>
                <strong>
                  <?= $l['name'] ?>
                </strong>
              </td>
              <td>
                <div>
                  <?= $l['phone'] ?>
                </div>
                <small>
                  <?= $l['email'] ?>
                </small>
              </td>
              <td>
                <?= $l['interested_plan'] ?>
              </td>
              <td>
                <span class="badge bg-light text-dark border">
                  <?= $l['source'] ?>
                </span>
              </td>
              <td>
                <?php if (!empty($l['followup_date'])) { ?>
                  <?= date('M d', strtotime($l['followup_date'])) ?>
                <?php } else { ?>
                  -
                <?php } ?>
              </td>
              <td>
                <?= getStatusBadge($l['status']) ?>
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <a
                    href="?convert=<?= $l['id'] ?>"
                    class="btn btn-success"
                    title="Convert"
                  >
                    <i class="fas fa-check"></i>
                  </a>
                  <a
                    href="?delete=<?= $l['id'] ?>"
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
<div class="modal fade" id="addModal">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5>Add Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-2">
        <input type="hidden" name="action" value="add">
        <div class="col-md-6">
          <label class="form-label">Name *</label>
          <input name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone *</label>
          <input name="phone" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input name="email" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Source</label>
          <select name="source" class="form-select">
            <option>Walk-in</option>
            <option>Facebook</option>
            <option>Instagram</option>
            <option>Referral</option>
            <option>Website</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Interested Plan</label>
          <input name="interested_plan" class="form-control" placeholder="Monthly">
        </div>
        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="new">New</option>
            <option value="contacted">Contacted</option>
            <option value="trial">Trial</option>
            <option value="converted">Converted</option>
            <option value="lost">Lost</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Follow-up Date</label>
          <input type="date" name="followup_date" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Lead</button>
      </div>
    </form>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
