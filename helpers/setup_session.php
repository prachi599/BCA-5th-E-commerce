<?php
/**
 * Database Migration - Add Session/Cookie Columns to Users Table
 * Run this file once: php helpers/setup_session.php
 */

include 'config/db.php';

$message = "";
$error = "";

// Check if columns exist
$result = mysqli_query($conn, "DESCRIBE users remember_token");

if ($result && mysqli_num_rows($result) > 0) {
    $message = "✓ Database columns already exist. No action needed.";
} else {
    // Add columns if they don't exist
    $query1 = mysqli_query($conn, "ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) NULL DEFAULT NULL AFTER password");
    $query2 = mysqli_query($conn, "ALTER TABLE users ADD COLUMN token_expiry DATETIME NULL DEFAULT NULL AFTER remember_token");

    if ($query1 && $query2) {
        $message = "✓ Successfully added session columns to users table!";
        $message .= "\n✓ Columns added: remember_token, token_expiry";
    } else {
        $error = "✗ Error adding columns: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GiftHub - Database Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f8f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .setup-box {
            background: white;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            max-width: 500px;
            width: 100%;
        }

        .setup-box h1 {
            color: #6f4b8b;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #f5c6cb;
        }

        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid #17a2b8;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #6f4b8b;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="setup-box">
        <h1>🎁 GiftHub Database Setup</h1>

        <?php if (!empty($message)): ?>
            <div class="success"><?php echo nl2br(htmlspecialchars($message)); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo nl2br(htmlspecialchars($error)); ?></div>
        <?php endif; ?>

        <div class="info">
            <strong>✓ Session & Cookie Features Enabled:</strong>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Secure session management</li>
                <li>Remember me functionality (30 days)</li>
                <li>Automatic login restoration</li>
                <li>Secure cookie handling</li>
            </ul>
        </div>

        <div class="back-link">
            <a href="/gifthub/index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
