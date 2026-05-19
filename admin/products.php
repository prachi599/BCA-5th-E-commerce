<?php
session_start();
require_once __DIR__ . '/../helpers/functions.php';
include '../config/db.php';

// Admin protection
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../shop.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<style>
    .main {
        padding: 40px;
        background: #fffaf9;
        min-height: 100vh;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .page-title {
        font-size: 42px;
        color: #6f4b8b;
        margin: 0;
    }

    .add-btn {
        display: inline-block;
        padding: 14px 22px;
        background: #e58cab;
        color: white;
        text-decoration: none;
        border-radius: 14px;
        font-weight: bold;
        font-size: 16px;
        transition: 0.3s ease;
    }

    .add-btn:hover {
        background: #d9789b;
        transform: translateY(-1px);
    }

    .table-box {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    thead {
        background: #f8e8f0;
    }

    th {
        color: #6f4b8b;
        font-size: 16px;
        font-weight: 700;
        padding: 18px 16px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }

    td {
        padding: 16px;
        text-align: center;
        border-bottom: 1px solid #f3f3f3;
        color: #444;
        font-size: 15px;
        vertical-align: middle;
    }

    tbody tr:hover {
        background: #fff8fb;
    }

    .product-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 12px;
        display: block;
        margin: 0 auto;
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
    }

    .product-name {
        font-weight: 600;
        color: #333;
    }

    .stock-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
        background: #f4e8fb;
        color: #6f4b8b;
    }

    .action-group {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-block;
        padding: 10px 14px;
        border-radius: 10px;
        text-decoration: none;
        color: white;
        font-size: 14px;
        font-weight: bold;
        transition: 0.3s ease;
    }

    .action-btn.edit {
        background: #8b6fa8;
    }

    .action-btn.edit:hover {
        background: #775b94;
    }

    .action-btn.delete {
        background: #c86a6a;
    }

    .action-btn.delete:hover {
        background: #b85c5c;
    }

    .empty-box {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        padding: 40px 30px;
        text-align: center;
    }

    .empty-box h3 {
        font-size: 28px;
        color: #6f4b8b;
        margin-bottom: 10px;
    }

    .empty-box p {
        font-size: 17px;
        color: #666;
        margin: 0;
    }

    @media (max-width: 900px) {
        .main {
            padding: 24px;
        }

        .page-title {
            font-size: 34px;
        }
    }
</style>

<div class="main">
    <div class="page-header">
        <h1 class="page-title">Manage Products</h1>
        <a href="add_product.php" class="add-btn">+ Add Product</a>
    </div>

    <?php if (mysqli_num_rows($result) > 0) : ?>
        <div class="table-box">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>

                                <td>
                                    <img
                                        src="/gifthub/uploads/products/<?php echo htmlspecialchars($row['image']); ?>"
                                        alt="<?php echo htmlspecialchars($row['name']); ?>"
                                        class="product-img"
                                    >
                                </td>

                                <td class="product-name">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </td>

                                <td>
                                    Rs.<?php echo number_format($row['price']); ?>
                                </td>

                                <td>
                                    <span class="stock-badge">
                                        <?php echo (int)$row['stock']; ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="action-group">
                                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="action-btn edit">Edit</a>
                                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else : ?>
        <div class="empty-box">
            <h3>No products found</h3>
            <p>Start by adding your first product.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>