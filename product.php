<?php
session_start();
require_once __DIR__ . '/helpers/functions.php';
include 'config/db.php';

// Redirect if no ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: shop.php");
    exit();
}

$id = (int) $_GET['id'];

$query = "SELECT * FROM products WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $query);

// Redirect if product not found
if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: shop.php");
    exit();
}

$product = mysqli_fetch_assoc($result);

// Add to cart
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = (int) $_SESSION['user_id'];
    $product_id = (int) $product['id'];

    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");

    if ($check && mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
    }

    header("Location: cart.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
    .section {
        padding: 60px 40px;
        background: #fffaf9;
        min-height: 100vh;
    }

    .product-details {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: center;
        background: #ffffff;
        padding: 35px;
        border-radius: 24px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .product-image img {
        width: 100%;
        height: 420px;
        object-fit: cover;
        border-radius: 20px;
    }

    .product-info h1 {
        font-size: 42px;
        color: #6f4b8b;
        margin-bottom: 15px;
    }

    .product-info .price {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .product-info .desc {
        font-size: 18px;
        color: #555;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .btn {
        padding: 14px 28px;
        background: #e58cab;
        color: white;
        border: none;
        border-radius: 14px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn:hover {
        background: #d9789b;
        transform: scale(1.03);
    }

    .back-btn {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        color: #6f4b8b;
        font-weight: bold;
    }

    @media (max-width: 900px) {
        .product-details {
            grid-template-columns: 1fr;
            text-align: center;
        }
    }
</style>

<section class="section">
    <div class="product-details">

        <div class="product-image">
            <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>

            <p class="price"><?php echo formatPrice($product['price']); ?></p>

            <p class="desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <form method="POST">
                <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
            </form>

            <a href="shop.php" class="back-btn">← Back to Shop</a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>