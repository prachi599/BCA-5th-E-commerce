<?php
include 'config/db.php';
require_once __DIR__ . '/helpers/functions.php';

$category_id = isset($_GET['category']) ? (int) $_GET['category'] : 0;

if ($category_id > 0) {
    $query = "SELECT * FROM products WHERE category_id = $category_id ORDER BY id DESC";
    $page_title = "Category Products";
} else {
    $query = "SELECT * FROM products ORDER BY id DESC";
    $page_title = "All Gifts";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
    .section {
        padding: 50px 40px;
        background: #fffaf9;
        min-height: 100vh;
    }

    .section h2 {
        text-align: center;
        font-size: 42px;
        color: #7a5a96;
        margin-bottom: 40px;
    }

    .product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
    width: 100%;
}

@media (max-width: 1200px) {
    .product-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 900px) {
    .product-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .product-grid {
        grid-template-columns: 1fr;
    }
}
    .card {
        background: #ffffff;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        text-align: center;
        padding-bottom: 24px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 26px rgba(0, 0, 0, 0.12);
    }

    .card img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        display: block;
    }

    .card h3 {
        font-size: 24px;
        color: #6f4b8b;
        margin: 20px 15px 10px;
    }

    .card .price {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 18px;
    }

    .product-buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin-top: 10px;
    }

    .product-buttons .btn {
        display: inline-block;
        width: 180px;
        text-align: center;
        padding: 12px 18px;
        background: #e58cab;
        color: #fff;
        text-decoration: none;
        border-radius: 14px;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    .product-buttons .btn:hover {
        background: #d9789b;
        transform: scale(1.03);
    }

    .no-products {
        text-align: center;
        width: 100%;
        font-size: 18px;
        color: #555;
    }
</style>

<section class="section">
    <h2><?php echo htmlspecialchars($page_title); ?></h2>

    <div class="product-grid">
        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="card">
                    <img src="uploads/products/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">

                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                    <p class="price"><?php echo formatPrice($row['price']); ?></p>

                    <div class="product-buttons">
                        <a href="product.php?id=<?php echo $row['id']; ?>" class="btn">View Product</a>
                        <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn">Add to Cart</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p class="no-products">No products available in this category.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>