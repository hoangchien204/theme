<?php
/*
Template Name: Quản lý Loại Sản phẩm
*/

// Kiểm tra quyền admin
if (!current_user_can('manage_options')) {
    wp_die('Bạn không có quyền truy cập trang này.');
}

global $wpdb;
$admin_page_title = 'Quản lý Loại Sản phẩm';

// Xử lý thêm loại sản phẩm
$errors = [];
if (isset($_POST['action']) && $_POST['action'] === 'add_catalog' && check_admin_referer('add_catalog_nonce')) {
    $tenloai = sanitize_text_field($_POST['tenloai']);
    $mota = sanitize_textarea_field($_POST['mota'] ?? '');

    if (empty($tenloai)) {
        $errors[] = 'Tên loại sản phẩm không được để trống.';
    }

    // Xử lý ảnh
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
                'TenLoai' => $tenloai,
                'Hinh' => $hinh_path ?: null,
                'MoTa' => $mota ?: null
            ],
            ['%s', '%s', '%s']
        );

        if ($result !== false) {
            wp_redirect(add_query_arg(['status' => 'success', 'message' => 'Loại sản phẩm đã được thêm thành công!'], wp_get_referer()));
            exit;
        } else {
            $errors[] = 'Thêm loại sản phẩm không thành công: ' . $wpdb->last_error;
        }
    }
}

// Xử lý sửa loại sản phẩm
if (isset($_POST['action']) && $_POST['action'] === 'edit_catalog' && check_admin_referer('edit_catalog_nonce')) {
    $maloai = intval($_POST['maloai']);
    $tenloai = sanitize_text_field($_POST['tenloai']);
    $mota = sanitize_textarea_field($_POST['mota'] ?? '');

    if (empty($tenloai)) {
        $errors[] = 'Tên loại sản phẩm không được để trống.';
    }

    // Xử lý ảnh
    $hinh_path = $_POST['current_hinh'] ?? '';
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
                // Xóa ảnh cũ nếu có
                if (!empty($_POST['current_hinh'])) {
                    $old_file = str_replace($upload_dir['url'], $upload_dir['path'], $_POST['current_hinh']);
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
            } else {
                $errors[] = 'Có lỗi khi tải ảnh lên.';
            }
        }
    }

    if (empty($errors)) {
        $result = $wpdb->update(
            "{$wpdb->prefix}loai",
            [
                'TenLoai' => $tenloai,
                'Hinh' => $hinh_path ?: null,
                'MoTa' => $mota ?: null,
            ],
            ['MaLoai' => $maloai],
            ['%s', '%s', '%s'],
            ['%d']
        );

        if ($result !== false) {
            wp_redirect(add_query_arg(['status' => 'success', 'message' => 'Loại sản phẩm đã được cập nhật thành công!'], remove_query_arg('edit_maloai', wp_get_referer())));
            exit;
        } else {
            $errors[] = 'Cập nhật loại sản phẩm không thành công: ' . $wpdb->last_error;
        }
    }
}

// Xử lý xóa loại sản phẩm
if (isset($_GET['action']) && $_GET['action'] === 'delete_catalog' && isset($_GET['maloai']) && check_admin_referer('delete_catalog_nonce')) {
    $maloai = intval($_GET['maloai']);
    // Kiểm tra xem danh mục có sản phẩm liên quan không
    $related_products = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}hanghoa WHERE MaLoai = %d", $maloai));
    if ($related_products > 0) {
        wp_redirect(add_query_arg(['status' => 'error', 'message' => 'Không thể xóa danh mục vì có sản phẩm liên quan.'], wp_get_referer()));
        exit;
    }

    $category = $wpdb->get_row($wpdb->prepare("SELECT Hinh FROM {$wpdb->prefix}loai WHERE MaLoai = %d", $maloai));
    $result = $wpdb->delete("{$wpdb->prefix}loai", ['MaLoai' => $maloai], ['%d']);

    if ($result !== false) {
        // Xóa ảnh nếu có
        if ($category->Hinh) {
            $upload_dir = wp_upload_dir();
            $file_path = str_replace($upload_dir['url'], $upload_dir['path'], $category->Hinh);
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        wp_redirect(add_query_arg(['status' => 'success', 'message' => 'Loại sản phẩm đã được xóa thành công!'], wp_get_referer()));
        exit;
    } else {
        wp_redirect(add_query_arg(['status' => 'error', 'message' => 'Xóa loại sản phẩm không thành công: ' . $wpdb->last_error], wp_get_referer()));
        exit;
    }
}

$catalog = null;
if (isset($_GET['edit_maloai'])) {
    $maloai = intval($_GET['edit_maloai']);
    $catalog = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}loai WHERE MaLoai = %d", $maloai));
    if (!$catalog) {
        wp_redirect(add_query_arg(['status' => 'error', 'message' => 'Danh mục không tồn tại.'], wp_get_referer()));
        exit;
    }
}

// Lấy danh sách danh mục
$categories = $wpdb->get_results("SELECT MaLoai, TenLoai FROM {$wpdb->prefix}loai");

?>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <?php get_template_part('admin-sidebar'); ?>

    <!-- Nội dung chính -->
    <div class="admin-content">
        <main class="admin-main">
            <div class="container-fluid px-4">

                <ol class="breadcrumb mb-4">

                </ol>

                <!-- Thông báo -->
                <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
                    <div class="notification <?php echo esc_attr($_GET['status']); ?>">
                        <?php echo esc_html($_GET['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-tags me-1"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="categoryTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20%; text-">Mã Loại</th>
                                    <th style="width: 20%">Tên Loại</th>
                                    <th style="width: 20%;">Hình Ảnh</th>
                                    <th style="width: 15%;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM {$wpdb->prefix}loai WHERE 1=1";
                                if (isset($_GET['sort-by-name']) && !empty($_GET['sort-by-name'])) {
                                    $order = ($_GET['sort-by-name'] == 'asc') ? 'ASC' : 'DESC';
                                    $query .= " ORDER BY TenLoai $order";
                                }
                                $catalogs = $wpdb->get_results($query);

                                if ($catalogs) :
                                    foreach ($catalogs as $catalog_item) :
                                        $image_path = !empty($catalog_item->Hinh) ? esc_url($catalog_item->Hinh) : esc_url(get_template_directory_uri() . '/img/placeholder.jpg');
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo esc_html($catalog_item->MaLoai); ?></td>
                                            <td><?php echo esc_html($catalog_item->TenLoai); ?></td>
                                            <td class="text-center">
                                                <img src="<?php echo $image_path; ?>" class="category-img" alt="<?php echo esc_attr($catalog_item->TenLoai); ?>" />
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo esc_url(add_query_arg(['edit_maloai' => $catalog_item->MaLoai])); ?>" class="btn btn-warning btn-sm" title="Sửa">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </a>
                                                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'delete_catalog', 'maloai' => $catalog_item->MaLoai]), 'delete_catalog_nonce')); ?>" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </a>
                                                    <button onclick="document.getElementById('add-catalog-form').style.display='flex'" class="btn btn-primary">
                                                      <i class="fas fa-plus me-1"></i>Thêm
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    endforeach;
                                else :
                                    ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Không có danh mục nào.</td>
                                    </tr>
                                    <?php
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form thêm loại sản phẩm -->
                <div id="add-catalog-form" class="form-popup-overlay">
                    <div class="form-popup">
                        <button onclick="document.getElementById('add-catalog-form').style.display='none'" class="btn-close">✖</button>
                        <h2>Thêm loại sản phẩm mới</h2>
                        <?php if (!empty($errors)) : ?>
                            <div class="notification error">
                                <?php echo implode('<br>', array_map('esc_html', $errors)); ?>
                            </div>
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data">
                            <?php wp_nonce_field('add_catalog_nonce'); ?>
                            <input type="hidden" name="action" value="add_catalog">
                            <p><input type="text" name="tenloai" placeholder="Tên loại sản phẩm" value="<?php echo isset($_POST['tenloai']) ? esc_attr($_POST['tenloai']) : ''; ?>" required /></p>
                            <p><input type="file" name="hinh" /></p>
                            <p><textarea name="mota" placeholder="Mô tả loại sản phẩm"><?php echo isset($_POST['mota']) ? esc_textarea($_POST['mota']) : ''; ?></textarea></p>
                            <button type="submit" class="btn">Lưu</button>
                        </form>
                    </div>
                </div>

                <!-- Form sửa loại sản phẩm -->
                <div id="edit-catalog-form" class="form-popup-overlay" <?php echo isset($_GET['edit_maloai']) ? 'style="display: flex;"' : ''; ?>>
                    <div class="form-popup">
                        <button onclick="window.location.href='./'" class="btn-close">✖</button>
                        <h2>Sửa loại sản phẩm</h2>
                        <?php if (!empty($errors)) : ?>
                            <div class="notification error">
                                <?php echo implode('<br>', array_map('esc_html', $errors)); ?>
                            </div>
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data">
                            <?php wp_nonce_field('edit_catalog_nonce'); ?>
                            <input type="hidden" name="action" value="edit_catalog">
                            <input type="hidden" name="maloai" value="<?php echo isset($catalog) ? esc_attr($catalog->MaLoai) : ''; ?>">
                            <p><input type="text" name="tenloai" placeholder="Tên loại sản phẩm" required value="<?php echo isset($catalog) ? esc_attr($catalog->TenLoai) : ''; ?>" /></p>
                            <p>
                                <input type="file" name="hinh" />
                                <?php if (isset($catalog) && $catalog->Hinh) : ?>
                                    <span>Ảnh hiện tại: <img src="<?php echo esc_url($catalog->Hinh); ?>" class="category-img" alt="Current Image" /></span>
                                <?php endif; ?>
                            </p>
                            <input type="hidden" name="current_hinh" value="<?php echo isset($catalog) ? esc_attr($catalog->Hinh) : ''; ?>">
                            <p><textarea name="mota" placeholder="Mô tả loại sản phẩm"><?php echo isset($catalog) ? esc_textarea($catalog->MoTa) : ''; ?></textarea></p>
                            <button type="submit" class="btn">Cập nhật</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
// Enqueue scripts và styles
wp_enqueue_script('jquery-datatables', 'https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js', ['jquery'], '1.13.1', true);
wp_enqueue_script('datatables-bootstrap', 'https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js', ['jquery-datatables'], '1.13.1', true);
wp_enqueue_style('datatables-bootstrap-css', 'https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css');
wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

// Script khởi tạo DataTables
wp_add_inline_script('jquery-datatables', '
    jQuery(document).ready(function($) {
        $("#categoryTable").DataTable({
            "pageLength": 10,
            "language": {
                "lengthMenu": "Hiển thị _MENU_ mục mỗi trang",
                "zeroRecords": "Không tìm thấy loại sản phẩm nào",
                "info": "Hiển thị trang _PAGE_ của _PAGES_",
                "infoEmpty": "Không có loại sản phẩm nào",
                "infoFiltered": "(lọc từ _MAX_ loại sản phẩm)",
                "search": "Tìm kiếm:",
                "paginate": {
                    "first": "Đầu",
                    "last": "Cuối",
                    "next": "Sau",
                    "previous": "Trước"
                }
            }
        });

        // Ẩn thông báo sau 5 giây
        const notification = $(".notification");
        if (notification.length) {
            setTimeout(() => {
                notification.addClass("hidden");
                setTimeout(() => notification.hide(), 500);
            }, 5000);
        }
    });
');
?>

<style>
/* Reset và thiết lập toàn màn hình */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    width: 100%;
    height: 100vh;
    overflow-x: hidden;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    color: #333;
}

/* Layout */
.admin-wrapper {
    display: flex;
    min-height: 100vh;
}

.admin-content {
    flex-grow: 1;
    padding: 20px;
}

/* Notification */
.notification {
    padding: 12px 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    text-align: center;
    font-size: 16px;
    font-weight: 500;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3000;
    min-width: 300px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    opacity: 1;
    transition: opacity 0.5s ease;
}

.notification.success {
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
}

.notification.error {
    color: #721c24;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
}

.notification.hidden {
    opacity: 0;
    pointer-events: none;
}

/* Card */
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e3e6ec;
}

/* Breadcrumb */
.breadcrumb {
    background-color: #f8f9fa;
    padding: 0.75rem 1rem;
    border-radius: 0.25rem;
}

/* Table */
.table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: collapse;
}

.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    text-align: center; /* Căn giữa tiêu đề cột */
    vertical-align: middle; /* Căn giữa theo chiều dọc */
    padding: 1rem;
    border: 1px solid #e3e6ec;
}

.table tbody td {
    text-align: center; /* Căn giữa nội dung */
    vertical-align: middle; /* Căn giữa theo chiều dọc */
    padding: 1rem;
    border: 1px solid #e3e6ec;
}

/* Định nghĩa độ rộng cố định cho các cột để căn cách đều */
.table th:nth-child(1),
.table td:nth-child(1) {
    width: 15%; /* Mã Loại */
}

.table th:nth-child(2),
.table td:nth-child(2) {
    width: 30%; /* Tên Loại */
}

.table th:nth-child(3),
.table td:nth-child(3) {
    width: 30%; /* Hình Ảnh */
}

.table th:nth-child(4),
.table td:nth-child(4) {
    width: 25%; /* Thao tác */
}

.category-img {
    width: 120px;
    height: 160px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
    display: block;
    margin: 0 auto; /* Căn giữa hình ảnh trong cột */
}

.category-img:hover {
    transform: scale(1.05);
}

/* Buttons */
.btn {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    font-size: 1em;
}

.btn:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

.btn-warning.btn-sm {
    text-decoration: none;
}

.btn-danger.btn-sm {
    margin-left: 5px;
    text-decoration: none;
}

.btn-primary {
    margin-left: 5px;
    text-decoration: none;
}

.btn-group > .btn {
    padding: 0.25rem 0.5rem;
}

.btn-group > .btn:hover {
    transform: translateY(-1px);
}

/* Form Popup */
.form-popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 2000;
    justify-content: center;
    align-items: center;
}

.form-popup {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 550px;
    position: relative;
}

.form-popup input,
.form-popup textarea {
    width: 100%;
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1em;
}

.form-popup input:focus,
.form-popup textarea:focus {
    border-color: #3498db;
    outline: none;
}

.form-popup textarea {
    height: 120px;
    resize: vertical;
}

.form-popup button {
    background-color: #2ecc71;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 5px;
    cursor: pointer;
}

.form-popup button:hover {
    background-color: #27ae60;
}

.btn-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 18px;
    width: 30px;
    height: 30px;
    cursor: pointer;
}

.btn-close:hover {
    background: #c0392b;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-content {
        padding: 10px;
    }

    /* Điều chỉnh độ rộng cột trên màn hình nhỏ */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 20%;
    }

    .table th:nth-child(2),
    .table td:nth-child(2) {
        width: 25%;
    }

    .table th:nth-child(3),
    .table td:nth-child(3) {
        width: 25%;
    }

    .table th:nth-child(4),
    .table td:nth-child(4) {
        width: 30%;
    }

    .category-img {
        width: 80px;
        height: 100px;
    }
}
</style>