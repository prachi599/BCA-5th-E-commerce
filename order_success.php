<?php
session_start();
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
    .success-section {
        min-height: 80vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #fffaf9;
        padding: 40px 20px;
    }

    .success-box {
        background: #fff;
        padding: 40px;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        text-align: center;
        max-width: 600px;
        width: 100%;
    }

    .success-box h1 {
        color: #6f4b8b;
        margin-bottom: 15px;
    }

    .success-box p {
        font-size: 18px;
        color: #555;
        margin-bottom: 25px;
    }

    .success-btn {
        display: inline-block;
        padding: 14px 24px;
        background: #e58cab;
        color: white;
        text-decoration: none;
        border-radius: 14px;
        font-weight: bold;
    }

    .success-btn:hover {
        background: #d9789b;
    }
</style>

<section class="success-section">
    <div class="success-box">
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for shopping with GiftHub.</p>
        <a href="shop.php" class="success-btn">Continue Shopping</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>