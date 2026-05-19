<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="logo">🎁 GiftHub</div>
    <ul class="nav-links">
        <li><a href="/gifthub/index.php">Home</a></li>
        <li><a href="/gifthub/shop.php">Shop</a></li>
        <li><a href="/gifthub/cart.php">Cart</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
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