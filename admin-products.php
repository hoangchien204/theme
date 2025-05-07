<?php
/*
Template Name: Quản lý Sản phẩm
*/

// Kiểm tra quyền admin
if (!current_user_can('manage_options')) {
    wp_die('Bạn không có quyền truy cập trang này.');
}

global $wpdb;
$admin_page_title = 'Quản lý Sản phẩm';

// Xử lý thêm sản phẩm
if (isset($_POST['action']) && $_POST['action'] === 'add_product' && check_admin_referer('add_product_nonce')) {
    $tenhh = sanitize_text_field($_POST['tenhh']);
    $maloai = intval($_POST['maloai']);
    $motadonvi = sanitize_text_field($_POST['motadonvi'] ?? '');
    $dongia = floatval($_POST['dongia']);
    $mota = sanitize_textarea_field($_POST['mota'] ?? '');
    $soluongtonkho = intval($_POST['soluongtonkho'] ?? 0);

    // Kiểm tra dữ liệu
    $errors = [];
    if (empty($tenhh)) $errors[] = 'Tên sản phẩm không được để trống.';
    if ($maloai <= 0) $errors[] = 'Vui lòng chọn loại sản phẩm hợp lệ.';
    if ($dongia < 0) $errors[] = 'Đơn giá không được âm.';
    if ($soluongtonkho < 0) $errors[] = 'Số lượng tồn kho không được âm.';

    // Xử lý ảnh
    $hinh_path = '';
    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] == 0) {
        $upload_dir = get_template_directory() . '/img/fruits/';
        $target_file = $upload_dir . basename($_FILES['hinh']['name']);

        // Tạo thư mục nếu chưa có
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Di chuyển file vào thư mục
        if (move_uploaded_file($_FILES['hinh']['tmp_name'], $target_file)) {
            $hinh_path = 'fruits/' . basename($_FILES['hinh']['name']);
        } else {
            wp_redirect(add_query_arg(['status' => 'error', 'message' => 'Có lỗi khi tải ảnh lên.'], wp_get_referer()));
            exit;
        }
    } else {
        $errors[] = 'Vui lòng chọn một ảnh cho sản phẩm.';
    }

    if (empty($errors)) {
        $result = $wpdb->insert(
            'wp_hanghoa',
            [
                'TenHH' => $tenhh,
                'MaLoai' => $maloai,
                'MoTaDonVi' => $motadonvi ?: null,
                'DonGia' => $dongia,
                'Hinh' => $hinh_path ?: null,
                'NgaySX' => current_time('mysql'),
                'GiamGia' => 0.00,
                'SoLanXem' => 0,
                'MoTa' => $mota ?: null,
                'SoLanMua' => 0,
                'SoLuongTonKho' => $soluongtonkho
            ],
            ['%s', '%d', '%s', '%f', '%s', '%s', '%f', '%d', '%s', '%d', '%d']
        );

        if ($result !== false) {
            wp_redirect(add_query_arg(['status' => 'success', 'message' => 'Sản phẩm đã được thêm thành công!'], wp_get_referer()));
            exit;
        } else {
            $errors[] = 'Thêm sản phẩm không thành công: ' . $wpdb->last_error;
        }
    }

    if (!empty($errors)) {
        wp_redirect(add_query_arg(['status' => 'error', 'message' => implode(' ', $errors)], wp_get_referer()));
        exit;
    }
}

// Xử lý sửa sản phẩm
if (isset($_POST['action']) && $_POST['action'] === 'edit_product' && check_admin_referer('edit_product_nonce')) {
    $mahh = intval($_POST['mahh']);
    $tenhh = sanitize_text_field($_POST['tenhh']);
    $maloai = intval($_POST['maloai']);
    $motadonvi = sanitize_text_field($_POST['motadonvi'] ?? '');
    $dongia = floatval($_POST['dongia']);
    $mota = sanitize_textarea_field($_POST['mota'] ?? '');
    $soluongtonkho = intval($_POST['soluongtonkho'] ?? 0);

    // Kiểm tra dữ liệu
    $errors = [];
    if (empty($tenhh)) $errors[] = 'Tên sản phẩm không được để trống.';
    if ($maloai <= 0) $errors[] = 'Vui lòng chọn loại sản phẩm hợp lệ.';
    if ($dongia < 0) $errors[] = 'Đơn giá không được âm.';
    if ($soluongtonkho < 0) $errors[] = 'Số lượng tồn kho không được âm.';

    // Xử lý ảnh
    $hinh_path = $_POST['current_hinh'];
    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] == 0) {
        $upload_dir = get_template_directory() . '/img/fruits/';
        $target_file = $upload_dir . basename($_FILES['hinh']['name']);
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        if (move_uploaded_file($_FILES['hinh']['tmp_name'], $target_file)) {
            $hinh_path = 'fruits/' . basename($_FILES['hinh']['name']);
            // Xóa ảnh cũ nếu có
            if ($hinh_path !== $_POST['current_hinh'] && !empty($_POST['current_hinh'])) {
                $old_file = get_template_directory() . '/img/' . $_POST['current_hinh'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
        } else {
            $errors[] = 'Có lỗi khi tải ảnh lên.';
        }
    }

    if (empty($errors)) {
        $result = $wpdb->update(
            'wp_hanghoa',
            [
                'TenHH' => $tenhh,
                'MaLoai' => $maloai,
                'MoTaDonVi' => $motadonvi ?: null,
                'DonGia' => $dongia,
                'Hinh' => $hinh_path ?: null,
                'MoTa' => $mota ?: null,
                'SoLuongTonKho' => $soluongtonkho
            ],
            ['MaHH' => $mahh],
            ['%s', '%d', '%s', '%f', '%s', '%s', '%d'],
            ['%d']
        );

        if ($result !== false) {
            wp_redirect(add_query_arg(['status' => 'success', 'message' => 'Sản phẩm đã được cập nhật thành công!'], remove_query_arg('edit_mahh', wp_get_referer())));
            exit;
        } else {
            $errors[] = 'Cập nhật sản phẩm không thành công: ' . $wpdb->last_error;
        }
    }

    if (!empty($errors)) {
        wp_redirect(add_query_arg(['status' => 'error', 'message' => implode(' ', $errors)], wp_get_referer()));
        exit;
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['action']) && $_GET['action'] === 'delete_product' && isset($_GET['mahh']) && check_admin_referer('delete_product_nonce')) {
    $mahh = intval($_GET['mahh']);
    $result = $wpdb->delete('wp_hanghoa', ['MaHH' => $mahh], ['%d']);

    if ($result !== false) {
        wp_redirect(add_query_arg(['status' => 'success', 'message' => 'Sản phẩm đã được xóa thành công!'], wp_get_referer()));
        exit;
    } else {
        wp_redirect(add_query_arg(['status' => 'error', 'message' => 'Xóa sản phẩm không thành công: ' . $wpdb->last_error], wp_get_referer()));
        exit;
    }
}

$product = null;
if (isset($_GET['edit_mahh'])) {
    $mahh = intval($_GET['edit_mahh']);
    $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_hanghoa WHERE MaHH = %d", $mahh));
}
?>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <?php get_template_part('admin-sidebar'); ?>

    <!-- Nội dung chính -->
    <div class="admin-content">
        <main class="admin-main">
            <!-- Thông báo -->
            <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
                <div class="notification <?php echo esc_attr($_GET['status']); ?>">
                    <?php echo esc_html($_GET['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Nút thêm sản phẩm -->
            <div class="action-buttons">
                <button onclick="document.getElementById('add-product-form').style.display='flex'" class="btn">+ Thêm sản phẩm</button>
                <div class="sorting-dropdown">
                    <form method="get">
                        <select name="sort-by-category" class="dropdown-select" onchange="this.form.submit()">
                            <option value="">-- Sắp xếp theo loại --</option>
                            <?php
                            $categories = $wpdb->get_results("SELECT * FROM wp_loai");
                            foreach ($categories as $category) {
                                $selected = (isset($_GET['sort-by-category']) && $_GET['sort-by-category'] == $category->MaLoai) ? 'selected' : '';
                                echo '<option value="' . esc_attr($category->MaLoai) . '" ' . $selected . '>' . esc_html($category->TenLoai) . '</option>';
                            }
                            ?>
                        </select>
                        <select name="sort-by-name" class="dropdown-select" onchange="this.form.submit()">
                            <option value="">-- Sắp xếp theo tên --</option>
                            <option value="asc" <?php echo isset($_GET['sort-by-name']) && $_GET['sort-by-name'] == 'asc' ? 'selected' : ''; ?>>A-Z</option>
                            <option value="desc" <?php echo isset($_GET['sort-by-name']) && $_GET['sort-by-name'] == 'desc' ? 'selected' : ''; ?>>Z-A</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="product-list">
                <?php
                $query = "SELECT * FROM wp_hanghoa WHERE 1=1";
                if (isset($_GET['sort-by-category']) && !empty($_GET['sort-by-category'])) {
                    $query .= " AND MaLoai = " . esc_sql($_GET['sort-by-category']);
                }
                if (isset($_GET['sort-by-name']) && !empty($_GET['sort-by-name'])) {
                    $order = ($_GET['sort-by-name'] == 'asc') ? 'ASC' : 'DESC';
                    $query .= " ORDER BY TenHH " . $order;
                }
                $products = $wpdb->get_results($query);

                if ($products) {
                    foreach ($products as $product_item) {
                        $image_path = !empty($product_item->Hinh) ? esc_url(get_stylesheet_directory_uri() . '/img/' . $product_item->Hinh) : esc_url(get_stylesheet_directory_uri() . '/img/placeholder.jpg');
                        ?>
                        <div class="product-item">
                            <img src="<?php echo $image_path; ?>" alt="Ảnh sản phẩm" />
                            <h3><?php echo esc_html($product_item->TenHH); ?></h3>
                            <p>Giá: <?php echo number_format($product_item->DonGia, 0, ',', '.'); ?>đ</p>
                            <div class="product-actions">
                                <a href="<?php echo add_query_arg(['edit_mahh' => $product_item->MaHH]); ?>" class="btn-edit">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Sửa
                                </a>
                                <a href="<?php echo wp_nonce_url(add_query_arg(['action' => 'delete_product', 'mahh' => $product_item->MaHH]), 'delete_product_nonce'); ?>" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m-8 0v14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6m-4 0v14" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Xóa
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "Không có sản phẩm nào.";
                }
                ?>
            </div>

            <!-- Form thêm sản phẩm -->
            <div id="add-product-form" class="form-popup-overlay">
                <div class="form-popup">
                    <button onclick="document.getElementById('add-product-form').style.display='none'" class="btn-close">✖</button>
                    <h2>Thêm sản phẩm mới</h2>
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('add_product_nonce'); ?>
                        <input type="hidden" name="action" value="add_product">
                        <p><input type="text" name="tenhh" placeholder="Tên sản phẩm" required /></p>
                        <p>
                            <select name="maloai" required>
                                <option value="">Chọn mã loại sản phẩm</option>
                                <?php
                                foreach ($categories as $category) {
                                    echo '<option value="' . esc_attr($category->MaLoai) . '">' . esc_html($category->TenLoai) . '</option>';
                                }
                                ?>
                            </select>
                        </p>
                        <p><input type="text" name="motadonvi" placeholder="Mô tả đơn vị" /></p>
                        <p><input type="number" step="0.01" name="dongia" placeholder="Đơn giá" required min="0" /></p>
                        <p><input type="file" name="hinh" required /></p>
                        <p><textarea name="mota" placeholder="Mô tả chi tiết sản phẩm"></textarea></p>
                        <p><input type="number" name="soluongtonkho" placeholder="Số lượng tồn kho" value="0" min="0" /></p>
                        <button type="submit" class="btn">Lưu</button>
                    </form>
                </div>
            </div>

            <!-- Form sửa sản phẩm -->
            <div id="edit-product-form" class="form-popup-overlay" <?php echo isset($_GET['edit_mahh']) ? 'style="display: flex;"' : ''; ?>>
                <div class="form-popup">
                    <button onclick="window.location.href='<?php echo remove_query_arg('edit_mahh'); ?>'" class="btn-close">✖</button>
                    <h2>Sửa sản phẩm</h2>
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('edit_product_nonce'); ?>
                        <input type="hidden" name="action" value="edit_product">
                        <input type="hidden" name="mahh" id="edit-mahh" value="<?php echo isset($product) ? esc_attr($product->MaHH) : ''; ?>">
                        <p><input type="text" name="tenhh" id="edit-tenhh" placeholder="Tên sản phẩm" required value="<?php echo isset($product) ? esc_attr($product->TenHH) : ''; ?>" /></p>
                        <p>
                            <select name="maloai" id="edit-maloai" required>
                                <option value="">Chọn mã loại sản phẩm</option>
                                <?php
                                foreach ($categories as $category) {
                                    $selected = (isset($product) && $product->MaLoai == $category->MaLoai) ? 'selected' : '';
                                    echo '<option value="' . esc_attr($category->MaLoai) . '" ' . $selected . '>' . esc_html($category->TenLoai) . '</option>';
                                }
                                ?>
                            </select>
                        </p>
                        <p><input type="text" name="motadonvi" id="edit-motadonvi" placeholder="Mô tả đơn vị" value="<?php echo isset($product) ? esc_attr($product->MoTaDonVi) : ''; ?>" /></p>
                        <p><input type="number" step="0.01" name="dongia" id="edit-dongia" placeholder="Đơn giá" required min="0" value="<?php echo isset($product) ? esc_attr($product->DonGia) : ''; ?>" /></p>
                        <p><input type="file" name="hinh" id="edit-hinh" /> <span id="current-hinh"><?php echo isset($product) ? 'Ảnh hiện tại: ' . esc_html($product->Hinh) : ''; ?></span></p>
                        <input type="hidden" name="current_hinh" id="current-hinh-value" value="<?php echo isset($product) ? esc_attr($product->Hinh) : ''; ?>">
                        <p><textarea name="mota" id="edit-mota" placeholder="Mô tả chi tiết sản phẩm"><?php echo isset($product) ? esc_textarea($product->MoTa) : ''; ?></textarea></p>
                        <p><input type="number" name="soluongtonkho" id="edit-soluongtonkho" placeholder="Số lượng tồn kho" value="<?php echo isset($product) ? esc_attr($product->SoLuongTonKho) : ''; ?>" min="0" /></p>
                        <button type="submit" class="btn">Cập nhật</button>
                    </form>
                </div>
            </div>
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
    overflow-x: hidden;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    color: #333;
}

/* Layout */
.admin-wrapper {
    display: flex;
    min-height: 100vh;
    width: 100vw;
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

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.btn {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    font-size: 1em;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

/* Product Actions */
.product-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 10px;
}

.btn-edit, .btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.95em;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.btn-edit {
    background-color: #f39c12;
    color: white;
}

.btn-edit:hover {
    background-color: #e67e22;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.btn-delete {
    background-color: #e74c3c;
    color: white;
}

.btn-delete:hover {
    background-color: #c0392b;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.btn-icon {
    width: 16px;
    height: 16px;
    stroke-width: 2;
}

/* Product List */
.product-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.product-item {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
}

.product-item img {
    max-width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 5px;
    margin-bottom: 15px;
}

.product-item h3 {
    font-size: 1.1em;
    margin-bottom: 10px;
    color: #2c3e50;
    font-weight: 500;
}

.product-item p {
    font-size: 0.95em;
    color: #7f8c8d;
    margin-bottom: 15px;
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
    transition: border-color 0.3s ease;
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
    font-size: 1em;
    transition: background-color 0.3s ease;
    align-items: center;
    justify-content: center;
    display: flex;
;
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
    transition: background-color 0.3s ease;
}

.btn-close:hover {
    background: #c0392b;
}

/* Sorting Dropdown */
.sorting-dropdown {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.dropdown-select {
    padding: 8px 12px;
    font-size: 16px;
    border-radius: 8px;
    border: 1px solid #ccc;
    appearance: none;
    background: white url("data:image/svg+xml;utf8,<svg fill='gray' height='16' viewBox='0 0 24 24' width='16' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>") no-repeat right 10px center;
    background-size: 16px;
    cursor: pointer;
    min-width: 200px;
}

select[name="maloai"] {
    padding: 10px 15px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    color: #333;
    width: 100%;
    transition: border-color 0.3s ease, background-color 0.3s ease;
    cursor: pointer;
    appearance: none;
}

select[name="maloai"]:focus {
    border-color: #3498db;
    background-color: #f0f8ff;
    outline: none;
}

select[name="maloai"] {
    background: url('data:image/svg+xml;utf8,<svg fill="gray" height="16" width="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 15px center;
    background-size: 16px;
    padding-right: 30px;
    margin-bottom: 15px;
}

select[name="maloai"] option {
    padding: 10px;
    background-color: #fff;
    color: #333;
    border: 1px solid #ddd;
}

select[name="maloai"] option:hover {
    background-color: #f1f1f1;
}

@media (max-width: 768px) {
    .admin-content {
        padding: 10px;
    }

    .product-list {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }

    .product-item {
        padding: 15px;
    }

    .btn-edit, .btn-delete {
        padding: 6px 12px;
        font-size: 0.9em;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.querySelector('.notification');
    if (notification) {
        let timeoutId;
        const hideNotification = () => {
            notification.classList.add('hidden');
            setTimeout(() => {
                notification.style.display = 'none';
            }, 500); // Đợi hiệu ứng opacity hoàn tất
        };
        // Xóa timeout cũ nếu có
        clearTimeout(timeoutId);
        // Ẩn thông báo sau 5 giây
        timeoutId = setTimeout(hideNotification, 5000);
    }
});
</script>