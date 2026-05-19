<?php
session_start();
require_once __DIR__ . '/helpers/functions.php';
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");

    if ($check && mysqli_num_rows($check) > 0) {
        $cart_item = mysqli_fetch_assoc($check);

        if ($action === 'increase') {
            mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id");
        } elseif ($action === 'decrease') {
            if ($cart_item['quantity'] > 1) {
                mysqli_query($conn, "UPDATE cart SET quantity = quantity - 1 WHERE user_id = $user_id AND product_id = $product_id");
            } else {
                mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id");
            }
        } elseif ($action === 'remove') {
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id");
        }
    }

    header("Location: cart.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
    .cart-section {
        padding: 60px 40px;
        background: #fffaf9;
        min-height: 100vh;
    }

    .cart-container {
        max-width: 1150px;
        margin: 0 auto;
    }

    .cart-title {
        text-align: center;
        font-size: 42px;
        color: #6f4b8b;
        margin-bottom: 40px;
    }

    .cart-item {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 25px;
        align-items: center;
        background: #ffffff;
        padding: 22px;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
    }

    .cart-item img {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 18px;
        display: block;
        background: #f4f4f4;
    }

    .cart-details h2 {
        margin: 0 0 12px;
        font-size: 28px;
        color: #6f4b8b;
    }

    .cart-details p {
        margin: 8px 0;
        font-size: 18px;
        color: #444;
    }

    .cart-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 18px;
    }

    .qty-btn,
    .remove-btn,
    .checkout-btn,
    .continue-btn {
        border: none;
        color: #fff;
        padding: 12px 18px;
        border-radius: 14px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .qty-btn {
        background: #e58cab;
        min-width: 48px;
        text-align: center;
    }

    .qty-btn:hover {
        background: #d9789b;
        transform: scale(1.03);
    }

    .remove-btn {
        background: #c86a6a;
    }

    .remove-btn:hover {
        background: #b85c5c;
        transform: scale(1.03);
    }

    .qty-number {
        min-width: 30px;
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }

    .cart-summary {
        margin-top: 35px;
        background: #ffffff;
        border-radius: 22px;
        padding: 28px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        text-align: right;
    }

    .cart-summary h2 {
        font-size: 36px;
        color: #6f4b8b;
        margin-bottom: 20px;
    }

    .checkout-btn {
    display: inline-block;
    background: #f08fb2;
    color: white;
    padding: 14px 28px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    margin-top: 15px;
}
    .checkout-btn:hover {
        background: #d9789b;
        transform: scale(1.03);
    }

    .continue-btn {
        background: #8b6fa8;
    }

    .continue-btn:hover {
        background: #775b94;
        transform: scale(1.03);
    }

    .empty-cart-box {
        max-width: 700px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 22px;
        padding: 40px 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .empty-cart-box h3 {
        font-size: 30px;
        color: #6f4b8b;
        margin-bottom: 12px;
    }

    .empty-cart-box p {
        font-size: 18px;
        color: #555;
        margin-bottom: 25px;
    }

    form {
        display: inline;
        margin: 0;
    }

    @media (max-width: 768px) {
        .cart-section {
            padding: 40px 20px;
        }

        .cart-title {
            font-size: 34px;
        }

        .cart-item {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .cart-item img {
            margin: 0 auto;
        }

        .cart-actions {
            justify-content: center;
        }

        .cart-summary {
            text-align: center;
        }

        .checkout-btn,
        .continue-btn {
            margin: 8px 6px 0;
        }
    }
</style>

<section class="cart-section">
    <div class="cart-container">
        <h1 class="cart-title">Your Cart</h1>

        <?php
        $grand_total = 0;

        $query = mysqli_query($conn, "
            SELECT cart.product_id, cart.quantity, products.name, products.price, products.image
            FROM cart
            JOIN products ON cart.product_id = products.id
            WHERE cart.user_id = $user_id
            ORDER BY cart.id DESC
        ");

        if ($query && mysqli_num_rows($query) > 0) {
            while ($item = mysqli_fetch_assoc($query)) {
                $product_id = $item['product_id'];
                $name = $item['name'];
                $price = $item['price'];
                $quantity = $item['quantity'];
                $image = $item['image'];
                $subtotal = $price * $quantity;
                $grand_total += $subtotal;

                $imagePath = 'uploads/products/' . $image;
                ?>

                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($name); ?>">

                    <div class="cart-details">
                        <h2><?php echo htmlspecialchars($name); ?></h2>
                        <p>Price: <?php echo formatPrice($price); ?></p>
                        <p>Quantity: <?php echo $quantity; ?></p>
                        <p>Subtotal: <?php echo formatPrice($subtotal); ?></p>

                        <div class="cart-actions">
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="hidden" name="action" value="decrease">
                                <button type="submit" class="qty-btn">-</button>
                            </form>

                            <span class="qty-number"><?php echo $quantity; ?></span>

                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="hidden" name="action" value="increase">
                                <button type="submit" class="qty-btn">+</button>
                            </form>

                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
            }
            ?>

            <div class="cart-summary">
                <h2>Total: Rs.<?php echo number_format($grand_total, 2); ?></h2>
                <a href="shop.php" class="continue-btn">Continue Shopping</a>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>

            <?php
        } else {
            ?>
            <div class="empty-cart-box">
                <h3>Your cart is empty</h3>
                <p>Add something beautiful from our shop.</p>
                <a href="shop.php" class="continue-btn">Go to Shop</a>
            </div>
            <?php
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
