<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    $current = $_SERVER['REQUEST_URI'];
    // Determine correct path to index
    if (strpos($current, '/admin/') !== false) {
        header("Location: ../index.php");
    } else {
        header("Location: index.php");
    }
    exit;
}
?>
