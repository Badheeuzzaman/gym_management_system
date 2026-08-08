<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../index.php");
        exit;
    }
}

function getSetting($key, $default = '') {
    global $settings;
    return $settings[$key] ?? $default;
}

function formatCurrency($amount) {
    $currency = getSetting('currency', '$');
    return $currency . number_format((float)$amount, 2);
}

function generateMemberCode() {
    return 'GYM' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
}

function generateInvoiceNo() {
    return 'INV-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return $diff . " seconds ago";
    if ($diff < 3600) return floor($diff/60) . " mins ago";
    if ($diff < 86400) return floor($diff/3600) . " hours ago";
    if ($diff < 2592000) return floor($diff/86400) . " days ago";
    return date('M d, Y', $time);
}

function getStatusBadge($status) {
    $map = [
        'active' => 'success',
        'inactive' => 'secondary',
        'expired' => 'danger',
        'pending' => 'warning',
        'completed' => 'success',
        'trial' => 'info',
        'converted' => 'primary',
        'present' => 'success',
        'absent' => 'danger',
        'late' => 'warning'
    ];
    $color = $map[strtolower($status)] ?? 'secondary';
    return "<span class='badge bg-{$color}'>" . ucfirst($status) . "</span>";
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
