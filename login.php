<?php
// Start session with secure settings
session_set_cookie_params([
    'lifetime' => 7 * 24 * 60 * 60,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
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
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

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
           $_SESSION['user_role'] = isset($user['role']) ? $user['role'] : 'customer';
            
            // Set remember me cookie if checkbox is checked
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                mysqli_query($conn, "UPDATE users SET remember_token = '$token', token_expiry = '$expiry' WHERE id = " . $user['id']);
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }
            
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

    .remember-checkbox {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        font-size: 15px;
    }

    .remember-checkbox input {
        width: auto;
        margin-right: 8px;
        margin-bottom: 0;
        cursor: pointer;
    }

    .remember-checkbox label {
        cursor: pointer;
        color: #555;
    }
</style>

<section class="login-section">
    <div class="login-box">
        <h2>Login</h2>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            
            <div class="remember-checkbox">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me for 30 days</label>
            </div>
            
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