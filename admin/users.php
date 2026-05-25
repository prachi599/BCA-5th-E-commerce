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

// Get all users
$users_query = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<style>
    .main {
        padding: 40px;
        background: #fffaf9;
        min-height: 100vh;
    }

    .users-title {
        font-size: 42px;
        color: #6f4b8b;
        margin-bottom: 35px;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .users-table thead {
        background: #6f4b8b;
        color: white;
    }

    .users-table th {
        padding: 16px;
        text-align: left;
        font-weight: bold;
    }

    .users-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #eee;
    }

    .users-table tbody tr:hover {
        background: #f9f9f9;
    }

    .role-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: bold;
    }

    .role-admin {
        background: #e58cab;
        color: white;
    }

    .role-customer {
        background: #ddd;
        color: #666;
    }
</style>

<section class="main">
    <h1 class="users-title">👥 Manage Users</h1>

    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users_query && mysqli_num_rows($users_query) > 0): ?>
                <?php while ($user = mysqli_fetch_assoc($users_query)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo htmlspecialchars($user['role'] ?? 'customer'); ?>">
                                <?php echo htmlspecialchars(ucfirst($user['role'] ?? 'customer')); ?>
                            </span>
                        </td>
                        <td><?php echo isset($user['created_at']) ? htmlspecialchars($user['created_at']) : 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php include '../includes/footer.php'; ?>
