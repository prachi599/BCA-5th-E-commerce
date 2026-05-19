<?php
include 'includes/header.php';
include 'config/db.php';

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$message = "";

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

if (isset($_POST['place_order'])) {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $insert_order = "INSERT INTO orders (customer_name, email, phone, address, total_amount, status)
                     VALUES ('$customer_name', '$email', '$phone', '$address', '$total', 'Pending')";

    if (mysqli_query($conn, $insert_order)) {
        $order_id = mysqli_insert_id($conn);

        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $insert_item = "INSERT INTO order_items (order_id, product_id, quantity, price)
                            VALUES ('$order_id', '$product_id', '$quantity', '$price')";

            mysqli_query($conn, $insert_item);
        }

        unset($_SESSION['cart']);

        header("Location: order_success.php?order_id=" . $order_id);
        exit;
    } else {
        $message = "Order failed: " . mysqli_error($conn);
    }
}
?>

<?php include 'includes/navbar.php'; ?>

<style>
.checkout-box {
    max-width: 650px;
    margin: 0 auto;
    background: #fff;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}

.checkout-box label {
    display: block;
    margin-top: 15px;
    margin-bottom: 8px;
    font-weight: bold;
    color: #6d4d78;
}

.checkout-box input,
.checkout-box textarea {
    width: 100%;
    padding: 13px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 16px;
}

.checkout-box h3 {
    margin-top: 20px;
    margin-bottom: 20px;
    color: #6c4776;
    font-size: 26px;
}
</style>

<section class="section">
    <h2>Checkout</h2>

    <?php if (!empty($message)) : ?>
        <p style="background:#f3d7df; padding:12px; border-radius:8px; margin-bottom:20px;">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <div class="checkout-box">
        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="customer_name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Phone</label>
            <input type="text" name="phone" required>

            <label>Address</label>
            <textarea name="address" rows="5" required></textarea>

            <h3>Total: Rs <?php echo number_format($total, 2); ?></h3>

            <button type="submit" name="place_order" class="btn">Place Order</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>