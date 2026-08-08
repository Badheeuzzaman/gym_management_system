<?php
$base = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false)
    ? ''
    : 'admin/';

$root = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false)
    ? '../'
    : '';

$current = basename($_SERVER['PHP_SELF']);

function isActive($files)
{
    global $current;

    if (is_array($files)) {
        return in_array($current, $files) ? 'active' : '';
    }

    return $current === $files ? 'active' : '';
}
?>
<div class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-logo" style="background: linear-gradient(135deg, #000, var(--danger));">
            <img
                src="<?= $root ?>assets/logo.png"
                alt="FlexFit Logo"
                style="width:35px; height:45px; margin-bottom:-1px; border-radius:12px;"
            >
        </div>
        <div class="brand-text">
            <h5><?= getSetting('gym_name', 'FlexFit Gym') ?></h5>
            <small>Management</small>
        </div>
    </div>

    <div class="nav-section">Main</div>
    <a
        href="<?= $root ?>dashboard.php"
        class="nav-link <?= isActive('dashboard.php') ?>"
    ><i class="fas fa-th-large"></i> Dashboard</a>

    <div class="nav-section">Members</div>
    <a
        href="<?= $base ?>members.php"
        class="nav-link <?= isActive('members.php') ?>"
    ><i class="fas fa-users"></i> Members</a>
    <a
        href="<?= $base ?>leads.php"
        class="nav-link <?= isActive('leads.php') ?>"
    ><i class="fas fa-user-plus"></i> Leads / Trials</a>
    <a
        href="<?= $base ?>assign_workout.php"
        class="nav-link <?= isActive('assign_workout.php') ?>"
    ><i class="fas fa-dumbbell"></i> Assign Workout</a>
    <a
        href="<?= $base ?>birthdays.php"
        class="nav-link <?= isActive('birthdays.php') ?>"
    ><i class="fas fa-birthday-cake"></i> Birthdays</a>
    <a
        href="<?= $base ?>attendance.php"
        class="nav-link <?= isActive('attendance.php') ?>"
    ><i class="fas fa-calendar-check"></i> Attendance</a>
    <a
        href="<?= $base ?>attendance_scan.php"
        class="nav-link <?= isActive('attendance_scan.php') ?>"
    ><i class="fas fa-qrcode"></i> QR Scanner</a>
    <a
        href="<?= $base ?>zkteco.php"
        class="nav-link <?= isActive('zkteco.php') ?>"
    ><i class="fas fa-fingerprint"></i> Biometric Device</a>
    <a
        href="<?= $base ?>memberships.php"
        class="nav-link <?= isActive('memberships.php') ?>"
    ><i class="fas fa-id-card"></i> Memberships</a>

    <div class="nav-section">Finance</div>
    <a
        href="<?= $base ?>payments.php"
        class="nav-link <?= isActive('payments.php') ?>"
    ><i class="fas fa-credit-card"></i> Payments</a>
    <a
        href="<?= $base ?>expenses.php"
        class="nav-link <?= isActive('expenses.php') ?>"
    ><i class="fas fa-file-invoice-dollar"></i> Expenses</a>
    <a
        href="<?= $base ?>day_end.php"
        class="nav-link <?= isActive('day_end.php') ?>"
    ><i class="fas fa-cash-register"></i> Day End Closing</a>
    <a
        href="<?= $base ?>transfers.php"
        class="nav-link <?= isActive('transfers.php') ?>"
    ><i class="fas fa-exchange-alt"></i> Cash & Bank</a>
    <a
        href="<?= $base ?>banks.php"
        class="nav-link <?= isActive('banks.php') ?>"
    ><i class="fas fa-university"></i> Bank Accounts</a>
    <a
        href="<?= $base ?>closing_checklist.php"
        class="nav-link <?= isActive('closing_checklist.php') ?>"
    ><i class="fas fa-tasks"></i> Closing Checklist</a>

    <div class="nav-section">Store</div>
    <a
        href="<?= $base ?>store_pos.php"
        class="nav-link <?= isActive('store_pos.php') ?>"
    ><i class="fas fa-shopping-cart"></i> New Sale (POS)</a>
    <a
        href="<?= $base ?>store.php"
        class="nav-link <?= isActive(['store.php', 'store_items.php']) ?>"
    ><i class="fas fa-box"></i> Items</a>
    <a
        href="<?= $base ?>store_sales.php"
        class="nav-link <?= isActive('store_sales.php') ?>"
    ><i class="fas fa-receipt"></i> Sales History</a>
    <a
        href="<?= $base ?>pos_payments.php"
        class="nav-link <?= isActive('pos_payments.php') ?>"
    ><i class="fas fa-check-double"></i> Payment Approvals</a>

    <div class="nav-section">Gym</div>
    <a
        href="<?= $base ?>trainers.php"
        class="nav-link <?= isActive('trainers.php') ?>"
    ><i class="fas fa-user-tie"></i> Trainers</a>
    <a
        href="<?= $base ?>classes.php"
        class="nav-link <?= isActive('classes.php') ?>"
    ><i class="fas fa-chalkboard-teacher"></i> Classes</a>
    <a
        href="<?= $base ?>workouts.php"
        class="nav-link <?= isActive('workouts.php') ?>"
    ><i class="fas fa-running"></i> Workouts</a>
    <a
        href="<?= $base ?>diet_templates.php"
        class="nav-link <?= isActive('diet_templates.php') ?>"
    ><i class="fas fa-apple-alt"></i> Diet Templates</a>
    <a
        href="<?= $base ?>plans.php"
        class="nav-link <?= isActive('plans.php') ?>"
    ><i class="fas fa-layer-group"></i> Plans</a>

    <div class="nav-section">Inventory & Assets</div>
    <a
        href="<?= $base ?>inventory.php"
        class="nav-link <?= isActive('inventory.php') ?>"
    ><i class="fas fa-warehouse"></i> Inventory</a>
    <a
        href="<?= $base ?>inventory_suppliers.php"
        class="nav-link <?= isActive('inventory_suppliers.php') ?>"
    ><i class="fas fa-truck"></i> Suppliers</a>
    <a
        href="<?= $base ?>equipment.php"
        class="nav-link <?= isActive('equipment.php') ?>"
    ><i class="fas fa-tools"></i> Equipment</a>

    <div class="nav-section">Tools</div>
    <a
        href="<?= $base ?>reports.php"
        class="nav-link <?= isActive('reports.php') ?>"
    ><i class="fas fa-chart-line"></i> Reports</a>
    <a
        href="<?= $base ?>reminders.php"
        class="nav-link <?= isActive('reminders.php') ?>"
    ><i class="fas fa-bell"></i> Reminders</a>
    <a
        href="<?= $base ?>sms.php"
        class="nav-link <?= isActive('sms.php') ?>"
    ><i class="fas fa-sms"></i> SMS Center</a>

    <div class="nav-section">Staff & HR</div>
    <a
        href="<?= $base ?>staff.php"
        class="nav-link <?= isActive('staff.php') ?>"
    ><i class="fas fa-users-cog"></i> Staff</a>
    <a
        href="<?= $base ?>my_store_account.php"
        class="nav-link <?= isActive('my_store_account.php') ?>"
    ><i class="fas fa-store"></i> My Store Account</a>
    <a
        href="<?= $base ?>staff_salary.php"
        class="nav-link <?= isActive('staff_salary.php') ?>"
    ><i class="fas fa-money-check-alt"></i> Salary & Payroll</a>
    <a
        href="<?= $base ?>staff_shifts.php"
        class="nav-link <?= isActive('staff_shifts.php') ?>"
    ><i class="fas fa-clock"></i> Shift Timings</a>
    <a
        href="<?= $base ?>staff_duty.php"
        class="nav-link <?= isActive('staff_duty.php') ?>"
    ><i class="fas fa-calendar-alt"></i> Duty Roster</a>
    <a
        href="<?= $base ?>staff_attendance.php"
        class="nav-link <?= isActive('staff_attendance.php') ?>"
    ><i class="fas fa-user-check"></i> Staff Attendance</a>
    <a
        href="<?= $base ?>staff_bonus.php"
        class="nav-link <?= isActive('staff_bonus.php') ?>"
    ><i class="fas fa-gift"></i> Bonuses</a>

    <div class="nav-section">Admin</div>
    <a
        href="<?= $base ?>form_fields.php"
        class="nav-link <?= isActive('form_fields.php') ?>"
    ><i class="fas fa-wpforms"></i> Form Builder</a>
    <a
        href="<?= $base ?>settings.php"
        class="nav-link <?= isActive('settings.php') ?>"
    ><i class="fas fa-cog"></i> Settings</a>
    <a
        href="<?= $base ?>help.php"
        class="nav-link <?= isActive('help.php') ?>"
    ><i class="fas fa-question-circle"></i> Help & Docs</a>
    <a
        href="<?= $root ?>logout.php"
        class="nav-link"
        style="margin-bottom:30px; color:#ff7675;"
    ><i class="fas fa-sign-out-alt"></i> Sign Out</a>
</div>
