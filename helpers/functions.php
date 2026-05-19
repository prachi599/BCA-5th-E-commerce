<?php

function formatPrice($amount) {
    return 'Rs. ' . number_format($amount, 2);
}

function renderProductCard($product, $showPrice = false) {
?>
    <div class="card">
        <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>">

        <h3><?php echo htmlspecialchars($product['name']); ?></h3>

        <?php if ($showPrice): ?>
            <p class="price"><?php echo formatPrice($product['price']); ?></p>
        <?php endif; ?>

        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn">
            View Product
        </a>
    </div>
<?php
}

?>