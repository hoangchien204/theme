<?php
// admin-layout.php
/*
Template Name: Admin
Description: Template for Ecommerce Admin Dashboard
*/
// Kiểm tra quyền
if (!current_user_can('manage_options')) {
    wp_die('Bạn không có quyền truy cập trang này.');
}

// Xác định tiêu đề dựa vào đường dẫn URL hiện tại
$current_url = $_SERVER['REQUEST_URI'];
if (strpos($current_url, 'admin-products') !== false) {
    $admin_page_title = 'Quản lý Sản phẩm';
} elseif (strpos($current_url, 'admin-orders') !== false) {
    $admin_page_title = 'Quản lý Đơn hàng';
} elseif (strpos($current_url, 'admin-customers') !== false) {
    $admin_page_title = 'Quản lý Khách hàng';
} elseif (strpos($current_url, 'page-categories') !== false) {
    $admin_page_title = 'Danh mục sản phẩm';
} else {
    $admin_page_title = 'Quản lý';
}
?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h2 class="logo">Ecommerce Admin</h2>
        <ul class="admin-menu">
           
            <li><a href="<?php echo site_url('/admin-products'); ?>">Sản phẩm</a></li>
            <li><a href="<?php echo site_url('/admin-orders'); ?>">Đơn hàng</a></li>
            <li><a href="<?php echo site_url('/page-categories'); ?>">Danh mục sản phẩm</a></li>
            <li><a href="<?php echo site_url('/page-statistical-report'); ?>">Thống kê</a></li>
        </ul>
    </aside>

    <div class="admin-content">
        <header class="admin-header">
            <div class="admin-header-left">
                <h1><?php echo $admin_page_title; ?></h1>
            </div>
            <div class="admin-header-right">
                <span>Xin chào, <?php echo wp_get_current_user()->display_name; ?></span>
            </div>
        </header>

        <main class="admin-main">

<style>
  /* Sidebar Styling */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

.admin-wrapper {
    display: flex;
    min-height: 100vh;
    width: 100vw;
        height: 100%;
}

.admin-sidebar {
    width: 250px;
    background: #2c3e50;
    padding: 20px;
    color: white;
    height: 100vh;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    position: relative;
    height: 100%;
}

.admin-sidebar .logo {
    font-size: 1.5em;
    text-align: center;
    margin-bottom: 40px;
    font-weight: bold;
    color: #ecf0f1;
}

.admin-menu {
    list-style: none;
    padding: 0;
}

.admin-menu li {
    margin: 20px 0;
}

.admin-menu a {
    color: #ecf0f1;
    text-decoration: none;
    font-size: 1.1em;
    display: block;
    padding: 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.admin-menu a:hover {
    background-color: #34495e;
    color: #ffffff;
    text-decoration: none;
}

/* Content Styling */
.admin-content {
    flex-grow: 1;
    padding: 20px;
    /* margin-left: 250px;  */
}
.admin-header {
    background: #fff;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 60px;
}

.admin-header-left h1 {
    font-size: 1.5em;
    color: #2c3e50;
}

/* Điều chỉnh khoảng cách cho .admin-header-right để đưa nó gần sidebar hơn */
.admin-header-right {
    font-size: 1em;
    color: #7f8c8d;
    margin-left: 0; /* Xóa khoảng cách từ bên trái */
    padding-left: 10px; /* Giảm khoảng cách với sidebar */
    padding-right: 10px; /* Tùy chỉnh nếu cần */
}

.admin-header-right span {
    padding: 5px 10px;
    background: #f9f9f9;
    border-radius: 5px;
}
</style>