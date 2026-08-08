<?php
session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Base URL helper
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname($_SERVER['SCRIPT_NAME']);
    // Adjust for nested admin folder
    $base = str_replace('/admin', '', $script);
    $base = rtrim($base, '/');
    if ($base === '/gym_management_system') {
        // keep
    }
    return $protocol . $host . $base . '/' . ltrim($path, '/');
}

function redirect($path) {
    header("Location: " . $path);
    exit;
}

// Auto load settings
$settings = [];
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'settings'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
} catch (Exception $e) {
    // table not created yet
}
?>
