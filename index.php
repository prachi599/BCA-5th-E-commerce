<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config/db.php';

// Fetch latest products only
$products = mysqli_query($conn, "
    SELECT * FROM products 
    ORDER BY id DESC 
    LIMIT 4
");
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>

.view-more-container {
    text-align: center;
    margin-top: 40px;
}

.view-more-btn {
    display: inline-block;
    padding: 14px 30px;
    background: #6f4b8b;
    color: white;
    text-decoration: none;
    border-radius: 14px;
    font-size: 18px;
    font-weight: bold;
    transition: 0.3s;
}

.view-more-btn:hover {
    background: #5a3c72;
    transform: scale(1.05);
}

/* CATEGORY */

.category-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 35px;
}

.category-card {
    width: 100%;
    max-width: 390px;
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    text-align: center;
    padding-bottom: 20px;
    transition: 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-card img {
    width: 100%;
    height: 260px;
    object-fit: cover;
}

.category-card h3 {
    font-size: 28px;
    color: #6f4b8b;
    margin: 18px 0;
}

/* FEATURED PRODUCTS */

.product-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
}

.card {
    width: 100%;
    max-width: 320px;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    text-align: center;
    padding-bottom: 20px;
}

.card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.card h3 {
    font-size: 22px;
    color: #6f4b8b;
    margin: 18px 0;
}

.btn {
    display: inline-block;
    background: #f08bb4;
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.btn:hover {
    background: #e46a9c;
}

</style>

<!-- HERO -->

<section class="hero">
    <div class="hero-text">
        <h1>
            Find the Perfect Gift <br>
            for Your Loved Ones
        </h1>

        <p>
            Beautifully curated gifts for every special moment.
            <br>
            Make memories with gifts that truly matter.
        </p>

        <a href="shop.php" class="btn">Shop Now</a>
    </div>
</section>

<!-- SHOP BY CATEGORY -->

<section class="section">

    <h2>Shop By Category</h2>

    <div class="category-container">

        <?php
        $categories = mysqli_query($conn, "
            SELECT * FROM categories 
            ORDER BY id DESC
        ");

        while($cat = mysqli_fetch_assoc($categories)) :
        ?>

        <div class="category-card">

            <img 
                src="uploads/categories/<?php echo htmlspecialchars($cat['image']); ?>"
                alt="<?php echo htmlspecialchars($cat['name']); ?>"
            >

            <h3>
                <?php echo htmlspecialchars($cat['name']); ?>
            </h3>

            <!-- OPEN CATEGORY PRODUCTS -->

            <a 
                href="shop.php?category=<?php echo $cat['id']; ?>" 
                class="btn"
            >
                Explore
            </a>

        </div>

        <?php endwhile; ?>

    </div>

</section>

<!-- FEATURED PRODUCTS -->

<section class="section">

    <h2>Featured Products</h2>

    <div class="product-grid">

        <?php while ($row = mysqli_fetch_assoc($products)) : ?>

        <div class="card">

            <img 
                src="uploads/products/<?php echo htmlspecialchars($row['image']); ?>" 
                alt="<?php echo htmlspecialchars($row['name']); ?>"
            >

            <h3>
                <?php echo htmlspecialchars($row['name']); ?>
            </h3>

            <!-- NO PRICE HERE -->

            <a 
                href="product.php?id=<?php echo $row['id']; ?>" 
                class="btn"
            >
                View Product
            </a>

        </div>

        <?php endwhile; ?>

    </div>

    <div class="view-more-container">

        <a href="shop.php" class="view-more-btn">
            View More Products
        </a>

    </div>

</section>

<?php include 'includes/footer.php'; ?>