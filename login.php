<?php
session_start();
include 'config/db.php';

$message = "";

// If already logged in, send to shop
if (isset($_SESSION['user_id'])) {
    header("Location: shop.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $email = mysqli_real_escape_string($conn, $email);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE LOWER(email) = LOWER('$email') LIMIT 1");

    if (!$query) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {
           $_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];
            header("Location: shop.php");
            exit();
        } else {
            $message = "Wrong password!";
        }
    } else {
        $message = "User not found!";
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
    .login-section {
        min-height: 80vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #fffaf9;
        padding: 40px 20px;
    }

    .login-box {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        padding: 35px;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    }

    .login-box h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #6f4b8b;
        font-size: 36px;
    }

    .login-box input {
        width: 100%;
        padding: 14px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .login-box button {
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

    .login-box button:hover {
        background: #d9789b;
    }

    .login-message {
        text-align: center;
        margin-top: 15px;
        color: red;
        font-size: 15px;
    }

    .register-text {
        text-align: center;
        margin-top: 18px;
        font-size: 15px;
    }

    .register-text a {
        color: #6f4b8b;
        font-weight: bold;
        text-decoration: none;
    }

    .register-text a:hover {
        text-decoration: underline;
    }
</style>

<section class="login-section">
    <div class="login-box">
        <h2>Login</h2>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>

        <?php if (!empty($message)) : ?>
            <p class="login-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <p class="register-text">
            Don’t have an account? <a href="register.php">Register</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>