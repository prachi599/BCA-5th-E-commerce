<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$message = "";
$message_type = "";

// Handle form submit
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    
    // Validation
    if (empty($name)) {
        $message = "Category name is required!";
        $message_type = "error";
    } else {
        // Check if category already exists
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM categories WHERE LOWER(name) = LOWER(?)");
        mysqli_stmt_bind_param($check_stmt, "s", $name);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "Category already exists!";
            $message_type = "error";
        } else {
            // Insert new category
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
            mysqli_stmt_bind_param($insert_stmt, "s", $name);

            if (mysqli_stmt_execute($insert_stmt)) {
                $message = "✓ Category added successfully!";
                $message_type = "success";
            } else {
                $message = "Failed to add category. Please try again.";
                $message_type = "error";
            }
        }
    }
}

// Get all categories
$categories_query = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
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
        margin-bottom: 35px;
    }

    .form-section {
        background: white;
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 40px;
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #6f4b8b;
        font-weight: bold;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .form-group button {
        background: #e58cab;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }

    .form-group button:hover {
        background: #d9789b;
    }

    .message {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .message.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .categories-section {
        background: white;
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .categories-title {
        font-size: 28px;
        color: #6f4b8b;
        margin-bottom: 20px;
    }

    .categories-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .category-item {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 12px;
        border-left: 4px solid #e58cab;
        transition: 0.3s;
    }

    .category-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .category-item h3 {
        margin: 0 0 10px;
        color: #6f4b8b;
        font-size: 20px;
    }

    .category-item p {
        margin: 0;
        color: #999;
        font-size: 14px;
    }
</style>

<section class="main">
    <h1 class="page-title">📁 Add Category</h1>

    <div class="form-section">
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="e.g., Gifts for Him, Gifts for Her" 
                    required
                >
            </div>

            <div class="form-group">
                <button type="submit" name="add_category">Add Category</button>
            </div>
        </form>
    </div>

    <div class="categories-section">
        <h2 class="categories-title">Existing Categories</h2>
        
        <?php if ($categories_query && mysqli_num_rows($categories_query) > 0): ?>
            <div class="categories-list">
                <?php while ($category = mysqli_fetch_assoc($categories_query)): ?>
                    <div class="category-item">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p>ID: <?php echo htmlspecialchars($category['id']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="color: #999; text-align: center; padding: 40px;">No categories found. Create your first category!</p>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
