<?php
$page_title = "Dashboard";
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch stats
try {
    $totalMembers = $pdo
        ->query("SELECT COUNT(*) FROM members WHERE status='active'")
        ->fetchColumn() ?: 0;

    $totalLeads = $pdo
        ->query("SELECT COUNT(*) FROM leads WHERE status != 'converted'")
        ->fetchColumn() ?: 0;

    $todayAttendance = $pdo
        ->query("SELECT COUNT(*) FROM attendance WHERE date = CURDATE()")
        ->fetchColumn() ?: 0;

    $expiringSoon = $pdo
        ->query("SELECT COUNT(*) FROM memberships WHERE end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")
        ->fetchColumn() ?: 0;

    $monthlyRevenue = $pdo
        ->query("SELECT SUM(amount) FROM payments WHERE MONTH(payment_date)=MONTH(CURDATE()) AND YEAR(payment_date)=YEAR(CURDATE())")
        ->fetchColumn() ?: 0;

    $todayRevenue = $pdo
        ->query("SELECT SUM(amount) FROM payments WHERE payment_date = CURDATE()")
        ->fetchColumn() ?: 0;

    $todayExpense = $pdo
        ->query("SELECT SUM(amount) FROM expenses WHERE expense_date = CURDATE()")
        ->fetchColumn() ?: 0;

    $birthdaysToday = $pdo
        ->query("SELECT COUNT(*) FROM members WHERE MONTH(dob)=MONTH(CURDATE()) AND DAY(dob)=DAY(CURDATE())")
        ->fetchColumn() ?: 0;

    $activeTrainers = $pdo
        ->query("SELECT COUNT(*) FROM trainers WHERE status='active'")
        ->fetchColumn() ?: 0;

    $lowStock = $pdo
        ->query("SELECT COUNT(*) FROM store_items WHERE stock < 10")
        ->fetchColumn() ?: 0;

    $pendingApprovals = $pdo
        ->query("SELECT COUNT(*) FROM pos_payments WHERE status='pending'")
        ->fetchColumn() ?: 0;
} catch (Exception $e) {
    $totalMembers = 0;
    $totalLeads = 0;
    $todayAttendance = 0;
    $expiringSoon = 0;
    $monthlyRevenue = 0;
    $todayRevenue = 0;
    $todayExpense = 0;
    $birthdaysToday = 0;
    $activeTrainers = 0;
    $lowStock = 0;
    $pendingApprovals = 0;
}
?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h4>Dashboard</h4>
                <small style="color:#636e72;">
                    Welcome back, <?= $_SESSION['full_name'] ?? 'Admin' ?>! Here's what's happening today.
                </small>
            </div>
        </div>
        <div class="topbar-actions">
            <div class="search-box d-none d-md-block">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search members, payments...">
            </div>
            <div style="position:relative;">
                <button class="btn btn-light position-relative">
                    <i class="fas fa-bell"></i>
                    <?php if ($expiringSoon > 0) : ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $expiringSoon ?>
                        </span>
                    <?php endif; ?>
                </button>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="color:inherit;">
                    <div class="avatar" style="background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white;">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="d-none d-md-block text-start" style="line-height:1.1;">
                        <div style="font-size:13px; font-weight:600; color:#2d3436;">
                            <?= $_SESSION['full_name'] ?? 'Admin' ?>
                        </div>
                        <small style="font-size:11px; color:#636e72;">Administrator</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px; min-width:220px; margin-top:8px;">
                    <li class="px-3 py-2">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="avatar" style="background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white;">
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                            </div>
                            <div>
                                <strong style="font-size:13px;">
                                    <?= $_SESSION['full_name'] ?? 'Admin' ?>
                                </strong>
                                <br>
                                <small style="color:#636e72; font-size:11px;">
                                    <?= $_SESSION['username'] ?? 'admin' ?> • Admin
                                </small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="admin/profile.php">
                            <i class="fas fa-user me-2" style="width:18px;"></i> My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="admin/settings.php">
                            <i class="fas fa-cog me-2" style="width:18px;"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="admin/my_store_account.php">
                            <i class="fas fa-store me-2" style="width:18px;"></i> My Store Account
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="admin/form_fields.php">
                            <i class="fas fa-wpforms me-2" style="width:18px;"></i> Form Builder
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2" style="width:18px;"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <!-- Quick Stats Row 1 -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0edff; color:#6c5ce7;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3><?= $totalMembers ?></h3>
                    <p>Active Members</p>
                    <div class="stat-trend text-success">
                        <i class="fas fa-arrow-up"></i> 12% vs last month
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e8f8f5; color:#00b894;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3><?= formatCurrency($monthlyRevenue) ?></h3>
                    <p>Monthly Revenue</p>
                    <div class="stat-trend text-success">
                        <i class="fas fa-arrow-up"></i> Today: <?= formatCurrency($todayRevenue) ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef9e7; color:#f1c40f;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3><?= $todayAttendance ?></h3>
                    <p>Today's Attendance</p>
                    <div class="stat-trend" style="color:#636e72;">
                        <i class="fas fa-clock"></i> Live check-ins
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fdedec; color:#e74c3c;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3><?= $expiringSoon ?></h3>
                    <p>Expiring in 7 Days</p>
                    <div class="stat-trend text-danger">
                        <i class="fas fa-bell"></i> Needs attention
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-6">
                <div class="card-modern p-3 text-center">
                    <div class="mx-auto mb-2" style="width:36px; height:36px; background:#dfe6e9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-birthday-cake" style="color:#e84393;"></i>
                    </div>
                    <h5 class="mb-0"><?= $birthdaysToday ?></h5>
                    <small style="color:#636e72; font-size:11px;">Birthdays Today</small>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card-modern p-3 text-center">
                    <div class="mx-auto mb-2" style="width:36px; height:36px; background:#dfe6e9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-user-plus" style="color:#0984e3;"></i>
                    </div>
                    <h5 class="mb-0"><?= $totalLeads ?></h5>
                    <small style="color:#636e72; font-size:11px;">Open Leads</small>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card-modern p-3 text-center">
                    <div class="mx-auto mb-2" style="width:36px; height:36px; background:#dfe6e9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-user-tie" style="color:#6c5ce7;"></i>
                    </div>
                    <h5 class="mb-0"><?= $activeTrainers ?></h5>
                    <small style="color:#636e72; font-size:11px;">Active Trainers</small>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card-modern p-3 text-center">
                    <div class="mx-auto mb-2" style="width:36px; height:36px; background:#dfe6e9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-box" style="color:#fdcb6e;"></i>
                    </div>
                    <h5 class="mb-0"><?= $lowStock ?></h5>
                    <small style="color:#636e72; font-size:11px;">Low Stock</small>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card-modern p-3 text-center">
                    <div class="mx-auto mb-2" style="width:36px; height:36px; background:#dfe6e9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-receipt" style="color:#00b894;"></i>
                    </div>
                    <h5 class="mb-0"><?= formatCurrency($todayExpense) ?></h5>
                    <small style="color:#636e72; font-size:11px;">Today Expense</small>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card-modern p-3 text-center">
                    <div class="mx-auto mb-2" style="width:36px; height:36px; background:#dfe6e9; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-check" style="color:#d63031;"></i>
                    </div>
                    <h5 class="mb-0"><?= $pendingApprovals ?></h5>
                    <small style="color:#636e72; font-size:11px;">Pending Approvals</small>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Charts -->
            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Revenue vs Expenses (Last 6 Months)</span>
                        <select class="form-select form-select-sm" style="width:120px;">
                            <option>This Year</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="90"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-modern">
                    <div class="card-header">Membership Distribution</div>
                    <div class="card-body">
                        <canvas id="memberPie" height="195"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-modern">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Recent Payments</span>
                        <a href="admin/payments.php" class="btn btn-sm btn-light">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $pdo->query("SELECT p.*, m.name as member_name FROM payments p LEFT JOIN members m ON p.member_id=m.id ORDER BY p.created_at DESC LIMIT 5");
                                    foreach ($stmt->fetchAll() as $pay) :
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar">
                                                    <?= strtoupper(substr($pay['member_name'] ?? 'G', 0, 1)) ?>
                                                </div>
                                                <?= $pay['member_name'] ?? 'Guest' ?>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?= formatCurrency($pay['amount']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= ucfirst($pay['payment_method']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= date('M d', strtotime($pay['payment_date'])) ?>
                                        </td>
                                        <td>
                                            <?= getStatusBadge($pay['status']) ?>
                                        </td>
                                    </tr>
                                <?php
                                    endforeach;
                                } catch (Exception $e) {
                                    echo "<tr><td colspan=5>No data yet</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-modern">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Expiring Memberships</span>
                        <a href="admin/memberships.php" class="btn btn-sm btn-warning">Renew</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Plan</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $pdo->query("SELECT ms.*, m.name as member_name, p.name as plan_name FROM memberships ms JOIN members m ON ms.member_id=m.id JOIN plans p ON ms.plan_id=p.id WHERE ms.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) LIMIT 5");
                                    foreach ($stmt->fetchAll() as $row) :
                                ?>
                                    <tr>
                                        <td>
                                            <?= $row['member_name'] ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= $row['plan_name'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-danger">
                                                <?= date('M d', strtotime($row['end_date'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">Renew</button>
                                        </td>
                                    </tr>
                                <?php
                                    endforeach;
                                } catch (Exception $e) {
                                    echo "<tr><td colspan=4>No expiring memberships</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-modern mt-4">
                    <div class="card-header">Quick Actions</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="admin/members.php" class="btn btn-light w-100 py-3" style="border:1px solid #f1f2f6; border-radius:12px;">
                                    <i class="fas fa-user-plus d-block mb-2" style="color:#6c5ce7;"></i>
                                    Add Member
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="admin/attendance_scan.php" class="btn btn-light w-100 py-3" style="border:1px solid #f1f2f6; border-radius:12px;">
                                    <i class="fas fa-qrcode d-block mb-2" style="color:#00b894;"></i>
                                    QR Scan
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="admin/store_pos.php" class="btn btn-light w-100 py-3" style="border:1px solid #f1f2f6; border-radius:12px;">
                                    <i class="fas fa-cash-register d-block mb-2" style="color:#fdcb6e;"></i>
                                    POS Sale
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="admin/payments.php" class="btn btn-light w-100 py-3" style="border:1px solid #f1f2f6; border-radius:12px;">
                                    <i class="fas fa-money-bill d-block mb-2" style="color:#0984e3;"></i>
                                    Payment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Revenue',
                    data: [5200, 6100, 4800, 7200, 6500, 8900],
                    borderColor: '#6c5ce7',
                    backgroundColor: 'rgba(108, 92, 231, .1)',
                    tension: .4,
                    fill: true
                },
                {
                    label: 'Expenses',
                    data: [3200, 2800, 3100, 2900, 3300, 3000],
                    borderColor: '#fdcb6e',
                    backgroundColor: 'rgba(253, 203, 110, .1)',
                    tension: .4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx2 = document.getElementById('memberPie').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Expired', 'Pending', 'Trial'],
            datasets: [
                {
                    data: [65, 15, 10, 10],
                    backgroundColor: ['#6c5ce7', '#dfe6e9', '#fdcb6e', '#00b894'],
                    borderWidth: 0
                }
            ]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
