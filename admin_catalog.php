<?php
/*
Plugin Name: Admin Category Manager
Description: Plugin quản lý danh mục cho admin với chức năng thêm, sửa, xóa.
Version: 1.0
Author: Bạn
*/

// Kiểm tra quyền truy cập
function acm_check_admin_access() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập trang này.');
    }
}

// Hiển thị danh sách danh mục
function acm_display_admin_categories() {
    acm_check_admin_access();
    ob_start();

    global $wpdb;
    $categories = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}loai ORDER BY MaLoai DESC");

    if ($categories) {
        ?>
        <div class="wrap">
            <h2>Quản lý danh mục</h2>
            <a href="?page=acm-categories&action=create" class="button button-primary">Thêm danh mục</a>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Hình ảnh</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) : 
                        $image_path = !empty($category->Hinh) ? esc_url($category->Hinh) : esc_url(get_stylesheet_directory_uri() . '/img/placeholder.jpg');
                    ?>
                        <tr>
                            <td><?= esc_html($category->MaLoai) ?></td>
                            <td><?= esc_html($category->TenLoai) ?></td>
                            <td><img src="<?= $image_path ?>" alt="Hình danh mục" style="max-width: 100px;" /></td>
                            <td>
                                <a href="?page=acm-categories&action=edit&id=<?= esc_attr($category->MaLoai) ?>" class="button">Sửa</a>
                                <button class="button btn-delete" data-id="<?= esc_attr($category->MaLoai) ?>" onclick="deleteCategory(<?= esc_attr($category->MaLoai) ?>)">Xóa</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    } else {
        echo '<p>Không có danh mục nào.</p>';
    }

    return ob_get_clean();
}

// Thêm danh mục
function acm_handle_create_category() {
    acm_check_admin_access();

    global $wpdb;
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('acm_create_category');

        $ten_loai = isset($_POST['ten_loai']) ? sanitize_text_field($_POST['ten_loai']) : '';

        if (empty($ten_loai)) {
            $errors[] = 'Tên danh mục không được để trống.';
        }

        // Xử lý upload ảnh
        $hinh_path = '';
        if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['hinh']['type'], $allowed_types)) {
                $errors[] = 'Chỉ cho phép tải lên các định dạng ảnh JPEG, PNG hoặc GIF.';
            } else {
                $upload_dir = wp_upload_dir();
                $category_dir = $upload_dir['path'] . '/categories';
                if (!file_exists($category_dir)) {
                    wp_mkdir_p($category_dir);
                }
                $file_name = wp_unique_filename($category_dir, $_FILES['hinh']['name']);
                $target_file = $category_dir . '/' . $file_name;
                if (move_uploaded_file($_FILES['hinh']['tmp_name'], $target_file)) {
                    $hinh_path = $upload_dir['url'] . '/categories/' . $file_name;
                } else {
                    $errors[] = 'Có lỗi khi tải ảnh lên.';
                }
            }
        }

        if (empty($errors)) {
            $result = $wpdb->insert(
                "{$wpdb->prefix}loai",
                [
                    'TenLoai' => $ten_loai,
                    'Hinh' => $hinh_path ?: null
                ],
                ['%s', '%s']
            );

            if ($result !== false) {
                wp_redirect(admin_url('admin.php?page=acm-categories&message=created'));
                exit;
            } else {
                $errors[] = 'Thêm danh mục không thành công: ' . $wpdb->last_error;
            }
        }
    }

    // Hiển thị form thêm danh mục
    ?>
    <div class="wrap">
        <h2>Thêm danh mục mới</h2>
        <?php if (!empty($errors)) : ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= esc_html($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('acm_create_category'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="ten_loai">Tên danh mục</label></th>
                    <td><input type="text" name="ten_loai" id="ten_loai" value="<?= isset($_POST['ten_loai']) ? esc_attr($_POST['ten_loai']) : '' ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="hinh">Hình ảnh</label></th>
                    <td><input type="file" name="hinh" id="hinh" accept="image/*"></td>
                </tr>
            </table>
            <?php submit_button('Thêm danh mục'); ?>
        </form>
    </div>
    <?php
}

// Sửa danh mục
function acm_handle_edit_category() {
    acm_check_admin_access();

    global $wpdb;
    $errors = [];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $category = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}loai WHERE MaLoai = %d", $id));
    if (!$category) {
        wp_die('Danh mục không tồn tại.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('acm_edit_category');

        $ten_loai = isset($_POST['ten_loai']) ? sanitize_text_field($_POST['ten_loai']) : '';

        if (empty($ten_loai)) {
            $errors[] = 'Tên danh mục không được để trống.';
        }

        // Xử lý upload ảnh
        $hinh_path = $category->Hinh;
        if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['hinh']['type'], $allowed_types)) {
                $errors[] = 'Chỉ cho phép tải lên các định dạng ảnh JPEG, PNG hoặc GIF.';
            } else {
                $upload_dir = wp_upload_dir();
                $category_dir = $upload_dir['path'] . '/categories';
                if (!file_exists($category_dir)) {
                    wp_mkdir_p($category_dir);
                }
                $file_name = wp_unique_filename($category_dir, $_FILES['hinh']['name']);
                $target_file = $category_dir . '/' . $file_name;
                if (move_uploaded_file($_FILES['hinh']['tmp_name'], $target_file)) {
                    $hinh_path = $upload_dir['url'] . '/categories/' . $file_name;
                } else {
                    $errors[] = 'Có lỗi khi tải ảnh lên.';
                }
            }
        }

        if (empty($errors)) {
            $result = $wpdb->update(
                "{$wpdb->prefix}loai",
                [
                    'TenLoai' => $ten_loai,
                    'Hinh' => $hinh_path ?: null
                ],
                ['MaLoai' => $id],
                ['%s', '%s'],
                ['%d']
            );

            if ($result !== false) {
                wp_redirect(admin_url('admin.php?page=acm-categories&message=updated'));
                exit;
            } else {
                $errors[] = 'Cập nhật danh mục không thành công: ' . $wpdb->last_error;
            }
        }
    }

    // Hiển thị form sửa danh mục
    ?>
    <div class="wrap">
        <h2>Sửa danh mục</h2>
        <?php if (!empty($errors)) : ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= esc_html($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('acm_edit_category'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="ten_loai">Tên danh mục</label></th>
                    <td><input type="text" name="ten_loai" id="ten_loai" value="<?= esc_attr($category->TenLoai) ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="hinh">Hình ảnh</label></th>
                    <td>
                        <?php if ($category->Hinh) : ?>
                            <img src="<?= esc_url($category->Hinh) ?>" alt="Hình danh mục" style="max-width: 100px;" /><br>
                        <?php endif; ?>
                        <input type="file" name="hinh" id="hinh" accept="image/*">
                    </td>
                </tr>
            </table>
            <?php submit_button('Cập nhật danh mục'); ?>
        </form>
    </div>
    <?php
}

// Xóa danh mục (AJAX)
function acm_handle_delete_category() {
    check_ajax_referer('acm_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Bạn không có quyền xóa danh mục.']);
        return;
    }

    if (isset($_POST['ma_loai'])) {
        global $wpdb;
        $ma_loai = intval($_POST['ma_loai']);

        $category = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}loai WHERE MaLoai = %d", $ma_loai));
        if (!$category) {
            wp_send_json_error(['message' => 'Danh mục không tồn tại.']);
            return;
        }

        $result = $wpdb->delete(
            "{$wpdb->prefix}loai",
            ['MaLoai' => $ma_loai],
            ['%d']
        );

        if ($result !== false) {
            wp_send_json_success(['message' => 'Danh mục đã được xóa thành công!']);
        } else {
            wp_send_json_error(['message' => 'Xóa danh mục không thành công: ' . $wpdb->last_error]);
        }
    } else {
        wp_send_json_error(['message' => 'Không tìm thấy danh mục để xóa.']);
    }

    die();
}

// Thêm menu admin
function acm_add_admin_menu() {
    add_menu_page(
        'Quản lý danh mục',
        'Danh mục',
        'manage_options',
        'acm-categories',
        'acm_admin_page_router',
        'dashicons-category'
    );
}

function acm_admin_page_router() {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action === 'create') {
        acm_handle_create_category();
    } elseif ($action === 'edit') {
        acm_handle_edit_category();
    } else {
        echo acm_display_admin_categories();
    }
}

// Enqueue scripts và styles
function acm_enqueue_assets() {
    if (!is_admin() || !isset($_GET['page']) || $_GET['page'] !== 'acm-categories') {
        return;
    }

    wp_enqueue_script('jquery');
    wp_localize_script('jquery', 'ajax_object', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('acm_nonce')
    ]);

    // Thêm CSS tùy chỉnh
    wp_enqueue_style('acm-styles', plugin_dir_url(__FILE__) . 'css/acm-styles.css');
}

// Tạo bảng cơ sở dữ liệu khi kích hoạt plugin
function acm_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'loai';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        MaLoai bigint(20) NOT NULL AUTO_INCREMENT,
        TenLoai varchar(255) NOT NULL,
        Hinh varchar(255) DEFAULT NULL,
        PRIMARY KEY (MaLoai)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Đăng ký hooks
add_action('admin_menu', 'acm_add_admin_menu');
add_action('admin_enqueue_scripts', 'acm_enqueue_assets');
add_action('wp_ajax_acm_delete_category', 'acm_handle_delete_category');
register_activation_hook(__FILE__, 'acm_create_table');

// CSS tùy chỉnh (lưu trong file css/acm-styles.css)