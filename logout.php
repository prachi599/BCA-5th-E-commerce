<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 7 * 24 * 60 * 60,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

include 'config/session.php';
include 'config/db.php';

// Call logout function from session.php
logoutUser($conn);
?>