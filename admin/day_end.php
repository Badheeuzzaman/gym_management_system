<?php

$page_title = "Day End Closing";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$today = date('Y-m-d');

$income = $pdo->query("
    SELECT SUM(amount)
    FROM payments
    WHERE payment_date = '$today'
")->fetchColumn() ?: 0;

$expense = $pdo->query("
    SELECT SUM(amount)
    FROM expenses
    WHERE expense_date = '$today'
")->fetchColumn() ?: 0;

$stmt = $pdo->query("
    SELECT *
    FROM day_closing
    WHERE closing_date = '$today'
");

$closing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        INSERT INTO day_closing
        (
            closing_date,
            opening_cash,
            total_income,
            total_expense,
            closing_cash,
            closed_by,
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
            ?
        )
        ON DUPLICATE KEY UPDATE
            opening_cash = VALUES(opening_cash),
            total_income = VALUES(total_income),
            total_expense = VALUES(total_expense),
            closing_cash = VALUES(closing_cash),
            notes = VALUES(notes)
    ");

    $stmt->execute([
        $today,
        $_POST['opening_cash'],
        $income,
        $expense,
        $_POST['closing_cash'],
        $_SESSION['user_id'],
        $_POST['notes']
    ]);

    $msg = "Day closed successfully!";

    $stmt = $pdo->query("
        SELECT *
        FROM day_closing
        WHERE closing_date = '$today'
    ");

    $closing = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query("
    SELECT *
    FROM day_closing
    ORDER BY closing_date DESC
    LIMIT 10
");

$history = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="main-content">
  <div class="topbar">
    <div class="d-flex gap-3 align-items-center">
      <button class="btn btn-light d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
      <h4>Day End Closing</h4>
    </div>
    <div class="topbar-actions">
      <span class="badge bg-light text-dark border">
        <?= $today ?>
      </span>
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
    <?php if (isset($msg)) { ?>
      <div class="alert alert-success">
        <?= $msg ?>
      </div>
    <?php } ?>
    <div class="row g-4">
  <div class="col-lg-5">
    <div class="card-modern">
      <div class="card-header">Close Today (<?= $today ?>)</div>
      <div class="card-body">
<form method="post">
  <div class="mb-3">
    <label class="form-label">Opening Cash</label>
    <input name="opening_cash" type="number" step="0.01" value="<?= $closing['opening_cash']??'500' ?>" class="form-control">
  </div>
    <div class="row g-2 mb-3">
      <div class="col-6">
      <div class="p-3" style="background:#e8f8f5;border-radius:10px;">
        <small style="color:#636e72;">Total Income</small>
        <h4 style="color:#00b894;"><?= formatCurrency($income) ?></h4>
      </div>
    </div>
    <div class="col-6">
      <div class="p-3" style="background:#fdedec;border-radius:10px;">
        <small style="color:#636e72;">Total Expense</small>
        <h4 style="color:#d63031;"><?= formatCurrency($expense) ?></h4>
      </div>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Closing Cash *</label>
    <input name="closing_cash" type="number" step="0.01" class="form-control" required value="<?= $closing['closing_cash']?? ($income - $expense + 500) ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control"><?= $closing['notes']??'' ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary w-100">Submit Day Closing</button>
</form>
</div>
</div>
</div>
<div class="col-lg-7">
  <div class="card-modern">
    <div class="card-header">Closing History (Last 10)</div>
    <div class="table-responsive">
      <table class="table table-modern mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Opening</th>
            <th>Income</th>
            <th>Expense</th>
            <th>Closing</th>
            <th>Closed By</th>
          </tr>
        </thead>
        <tbody>
<?php foreach ($history as $h) { ?>
          <tr>
            <td>
              <?= $h['closing_date'] ?>
            </td>
            <td>
              <?= formatCurrency($h['opening_cash']) ?>
            </td>
            <td style="color:#00b894;">
              <?= formatCurrency($h['total_income']) ?>
            </td>
            <td style="color:#d63031;">
              <?= formatCurrency($h['total_expense']) ?>
            </td>
            <td>
              <strong>
                <?= formatCurrency($h['closing_cash']) ?>
              </strong>
            </td>
            <td>
              <?= $h['closed_by'] ?>
            </td>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
</div>
<?php require_once '../includes/footer.php'; ?>
