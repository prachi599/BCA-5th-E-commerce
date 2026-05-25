<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
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

// Set admin session cookie
setcookie('admin_session', bin2hex(random_bytes(16)), time() + 3600, '/', '', false, true);

// Get product ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int) $_GET['id'];
$message = "";

// Fetch product
$product_query = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id LIMIT 1");

if (!$product_query || mysqli_num_rows($product_query) == 0) {
    header("Location: products.php");
    exit();
}

$product = mysqli_fetch_assoc($product_query);

// Fetch categories
$categories_query = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

if (!$categories_query) {
    die("Category query failed: " . mysqli_error($conn));
}

// Handle update
if (isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $category_id = (int) $_POST['category_id'];
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    $new_image_name = $product['image'];

    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];

        $folder = "../uploads/products/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $new_image_name = time() . "_" . basename($image_name);
        $target_file = $folder . $new_image_name;

        if (move_uploaded_file($image_tmp, $target_file)) {
            // Optional: delete old image file
            $old_file = $folder . $product['image'];
            if (!empty($product['image']) && file_exists($old_file)) {
                unlink($old_file);
            }
        } else {
            $message = "Image upload failed!";
        }
    }

    if (empty($message)) {
        $update = "UPDATE products 
                   SET category_id = '$category_id',
                       name = '$name',
                       description = '$description',
                       price = '$price',
                       image = '$new_image_name',
                       stock = '$stock'
                   WHERE id = '$product_id'";

        if (mysqli_query($conn, $update)) {
            $message = "Product updated successfully!";

            // Refresh product data after update
            $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id LIMIT 1");
            $product = mysqli_fetch_assoc($product_query);
        } else {
            $message = "Database error: " . mysqli_error($conn);
        }
    }
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

    .page-title {
        font-size: 42px;
        color: #6f4b8b;
        margin-bottom: 25px;
    }

    .message-box {
        padding: 14px 18px;
        border-radius: 14px;
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: 500;
        background: #f8dce6;
        color: #7a3451;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .form-box {
        background: #ffffff;
        max-width: 900px;
        padding: 32px;
        border-radius: 24px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .current-image {
        margin-bottom: 25px;
    }

    .current-image p {
        margin-bottom: 10px;
        color: #6f4b8b;
        font-weight: 600;
    }

    .current-image img {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-box label {
        font-size: 16px;
        font-weight: 600;
        color: #6f4b8b;
        margin-bottom: 8px;
    }

    .form-box input,
    .form-box select,
    .form-box textarea {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #e3d9e8;
        border-radius: 14px;
        font-size: 16px;
        background: #fff;
        box-sizing: border-box;
        outline: none;
        transition: 0.3s ease;
    }

    .form-box input:focus,
    .form-box select:focus,
    .form-box textarea:focus {
        border-color: #d9789b;
        box-shadow: 0 0 0 3px rgba(217, 120, 155, 0.12);
    }

    .form-box textarea {
        min-height: 140px;
        resize: vertical;
    }

    .file-input {
        padding: 12px;
        background: #fff;
    }

    .submit-btn {
        margin-top: 28px;
        padding: 14px 28px;
        background: #8b6fa8;
        color: white;
        border: none;
        border-radius: 14px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .submit-btn:hover {
        background: #775b94;
        transform: translateY(-1px);
    }

    .back-link {
        display: inline-block;
        margin-top: 18px;
        color: #6f4b8b;
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 900px) {
        .main {
            padding: 24px;
        }

        .page-title {
            font-size: 34px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="main">
    <h1 class="page-title">Edit Product</h1>

    <?php if (!empty($message)) : ?>
        <div class="message-box">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-box">
        <div class="current-image">
            <p>Current Image</p>
            <img src="/gifthub/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while ($category = mysqli_fetch_assoc($categories_query)) : ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="image">Change Product Image (optional)</label>
                    <input type="file" id="image" name="image" class="file-input" accept="image/*">
                </div>
            </div>

            <button type="submit" name="update_product" class="submit-btn">Update Product</button>
        </form>

        <a href="products.php" class="back-link">← Back to Products</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>