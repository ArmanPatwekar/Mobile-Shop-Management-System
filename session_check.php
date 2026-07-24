<?php
// Include this file at the top of protected pages
// Usage: require_once 'session_check.php';
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: login.html");
    exit;
}
// Optional: Set timezone
date_default_timezone_set('Asia/Kolkata');
?>
