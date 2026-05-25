<?php
// Start session with secure settings
session_set_cookie_params([
    'lifetime' => 7 * 24 * 60 * 60,  // 7 days
    'path' => '/',
    'domain' => '',
    'secure' => false,  // Set to true if using HTTPS
    'httponly' => true,  // Prevent JavaScript access
    'samesite' => 'Strict'  // CSRF protection
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config/db.php';

$message = "";

// If already logged in, send to shop
if (isset($_SESSION['user_id'])) {
    header("Location: shop.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address!";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters!";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Check if email already exists using prepared statement
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "Email already registered! <a href='login.php'>Login here</a>";
        } else {
            // Insert new user with prepared statement
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            mysqli_stmt_bind_param($insert_stmt, "sss", $name, $email, $hashed_password);

            if (mysqli_stmt_execute($insert_stmt)) {
                $message = "✓ Registration successful! <a href='login.php'>Click here to login</a>";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
    .register-section {
        min-height: 80vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #fffaf9;
        padding: 40px 20px;
    }

    .register-box {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        padding: 35px;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    }

    .register-box h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #6f4b8b;
        font-size: 36px;
    }

    .register-box input {
        width: 100%;
        padding: 14px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .register-box button {
        width: 100%;
        padding: 14px;
        background: #e58cab;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .register-box button:hover {
        background: #d9789b;
    }

    .register-message {
        text-align: center;
        margin-top: 15px;
        color: red;
        font-size: 15px;
    }

    .register-message a {
        color: #6f4b8b;
        font-weight: bold;
        text-decoration: none;
    }

    .register-message a:hover {
        text-decoration: underline;
    }

    .login-text {
        text-align: center;
        margin-top: 18px;
        font-size: 15px;
    }

    .login-text a {
        color: #6f4b8b;
        font-weight: bold;
        text-decoration: none;
    }

    .login-text a:hover {
        text-decoration: underline;
    }
</style>

<section class="register-section">
    <div class="register-box">
        <h2>Register</h2>

        <form method="POST">
            <input type="text" name="name" placeholder="Enter Name" required>
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Register</button>
        </form>

        <?php if (!empty($message)) : ?>
            <p class="register-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <p class="login-text">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>