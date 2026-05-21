<?php
/**
 * Session & Cookie Configuration
 */

// Set secure session settings
session_set_cookie_params([
    'lifetime' => 7 * 24 * 60 * 60,  // 7 days
    'path' => '/',
    'domain' => '',
    'secure' => false,  // Set to true if using HTTPS
    'httponly' => true,  // Prevent JavaScript access
    'samesite' => 'Strict'  // CSRF protection
]);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in, restore from cookie if needed
 */
function checkUserLogin() {
    // If session exists, user is logged in
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return true;
    }

    // Check if remember me cookie exists
    if (isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
        include 'db.php';
        
        $token = mysqli_real_escape_string($conn, $_COOKIE['remember_token']);
        $query = mysqli_query($conn, "SELECT * FROM users WHERE remember_token = '$token' AND token_expiry > NOW() LIMIT 1");

        if ($query && mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);
            
            // Restore session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            return true;
        } else {
            // Token expired or invalid, remove cookie
            setcookie('remember_token', '', time() - 3600, '/');
            return false;
        }
    }

    return false;
}

/**
 * Set remember me cookie
 */
function setRememberCookie($user_id, $conn) {
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

    $query = mysqli_query($conn, "UPDATE users SET remember_token = '$token', token_expiry = '$expiry' WHERE id = $user_id");

    if ($query) {
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        return true;
    }
    return false;
}

/**
 * Clear remember me cookie
 */
function clearRememberCookie($conn) {
    if (isset($_SESSION['user_id'])) {
        mysqli_query($conn, "UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = " . $_SESSION['user_id']);
    }
    setcookie('remember_token', '', time() - 3600, '/');
}

/**
 * Logout user
 */
function logoutUser($conn) {
    clearRememberCookie($conn);
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
