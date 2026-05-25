<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../helpers/functions.php';
include '../config/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../shop.php");
    exit();
}

// Fetch all orders
$orders_query = mysqli_query($conn, "
    SELECT *
    FROM orders
    ORDER BY id DESC
");
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<style>

.admin-orders-section{
    padding:60px 40px;
    background:#fffaf9;
    min-height:100vh;
}

.admin-orders-container{
    max-width:1200px;
    margin:0 auto;
}

.admin-orders-title{
    text-align:center;
    font-size:42px;
    color:#6f4b8b;
    margin-bottom:40px;
}

.order-card{
    background:#fff;
    border-radius:22px;
    padding:28px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
    margin-bottom:25px;
}

.order-header{
    display:flex;
    justify-content:space-between;
    gap:20px;
    flex-wrap:wrap;
    margin-bottom:18px;
    padding-bottom:16px;
    border-bottom:1px solid #eee;
}

.order-header h3{
    margin:0;
    color:#6f4b8b;
    font-size:24px;
}

.order-header p{
    margin:6px 0 0;
    color:#555;
    font-size:16px;
}

.order-total{
    font-size:22px;
    font-weight:bold;
    color:#6f4b8b;
}

.order-items{
    margin-top:15px;
}

.order-item{
    display:flex;
    justify-content:space-between;
    gap:20px;
    padding:12px 0;
    border-bottom:1px solid #f1f1f1;
    color:#444;
    font-size:17px;
}

.status-badge{
    display:inline-block;
    padding:6px 14px;
    border-radius:30px;
    background:#f3d7df;
    color:#6f4b8b;
    font-size:14px;
    font-weight:bold;
    margin-top:10px;
}

.payment-badge{
    display:inline-block;
    padding:6px 14px;
    border-radius:30px;
    background:#eee8ff;
    color:#6f4b8b;
    font-size:14px;
    font-weight:bold;
    margin-top:10px;
    margin-left:10px;
}

.empty-orders{
    max-width:700px;
    margin:0 auto;
    background:#fff;
    border-radius:22px;
    padding:40px 30px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
    text-align:center;
}

.empty-orders h3{
    font-size:30px;
    color:#6f4b8b;
    margin-bottom:12px;
}

.empty-orders p{
    font-size:18px;
    color:#555;
}

@media (max-width:768px){

    .admin-orders-section{
        padding:40px 20px;
    }

    .admin-orders-title{
        font-size:34px;
    }

    .order-item{
        flex-direction:column;
        gap:6px;
    }
}

</style>

<section class="admin-orders-section">

    <div class="admin-orders-container">

        <h1 class="admin-orders-title">
            Admin Orders
        </h1>

        <?php if ($orders_query && mysqli_num_rows($orders_query) > 0) : ?>

            <?php while ($order = mysqli_fetch_assoc($orders_query)) : ?>

                <div class="order-card">

                    <div class="order-header">

                        <div>

                            <h3>
                                Order #<?php echo $order['id']; ?>
                            </h3>

                            <p>
                                <strong>Customer:</strong>
                                <?php echo htmlspecialchars($order['customer_name']); ?>
                            </p>

                            <p>
                                <strong>Email:</strong>
                                <?php echo htmlspecialchars($order['email']); ?>
                            </p>

                            <p>
                                <strong>Phone:</strong>
                                <?php echo htmlspecialchars($order['phone']); ?>
                            </p>

                            <p>
                                <strong>Address:</strong>
                                <?php echo htmlspecialchars($order['address']); ?>
                            </p>

                            <p>
                                <strong>Date:</strong>
                                <?php echo $order['created_at']; ?>
                            </p>

                            <div class="status-badge">
                                <?php echo $order['status']; ?>
                            </div>

                            <div class="payment-badge">
                                <?php echo $order['payment_method']; ?>
                            </div>
                            <form action="update_order.php" method="POST" style="margin-top:15px;">

    <input type="hidden" 
           name="order_id" 
           value="<?= $order['id'] ?>">

    <select name="status">

        <option value="Pending"
        <?= $order['status']=='Pending' ? 'selected' : '' ?>>
            Pending
        </option>

        <option value="Processing"
        <?= $order['status']=='Processing' ? 'selected' : '' ?>>
            Processing
        </option>

        <option value="Shipped"
        <?= $order['status']=='Shipped' ? 'selected' : '' ?>>
            Shipped
        </option>

        <option value="Delivered"
        <?= $order['status']=='Delivered' ? 'selected' : '' ?>>
            Delivered
        </option>

    </select>

    <button type="submit">
        Update
    </button>

</form>

                        </div>

                        <div class="order-total">
                            Rs. <?php echo number_format($order['total_amount'], 2); ?>
                        </div>

                    </div>

                    <div class="order-items">

                        <?php

                        $order_id = (int)$order['id'];

                        $items_query = mysqli_query($conn, "
                            SELECT order_items.*, products.name AS product_name
                            FROM order_items
                            JOIN products 
                            ON order_items.product_id = products.id
                            WHERE order_items.order_id = $order_id
                        ");

                        ?>

                        <?php if ($items_query && mysqli_num_rows($items_query) > 0) : ?>

                            <?php while ($item = mysqli_fetch_assoc($items_query)) : ?>

                                <div class="order-item">

                                    <span>
                                        <?php echo htmlspecialchars($item['product_name']); ?>
                                        ×
                                        <?php echo $item['quantity']; ?>
                                    </span>

                                    <span>
                                        Rs.
                                        <?php
                                        echo number_format(
                                            $item['price'] * $item['quantity'],
                                            2
                                        );
                                        ?>
                                    </span>

                                </div>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else : ?>

            <div class="empty-orders">

                <h3>No Orders Found</h3>

                <p>
                    No customer orders yet.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>

<?php include '../includes/footer.php'; ?>