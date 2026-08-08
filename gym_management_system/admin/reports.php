<?php

$page_title = "Reports";

require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Monthly Income Report

$stmt = $pdo->query(
    "SELECT
        MONTH(payment_date) AS month,
        SUM(amount) AS total
    FROM payments
    WHERE YEAR(payment_date) = YEAR(CURDATE())
    GROUP BY MONTH(payment_date)
    ORDER BY MONTH(payment_date)"
);

$monthlyIncome = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Monthly Expense Report

$stmt = $pdo->query(
    "SELECT
        MONTH(expense_date) AS month,
        SUM(amount) AS total
    FROM expenses
    WHERE YEAR(expense_date) = YEAR(CURDATE())
    GROUP BY MONTH(expense_date)
    ORDER BY MONTH(expense_date)"
);

$monthlyExpense = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$attendanceAvgQuery = "SELECT AVG(cnt) FROM (SELECT COUNT(*) as cnt FROM attendance GROUP BY date) t";
$attendanceAvgStmt = $pdo->query($attendanceAvgQuery);
$averageAttendance = round($attendanceAvgStmt->fetchColumn() ?: 0);

?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Reports & Analytics</h4>
        </div>
        <div class="topbar-actions">
            <select class="form-select form-select-sm">
                <option>2024</option>
                <option>2025</option>
                <option>2026</option>
            </select>
            <button class="btn btn-light btn-sm">
                <i class="fas fa-download"></i>
                Export PDF
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
            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="card-header">
                        Income vs Expense (Yearly)
                    </div>
                    <div class="card-body">
                        <canvas id="reportChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-modern">
                    <div class="card-header">
                        Membership Stats
                    </div>
                    <div class="card-body">
                        <canvas id="membershipChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern p-3">
                    <small>Total Revenue</small>
                    <h4>
                        <?= formatCurrency(array_sum($monthlyIncome)) ?>
                    </h4>
                    <small style="color:#00b894;">
                        +12% YoY
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern p-3">
                    <small>Total Expenses</small>
                    <h4>
                        <?= formatCurrency(array_sum($monthlyExpense)) ?>
                    </h4>
                    <small style="color:#d63031;">
                        +5% YoY
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern p-3">
                    <small>Net Profit</small>
                    <h4>
                        <?= formatCurrency(
                            array_sum($monthlyIncome)
                            - array_sum($monthlyExpense)
                        ) ?>
                    </h4>
                    <small style="color:#00b894;">
                        Profit
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern p-3">
                    <small>Avg Attendance</small>
                    <h4>
                        <?= $averageAttendance ?> /day
                    </h4>
                </div>
            </div>
            <div class="col-12">
                <div class="card-modern">
                    <div class="card-header">
                        Detailed Reports
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <a
                                href="payments.php"
                                class="btn btn-light w-100 py-3"
                            >
                                <i class="fas fa-money-bill d-block mb-2"></i>
                                Payment Report
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a
                                href="attendance.php"
                                class="btn btn-light w-100 py-3"
                            >
                                <i class="fas fa-calendar-check d-block mb-2"></i>
                                Attendance Report
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a
                                href="expenses.php"
                                class="btn btn-light w-100 py-3"
                            >
                                <i class="fas fa-file-invoice d-block mb-2"></i>
                                Expense Report
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a
                                href="store_sales.php"
                                class="btn btn-light w-100 py-3"
                            >
                                <i class="fas fa-cash-register d-block mb-2"></i>
                                Sales Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const reportCanvas = document.getElementById('reportChart');
const reportCtx = reportCanvas.getContext('2d');
const reportChart = new Chart(reportCtx, {
    type: 'bar',
    data: {
        labels: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ],
        datasets: [
            {
                label: 'Income',
                data: [
                    <?= implode(', ', array_map(fn($i) => $monthlyIncome[$i] ?? 0, range(1, 12))) ?>
                ],
                backgroundColor: '#6c5ce7'
            },
            {
                label: 'Expense',
                data: [
                    <?= implode(', ', array_map(fn($i) => $monthlyExpense[$i] ?? 0, range(1, 12))) ?>
                ],
                backgroundColor: '#fdcb6e'
            }
        ]
    },
    options: {
        responsive: true
    }
});

const membershipCanvas = document.getElementById('membershipChart');
const membershipCtx = membershipCanvas.getContext('2d');
const membershipChart = new Chart(membershipCtx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Expired', 'Pending'],
        datasets: [
            {
                data: [65, 20, 15],
                backgroundColor: ['#6c5ce7', '#dfe6e9', '#fdcb6e']
            }
        ]
    },
    options: {
        cutout: '70%'
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
