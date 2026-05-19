<?php
session_start();
include '../config/db.php';

// Admin protection
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../shop.php");
    exit();
}

// Dashboard stats
$users_result = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users");
$products_result = mysqli_query($conn, "SELECT COUNT(*) AS total_products FROM products");
$orders_result = mysqli_query($conn, "SELECT COUNT(*) AS total_orders FROM orders");
$sales_result = mysqli_query($conn, "SELECT SUM(total_amount) AS total_sales FROM orders");

$total_users = 0;
$total_products = 0;
$total_orders = 0;
$total_sales = 0;

if ($users_result) {
    $total_users = mysqli_fetch_assoc($users_result)['total_users'];
}

if ($products_result) {
    $total_products = mysqli_fetch_assoc($products_result)['total_products'];
}

if ($orders_result) {
    $total_orders = mysqli_fetch_assoc($orders_result)['total_orders'];
}

if ($sales_result) {
    $sales_data = mysqli_fetch_assoc($sales_result);
    $total_sales = !empty($sales_data['total_sales']) ? $sales_data['total_sales'] : 0;
}
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<style>
    .main {
        padding: 40px;
        background: #fffaf9;
        min-height: 100vh;
    }

    .admin-dashboard-title {
        font-size: 42px;
        color: #6f4b8b;
        margin-bottom: 35px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: #fff;
        border-radius: 22px;
        padding: 28px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        text-align: center;
    }

    .stat-card h3 {
        margin: 0 0 12px;
        color: #6f4b8b;
        font-size: 22px;
    }

    .stat-card p {
        margin: 0;
        font-size: 30px;
        font-weight: bold;
        color: #333;
    }

    .admin-links-box {
        background: #fff;
        border-radius: 22px;
        padding: 30px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .admin-links-box h2 {
        color: #6f4b8b;
        margin-bottom: 20px;
        font-size: 30px;
    }

    .admin-links {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .admin-btn {
        display: inline-block;
        padding: 14px 22px;
        background: #e58cab;
        color: white;
        text-decoration: none;
        border-radius: 14px;
        font-weight: bold;
        transition: 0.3s ease;
    }

    .admin-btn:hover {
        background: #d9789b;
    }

    @media (max-width: 1000px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .main {
            padding: 24px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .admin-dashboard-title {
            font-size: 34px;
        }
    }
</style>

<div class="main">
    <h1 class="admin-dashboard-title">Admin Dashboard</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>

        <div class="stat-card">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
        </div>

        <div class="stat-card">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
        </div>

        <div class="stat-card">
            <h3>Total Sales</h3>
            <p>Rs. <?php echo number_format($total_sales, 2); ?></p>
        </div>
    </div>

    <div class="admin-links-box">
        <h2>Quick Actions</h2>
        <div class="admin-links">
            <a href="/gifthub/admin/admin_orders.php" class="admin-btn">View Orders</a>
            <a href="/gifthub/admin/products.php" class="admin-btn">Manage Products</a>
            <a href="/gifthub/shop.php" class="admin-btn">Go to Shop</a>
            <a href="/gifthub/orders.php" class="admin-btn">My Orders</a>
            <a href="/gifthub/logout.php" class="admin-btn">Logout</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>