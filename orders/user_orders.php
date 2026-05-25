<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn,
"SELECT * FROM orders WHERE user_id='$user_id' ORDER BY id DESC");
?>

<h2>My Orders</h2>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">

    <p><b>Order ID:</b> <?= $row['id'] ?></p>
    <p><b>Amount:</b> Rs. <?= $row['total_amount'] ?></p>
    <p><b>Payment Method:</b> <?= $row['payment_method'] ?></p>
    <p><b>Status:</b> <?= $row['payment_status'] ?></p>

</div>

<?php } ?>