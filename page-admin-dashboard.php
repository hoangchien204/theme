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
    <?php get_template_part('admin-sidebar'); ?>

    <!-- Main content -->
    <div class="admin-content">
       
        <main class="admin-main">
            <!-- Shortcode để hiển thị cards -->
            <?php echo do_shortcode('[ecommerce_admin_cards]'); ?>

            <!-- Shortcode để hiển thị bảng đơn hàng -->
            <?php echo do_shortcode('[ecommerce_admin_orders]'); ?>
        </main>
    </div>
</div>




<style>
    

   /* Reset and Full-Screen Setup */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    width: 100%;
    height: 100vh;
    margin: 0;
    padding: 0;
    overflow-x: hidden; /* Prevent horizontal scroll */
}

body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f9;
    color: #333;
    display: flex; /* Ensure body stretches */
    flex-direction: column;
}

/* Layout */
.admin-wrapper {
    display: flex;
    min-height: 100vh;
    width: 100vw; /* Full width of the viewport */
}

/* Sidebar */
.admin-sidebar {
    width: 250px;
    background: #2c3e50;
    padding: 20px;
    color: white;
    height: 100vh; /* Full height of the viewport */
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
    width: calc(100vw - 250px); /* Full width minus sidebar */
}

/* Header */
.admin-header {
    background: #fff;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
    width: 100%;
}

/* Main */
.admin-main {
    padding: 20px;
    flex: 1;
    width: 100%;
}

/* Cards */
.admin-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    width: 100%;
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
    width: 100%;
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