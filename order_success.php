<?php
include 'config/session.php';
include 'config/db.php';

if (!isset($_GET['order_id'])) {
    header("Location: shop.php");
    exit();
}

$order_id = (int) $_GET['order_id'];

$query = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id");

if (!$query || mysqli_num_rows($query) == 0) {
    header("Location: shop.php");
    exit();
}

$order = mysqli_fetch_assoc($query);
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
.success-box{
    max-width:600px;
    margin:60px auto;
    background:#fff;
    padding:40px;
    border-radius:18px;
    text-align:center;
    box-shadow:0 4px 18px rgba(0,0,0,0.08);
}

.success-box h2{
    color:#4CAF50;
    margin-bottom:20px;
    font-size:34px;
}

.success-box p{
    font-size:18px;
    margin:10px 0;
    color:#555;
}

.success-box a{
    display:inline-block;
    margin-top:25px;
    padding:12px 24px;
    background:#e58cab;
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
}
</style>

<section class="section">

    <div class="success-box">

        <h2>Order Successful 🎉</h2>

        <p>Thank you for your order.</p>

        <p>
            Your Order ID:
            <strong>#<?php echo $order['id']; ?></strong>
        </p>

        <p>
            Total Amount:
            <strong>
                Rs <?php echo number_format($order['total_amount'],2); ?>
            </strong>
        </p>

        <p>
            Status:
            <strong><?php echo $order['status']; ?></strong>
        </p>

        <a href="shop.php">
            Continue Shopping
        </a>

    </div>

</section>

<?php include 'includes/footer.php'; ?>