<?php
// Set base path
$base_path = dirname(dirname(__FILE__));

if (!isset($conn)) {
    include $base_path . '/config/db.php';
}

// Check if user is logged in (restores from cookie if needed)
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) ? true : false;

// If not logged in, check remember token
if (!$isLoggedIn && isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
    include_once $base_path . '/config/session.php';
    $isLoggedIn = checkUserLogin();
}
?>

<nav class="navbar">
    <div class="logo">🎁 GiftHub</div>
    <ul class="nav-links">
        <li><a href="/gifthub/index.php">Home</a></li>
        <li><a href="/gifthub/shop.php">Shop</a></li>
        <li><a href="/gifthub/cart.php">Cart</a></li>

        <?php if ($isLoggedIn && isset($_SESSION['user_id'])): ?>
            <li><a href="/gifthub/orders.php">Orders</a></li>

            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li><a href="/gifthub/admin/index.php">Dashboard</a></li>
                <li><a href="/gifthub/admin/admin_orders.php">Admin Orders</a></li>
            <?php endif; ?>

            <li><a href="#">👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
            <li><a href="/gifthub/logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="/gifthub/login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>