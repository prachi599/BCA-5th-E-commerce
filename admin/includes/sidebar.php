<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .admin-layout {
        display: flex;
        min-height: 100vh;
        background: #fffaf9;
    }

    .admin-sidebar {
        width: 280px;
        background: #e36994;
        color: white;
        padding: 35px 25px;
        box-sizing: border-box;
        position: sticky;
        top: 0;
        height: 100vh;
        flex-shrink: 0;
    }

    .admin-logo {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 35px;
        color: #fff;
    }

    .admin-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .admin-menu li {
        margin-bottom: 14px;
    }

    .admin-menu a {
        display: block;
        padding: 14px 16px;
        border-radius: 14px;
        color: #fff;
        text-decoration: none;
        font-size: 17px;
        font-weight: 500;
        transition: 0.3s ease;
    }

    .admin-menu a:hover,
    .admin-menu a.active {
        background: rgba(255, 255, 255, 0.14);
    }

    .admin-content {
        flex: 1;
        min-width: 0;
    }

    @media (max-width: 900px) {
        .admin-layout {
            flex-direction: column;
        }

        .admin-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            padding: 20px;
        }

        .admin-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .admin-menu li {
            margin-bottom: 0;
        }

        .admin-menu a {
            padding: 10px 14px;
            font-size: 15px;
        }
    }
</style>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-logo">GiftHub Admin</div>

        <ul class="admin-menu">
            <li>
                <a href="/gifthub/admin/index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="/gifthub/admin/products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
                    Products
                </a>
            </li>
            <li>
                <a href="/gifthub/admin/add_product.php" class="<?php echo $current_page == 'add_product.php' ? 'active' : ''; ?>">
                    Add Product
                </a>
            </li>
            <li>
                <a href="/gifthub/admin/admin_orders.php" class="<?php echo $current_page == 'admin_orders.php' ? 'active' : ''; ?>">
                    Orders
                </a>
            </li>
            <li>
                <a href="/gifthub/admin/users.php" class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                    Users
                </a>
            </li>
        </ul>
    </aside>

    <div class="admin-content">