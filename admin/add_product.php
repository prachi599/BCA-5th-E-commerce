<?php
session_start();
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

$message = "";
$message_type = "";

$name = "";
$category_id = "";
$description = "";
$price = "";
$stock = "";

// Handle form submit first
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $description = trim($_POST['description']);
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

    $image_name = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
    $image_tmp = isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '';
    $image_size = isset($_FILES['image']['size']) ? $_FILES['image']['size'] : 0;
    $image_error = isset($_FILES['image']['error']) ? $_FILES['image']['error'] : 0;

    $folder = "../uploads/products/";

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    if (empty($name) || empty($category_id) || empty($description) || empty($price) || $stock === "") {
        $message = "Please fill all required fields.";
        $message_type = "error";
    } elseif ($price <= 0) {
        $message = "Price must be greater than 0.";
        $message_type = "error";
    } elseif ($stock < 0) {
        $message = "Stock cannot be negative.";
        $message_type = "error";
    } elseif (empty($image_name)) {
        $message = "Please select an image.";
        $message_type = "error";
    } elseif ($image_error !== 0) {
        $message = "There was an error uploading the image.";
        $message_type = "error";
    } else {
        // Check category exists
        $check_category = mysqli_prepare($conn, "SELECT id FROM categories WHERE id = ?");
        mysqli_stmt_bind_param($check_category, "i", $category_id);
        mysqli_stmt_execute($check_category);
        $category_result = mysqli_stmt_get_result($check_category);

        if (mysqli_num_rows($category_result) == 0) {
            $message = "Selected category does not exist.";
            $message_type = "error";
        } else {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
            $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

            if (!in_array($image_extension, $allowed_extensions)) {
                $message = "Only JPG, JPEG, PNG, and WEBP images are allowed.";
                $message_type = "error";
            } elseif ($image_size > 2 * 1024 * 1024) {
                $message = "Image size must be less than 2MB.";
                $message_type = "error";
            } else {
                $new_image_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($image_name));
                $target_file = $folder . $new_image_name;

                // Optional: check duplicate product name
                $check_product = mysqli_prepare($conn, "SELECT id FROM products WHERE name = ?");
                mysqli_stmt_bind_param($check_product, "s", $name);
                mysqli_stmt_execute($check_product);
                $product_result = mysqli_stmt_get_result($check_product);

                if (mysqli_num_rows($product_result) > 0) {
                    $message = "A product with this name already exists.";
                    $message_type = "error";
                } else {
                    if (move_uploaded_file($image_tmp, $target_file)) {
                        $insert = mysqli_prepare($conn, "INSERT INTO products (category_id, name, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)");
                        mysqli_stmt_bind_param($insert, "issdsi", $category_id, $name, $description, $price, $new_image_name, $stock);

                        if (mysqli_stmt_execute($insert)) {
                            $message = "Product added successfully!";
                            $message_type = "success";

                            // Clear form after success
                            $name = "";
                            $category_id = "";
                            $description = "";
                            $price = "";
                            $stock = "";
                        } else {
                            // remove uploaded file if DB insert fails
                            if (file_exists($target_file)) {
                                unlink($target_file);
                            }
                            $message = "Database error: " . mysqli_error($conn);
                            $message_type = "error";
                        }
                    } else {
                        $message = "Image upload failed!";
                        $message_type = "error";
                    }
                }
            }
        }
    }
}

// Fetch categories after submit handling
$categories_query = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
if (!$categories_query) {
    die("Category query failed: " . mysqli_error($conn));
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
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .message-box.success {
        background: #dff6e4;
        color: #256b3f;
    }

    .message-box.error {
        background: #f8dce6;
        color: #7a3451;
    }

    .form-box {
        background: #ffffff;
        max-width: 900px;
        padding: 32px;
        border-radius: 24px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
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
    }

    .form-box textarea {
        min-height: 140px;
        resize: vertical;
    }

    .submit-btn {
        margin-top: 28px;
        padding: 14px 28px;
        background: #e58cab;
        color: white;
        border: none;
        border-radius: 14px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
    }

    .submit-btn:hover {
        background: #d9779b;
    }

    @media (max-width: 900px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="main">
    <h1 class="page-title">Add Product</h1>

    <?php if (!empty($message)) : ?>
        <div class="message-box <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-box">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter product name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while ($category = mysqli_fetch_assoc($categories_query)) : ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" placeholder="Enter price" value="<?php echo htmlspecialchars($price); ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" placeholder="Enter stock" value="<?php echo htmlspecialchars($stock); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Write product description" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                </div>
            </div>

            <button type="submit" name="add_product" class="submit-btn">Add Product</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>