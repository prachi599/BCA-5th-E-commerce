<?php
session_start();
require_once __DIR__ . '/helpers/functions.php';
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$orders_query = mysqli_query($conn, "
    SELECT * FROM orders
    WHERE user_id = $user_id
    ORDER BY id DESC
");
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
    .orders-section {
        padding: 60px 40px;
        background: #fffaf9;
        min-height: 100vh;
    }

    .orders-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .orders-title {
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
        margin-bottom: 25px;
    }

    .shop-btn {
        display: inline-block;
        padding: 14px 24px;
        background: #e58cab;
        color: white;
        text-decoration: none;
        border-radius: 14px;
        font-weight: bold;
        transition: 0.3s ease;
    }

    .shop-btn:hover {
        background: #d9789b;
    }

    @media (max-width: 768px) {
        .orders-section {
            padding: 40px 20px;
        }

        .orders-title {
            font-size: 34px;
        }

        .order-item {
            flex-direction: column;
            gap: 6px;
        }
    }
</style>

<section class="orders-section">
    <div class="orders-container">
        <h1 class="orders-title">My Orders</h1>

        <?php if ($orders_query && mysqli_num_rows($orders_query) > 0) : ?>
            <?php while ($order = mysqli_fetch_assoc($orders_query)) : ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p>Date: <?php echo $order['created_at']; ?></p>
                            <p>Name: <?php echo htmlspecialchars($order['full_name']); ?></p>
                            <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
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
                <h3>No orders yet</h3>
                <p>You haven’t placed any order yet.</p>
                <a href="shop.php" class="shop-btn">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>