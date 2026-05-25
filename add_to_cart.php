<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login with return URL
    header("Location: login.php?return=" . urlencode($_GET['id'] ?? ''));
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($product_id > 0) {
    // Use prepared statement for security
    $check_stmt = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        // Update quantity
        $update_stmt = mysqli_prepare($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($update_stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($update_stmt);
    } else {
        // Insert new item
        $insert_stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($insert_stmt);
    }
}

header("Location: cart.php");
exit();

?>