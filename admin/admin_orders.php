<?php
session_start();
require_once __DIR__ . '/../helpers/functions.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../shop.php");
    exit();
}

$orders_query = mysqli_query($conn, "
    SELECT orders.*, users.name AS user_name
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.id DESC
");
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<style>
    .admin-orders-section {
        padding: 60px 40px;
        background: #fffaf9;
        min-height: 100vh;
    }

    .admin-orders-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .admin-orders-title {
        text-align: center;
        font-size: 42px;
        color: #6f4b8b;
        margin-bottom: 40px;
    }

    .order-card {
        background: #fff;
        border-radius: 22px;
        padding: 28px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eee;
    }

    .order-header h3 {
        margin: 0;
        color: #6f4b8b;
        font-size: 24px;
    }

    .order-header p {
        margin: 6px 0 0;
        color: #555;
        font-size: 16px;
    }

    .order-total {
        font-size: 22px;
        font-weight: bold;
        color: #6f4b8b;
    }

    .order-items {
        margin-top: 15px;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f1f1;
        color: #444;
        font-size: 17px;
    }

    .empty-orders {
        max-width: 700px;
        margin: 0 auto;
        background: #fff;
        border-radius: 22px;
        padding: 40px 30px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        text-align: center;
    }

    .empty-orders h3 {
        font-size: 30px;
        color: #6f4b8b;
        margin-bottom: 12px;
    }

    .empty-orders p {
        font-size: 18px;
        color: #555;
    }

    @media (max-width: 768px) {
        .admin-orders-section {
            padding: 40px 20px;
        }

        .admin-orders-title {
            font-size: 34px;
        }

        .order-item {
            flex-direction: column;
            gap: 6px;
        }
    }
</style>

<section class="admin-orders-section">
    <div class="admin-orders-container">
        <h1 class="admin-orders-title">Admin Orders</h1>

        <?php if ($orders_query && mysqli_num_rows($orders_query) > 0) : ?>
            <?php while ($order = mysqli_fetch_assoc($orders_query)) : ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p><strong>User:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
                            <p><strong>Date:</strong> <?php echo $order['created_at']; ?></p>
                        </div>

                        <div class="order-total">
                            Total: Rs.<?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                    </div>

                    <div class="order-items">
                        <?php
                        $order_id = (int) $order['id'];
                        $items_query = mysqli_query($conn, "
                            SELECT * FROM order_items
                            WHERE order_id = $order_id
                            ORDER BY id ASC
                        ");

                        if ($items_query && mysqli_num_rows($items_query) > 0) :
                            while ($item = mysqli_fetch_assoc($items_query)) :
                        ?>
                            <div class="order-item">
                                <span>
                                    <?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?>
                                </span>
                                <span>
                                    Rs.<?php echo number_format($item['subtotal'], 2); ?>
                                </span>
                            </div>
                        <?php
                            endwhile;
                        endif;
                        ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="empty-orders">
                <h3>No orders found</h3>
                <p>No customer orders yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>