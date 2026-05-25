<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../shop.php");
    exit();
}

// Set admin cookie for session tracking
setcookie('admin_session', bin2hex(random_bytes(16)), time() + 3600, '/', '', false, true);

$id = (int) $_GET['id'];

mysqli_query($conn, "DELETE FROM products WHERE id = $id");

header("Location: products.php");