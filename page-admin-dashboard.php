<?php
/*
Template Name: Admin Dashboard
Description: Template for Ecommerce Admin Dashboard
*/

// Check quyền admin
if (!current_user_can('manage_options')) {
    wp_die('Bạn không có quyền truy cập trang này.');
}
?>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <h2 class="logo">Ecommerce Admin</h2>
        <ul class="admin-menu">
            <li><a href="<?php echo site_url('/admin-dashboard'); ?>">Bảng điều khiển</a></li>
            <li><a href="<?php echo site_url('/admin-products'); ?>">Sản phẩm</a></li>
            <li><a href="<?php echo site_url('/admin-orders'); ?>">Đơn hàng</a></li>
            <li><a href="<?php echo site_url('/admin-customers'); ?>">Khách hàng</a></li>
            <li><a href="<?php echo site_url('/admin-settings'); ?>">Cài đặt</a></li>
        </ul>
    </aside>

    <!-- Main content -->
    <div class="admin-content">
        <header class="admin-header">
            <div class="admin-header-left">
                <h1>Dashboard</h1>
            </div>
            <div class="admin-header-right">
                <span>Xin chào, <?php echo wp_get_current_user()->display_name; ?></span>
            </div>
        </header>

        <main class="admin-main">
            <!-- Shortcode để hiển thị cards -->
            <?php echo do_shortcode('[ecommerce_admin_cards]'); ?>

            <!-- Shortcode để hiển thị bảng đơn hàng -->
            <?php echo do_shortcode('[ecommerce_admin_orders]'); ?>
        </main>
    </div>
</div>




<style>
    

    /* Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f4f6f9;
        color: #333;
    }

    /* Layout */
    .admin-wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar */
    .admin-sidebar {
        width: 250px;
        background: #2c3e50;
        padding: 20px;
        color: white;
    }

    .admin-sidebar .logo {
        font-size: 1.5em;
        text-align: center;
        margin-bottom: 40px;
    }

    .admin-menu {
        list-style: none;
    }

    .admin-menu li {
        margin: 20px 0;
    }

    .admin-menu a {
        color: white;
        text-decoration: none;
        font-size: 1.1em;
    }

    .admin-menu a:hover {
        text-decoration: underline;
    }

    /* Main Content */
    .admin-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Header */
    .admin-header {
        background: #fff;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
    }

    /* Main */
    .admin-main {
        padding: 20px;
        flex: 1;
    }

    /* Cards */
    .admin-cards {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .card {
        background: white;
        flex: 1;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .card h3 {
        margin-bottom: 10px;
        font-size: 1.2em;
        color: #3498db;
    }

    /* Table */
    .admin-table {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .admin-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table th, .admin-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    .admin-table th {
        background: #ecf0f1;
    }

    .admin-table tr:hover {
        background: #f9f9f9;
    }

</style>