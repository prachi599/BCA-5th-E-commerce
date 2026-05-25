<?php
/**
 * Session & Cookie Configuration
 * Secure session handling with automatic cookie restoration
 */

// Set secure session settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 7 * 24 * 60 * 60,  // 7 days
        'path' => '/',
        'domain' => '',
        'secure' => false,  // Set to true if using HTTPS
        'httponly' => true,  // Prevent JavaScript access
        'samesite' => 'Strict'  // CSRF protection
    ]);
    
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
        
        // Use prepared statement for security
        $stmt = mysqli_prepare($conn, "SELECT id, name, role FROM users WHERE remember_token = ? AND token_expiry > NOW() LIMIT 1");
        
        if ($stmt) {
            $token = $_COOKIE['remember_token'];
            mysqli_stmt_bind_param($stmt, "s", $token);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                
                // Restore session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'] ?? 'customer';
                
                return true;
            } else {
                // Token expired or invalid, remove cookie
                setcookie('remember_token', '', time() - 3600, '/', '', false, true);
                return false;
            }
        }
    }

    return false;
}

/**
 * Set remember me cookie with prepared statement
 */
function setRememberCookie($user_id, $conn) {
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Use prepared statement for security
    $stmt = mysqli_prepare($conn, "UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $token, $expiry, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            return true;
        }
    }
    
    return false;
}

/**
 * Clear remember me cookie with prepared statement
 */
function clearRememberCookie($conn) {
    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = ?");
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
        }
    }
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

/**
 * Clear all session data safely
 */
function clearSessionData() {
    $_SESSION = [];
    
    // Destroy session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
}

/**
 * Logout user - clear session and cookies
 */
function logoutUser($conn) {
    clearRememberCookie($conn);
    clearSessionData();
    session_destroy();
    header("Location: index.php");
    exit();
}

/**
 * Validate session security - regenerate session ID periodically
 */
function validateSessionSecurity() {
    // Regenerate session ID every 30 minutes or on login
    if (!isset($_SESSION['LAST_REGENERATE'])) {
        $_SESSION['LAST_REGENERATE'] = time();
        session_regenerate_id(true);
    } elseif (time() - $_SESSION['LAST_REGENERATE'] > 1800) { // 30 minutes
        $_SESSION['LAST_REGENERATE'] = time();
        session_regenerate_id(true);
    }
}

// Validate session on every page load
if (session_status() === PHP_SESSION_ACTIVE) {
    validateSessionSecurity();
}
?>
