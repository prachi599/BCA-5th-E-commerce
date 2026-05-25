<?php
// Start session with secure settings
session_set_cookie_params([
    'lifetime' => 7 * 24 * 60 * 60,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// Get cart items
$cart_query = mysqli_query(
    $conn,
    "SELECT * FROM cart WHERE user_id = $user_id"
);

if (!$cart_query || mysqli_num_rows($cart_query) == 0) {
    header("Location: cart.php");
    exit();
}

$message = "";

// Calculate total
$total = 0;
$cart_items = [];

mysqli_data_seek($cart_query, 0);

while ($cart_item = mysqli_fetch_assoc($cart_query)) {

    $product_query = mysqli_query(
        $conn,
        "SELECT * FROM products 
         WHERE id = " . $cart_item['product_id']
    );

    if ($product = mysqli_fetch_assoc($product_query)) {

        $cart_item['product_name'] = $product['name'];
        $cart_item['product_price'] = $product['price'];

        $cart_items[] = $cart_item;

        $total += $product['price'] * $cart_item['quantity'];
    }
}

// Get user data
$user_query = mysqli_query(
    $conn,
    "SELECT name, email 
     FROM users 
     WHERE id = $user_id"
);

$user_data = mysqli_fetch_assoc($user_query);

// Place order
if (isset($_POST['place_order'])) {

    $customer_name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');

    // Validation
    if (
        empty($customer_name) ||
        empty($email) ||
        empty($phone) ||
        empty($address) ||
        empty($payment_method)
    ) {

        $message = "All fields are required!";

    } else {

        // Insert order
        $insert_stmt = mysqli_prepare(
            $conn,
            "INSERT INTO orders
            (
                user_id,
                customer_name,
                email,
                phone,
                address,
                total_amount,
                payment_method,
                status
            )

            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')"
        );

        mysqli_stmt_bind_param(
            $insert_stmt,
            "issssds",
            $user_id,
            $customer_name,
            $email,
            $phone,
            $address,
            $total,
            $payment_method
        );

        if (mysqli_stmt_execute($insert_stmt)) {

            $order_id = mysqli_insert_id($conn);

            // Insert order items
            foreach ($cart_items as $item) {

                $insert_item_stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO order_items
                    (
                        order_id,
                        product_id,
                        quantity,
                        price
                    )

                    VALUES (?, ?, ?, ?)"
                );

                $price = $item['product_price'];
                $quantity = $item['quantity'];

                mysqli_stmt_bind_param(
                    $insert_item_stmt,
                    "iiii",
                    $order_id,
                    $item['product_id'],
                    $quantity,
                    $price
                );

                mysqli_stmt_execute($insert_item_stmt);
            }

            // eSewa payment
            if ($payment_method == "eSewa") {

                header(
                    "Location: payments/esewa_pay.php?order_id=" . $order_id
                );

                exit();
            }

            // COD payment
            if ($payment_method == "COD") {

                // Clear cart
                mysqli_query(
                    $conn,
                    "DELETE FROM cart WHERE user_id = $user_id"
                );

                // Order cookie
                setcookie(
                    'last_order_id',
                    $order_id,
                    time() + 3600,
                    '/'
                );

                header(
                    "Location: order_success.php?order_id=" . $order_id
                );

                exit();
            }

        } else {

            $message = "Order failed: " . mysqli_error($conn);
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>

.checkout-box{
    max-width:650px;
    margin:0 auto;
    background:#fff;
    padding:30px;
    border-radius:18px;
    box-shadow:0 4px 14px rgba(0,0,0,0.08);
}

.checkout-box label{
    display:block;
    margin-top:15px;
    margin-bottom:8px;
    font-weight:bold;
    color:#6d4d78;
}

.checkout-box input,
.checkout-box textarea,
.checkout-box select{
    width:100%;
    padding:13px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:16px;
}

.checkout-box h3{
    margin-top:20px;
    margin-bottom:20px;
    color:#6c4776;
    font-size:26px;
}

</style>

<section class="section">

    <h2>Checkout</h2>

    <?php if (!empty($message)) : ?>

        <p style="
            background:#f3d7df;
            padding:12px;
            border-radius:8px;
            margin-bottom:20px;
        ">
            <?php echo $message; ?>
        </p>

    <?php endif; ?>

    <div class="checkout-box">

        <form method="POST">

            <label>Full Name</label>

            <input
                type="text"
                name="customer_name"
                value="<?php echo htmlspecialchars($user_data['name']); ?>"
                required
            >

            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars($user_data['email']); ?>"
                required
            >

            <label>Phone</label>

            <input
                type="text"
                name="phone"
                required
            >

            <label>Address</label>

            <textarea
                name="address"
                rows="5"
                required
            ></textarea>

            <label>Payment Method</label>

            <select name="payment_method" required>

                <option value="">
                    Select Payment
                </option>

                <option value="COD">
                    Cash on Delivery
                </option>

                <option value="eSewa">
                    eSewa
                </option>

            </select>

            <h3>
                Total: Rs
                <?php echo number_format($total, 2); ?>
            </h3>

            <button
                type="submit"
                name="place_order"
                class="btn"
            >
                Place Order
            </button>

        </form>

    </div>

</section>

<?php include 'includes/footer.php'; ?>