<?php
/*
Template Name: Shop Page
Description: Trang hiển thị danh sách sản phẩm từ plugin.
*/
?>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Calling wp_head -->
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <!-- WP Header -->
    <?php get_header(); ?>
    <?php get_template_part("Templates/common-banner"); ?>
    <div class="shop-page">
        <div class="container-product">
            <div class="shop-sidebar">
                <div class="shop-categories">
                    <h2>Danh mục sản phẩm</h2>
                    <?php
                    $categories = function_exists('myshop_get_categories') ? myshop_get_categories() : [];

                    if ($categories):
                        echo '<ul>';
                        foreach ($categories as $category):
                            echo '<li><a href="' . esc_url(add_query_arg('maloai', $category->MaLoai, get_permalink())) . '">' . esc_html($category->TenLoai) . '</a></li>';
                        endforeach;
                        echo '</ul>';
                    else:
                        echo '<p>Không có danh mục nào.</p>';
                    endif;
                    ?>
                </div>
            </div>

            <div class="shop-products">
                <h1>Sản phẩm của chúng tôi</h1>
                <div class="product-grid">
                    <?php
                    // Lấy tham số lọc loại sản phẩm
                    $maloai = isset($_GET['maloai']) ? intval($_GET['maloai']) : 0;

                    // Phân trang
                    $products_per_page = 3;
                    $current_page = isset($_GET['my_paged']) ? max(1, intval($_GET['my_paged'])) : 1;
                    $offset = ($current_page - 1) * $products_per_page;

                    // Đếm tổng số sản phẩm
                    function myshop_count_products($maloai = 0)
                    {
                        global $wpdb;
                        $where = "WHERE TrangThai = 1";
                        if ($maloai > 0) {
                            $where .= " AND MaLoai = " . intval($maloai);
                        }
                        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}hanghoa $where");
                    }

                    $total_products = myshop_count_products($maloai);
                    $total_pages = ceil($total_products / $products_per_page);

                    // Lấy sản phẩm cho trang hiện tại
                    $products = function_exists('myshop_get_products') ? myshop_get_products($maloai, $products_per_page, $offset) : [];

                    if ($products):
                        foreach ($products as $product):
                            $image_path = $product->Hinh ? get_template_directory_uri() . '/img/' . $product->Hinh : '';
                            $is_out_of_stock = $product->SoLuongTonKho <= 0;
                    ?>
                            <div class="product-item <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?>">
                                <?php if (!$is_out_of_stock): ?>
                                    <a href="<?php echo esc_url(home_url('/chi-tiet-san-pham/?id=' . $product->MaHH)); ?>">
                                    <?php endif; ?>

                                    <?php if ($image_path): ?>
                                        <div class="product-thumbnail">
                                            <img src="<?php echo esc_url($image_path); ?>" alt="<?php echo esc_attr($product->TenHH); ?>">
                                            <?php if ($is_out_of_stock): ?>
                                                <div class="sold-out-overlay">Đã hết hàng</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="product-info">
                                        <h3 class="product-title"><?php echo esc_html($product->TenHH); ?></h3>
                                        <p class="product-price"><?php echo number_format($product->DonGia, 0, ',', '.') . ' VND'; ?></p>
                                        <p class="product-sales">Đã bán: <?php echo esc_html($product->SoLanMua ?: 0); ?></p>
                                    </div>

                                    <?php if (!$is_out_of_stock): ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                </div>

                <!-- Phân trang -->
                <div class="pagination">
                    <?php if ($total_pages > 1): ?>
                        <ul class="pagination-list">
                            <?php
                            $base_url = remove_query_arg(['my_paged']); // xóa param cũ
                            if ($maloai > 0) {
                                $base_url = add_query_arg('maloai', $maloai, $base_url);
                            }

                            for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="pagination-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a href="<?php echo esc_url(add_query_arg('my_paged', $i, $base_url)); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p>Không có sản phẩm nào.</p>
            <?php endif; ?>
            </div>

        </div>
    </div>
    </div>
    <!-- WP Footer -->
    <?php get_footer(); ?>

    <!-- Calling wp_footer -->
    <?php wp_footer(); ?>
</body>

</html>

<style>
    /* General Layout for the Shop Page */
    .shop-page {
        display: flex;
        flex-wrap: wrap;
        margin: 0 auto;
        padding: 20px;
    }

    .container-product {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .shop-sidebar {
        width: 25%;
        padding: 20px;
        border-right: 1px solid #ddd;
    }

    .shop-categories {
        margin-bottom: 30px;
    }

    .shop-categories h2 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .shop-categories ul {
        list-style-type: none;
        padding-left: 0;
    }

    .shop-categories li {
        margin-bottom: 10px;
    }

    .shop-search {
        margin-bottom: 30px;
    }

    .shop-search h2 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .shop-search form {
        display: flex;
        flex-direction: column;
    }

    .shop-search input[type="search"] {
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .shop-search input[type="submit"] {
        padding: 10px;
        background-color: #0073e6;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .shop-search input[type="submit"]:hover {
        background-color: #005bb5;
    }

    /* Product Grid */
    .shop-products {
        width: 70%;
        padding: 20px;
    }

    .shop-products h1 {
        font-size: 32px;
        margin-bottom: 30px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .product-item {
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
        transition: transform 0.3s ease-in-out;
    }

    .product-item:hover {
        transform: scale(1.05);
    }

    .product-thumbnail {
        width: 100%;
        aspect-ratio: 1 / 1;
        /* vuông đều nhau */
        overflow: hidden;
        position: relative;
    }

    .product-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }


    .product-info {
        padding: 15px;
        text-align: center;
    }

    .product-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .product-price {
        font-size: 16px;
        color: #0073e6;
        font-weight: bold;
    }

    .product-sales {
        font-size: 14px;
        color: #555;
    }

    .common-banner {
        width: 100%;
        height: 200px;
    }

    .product-item.out-of-stock {
        position: relative;
        opacity: 0.5;
        pointer-events: none;
    }

    .product-thumbnail {
        position: relative;
    }

    .sold-out-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(255, 0, 0, 0.8);
        color: white;
        padding: 8px 16px;
        font-weight: bold;
        border-radius: 5px;
        z-index: 10;
    }

    .pagination {
        width: 100%;
        /* NEW: Đảm bảo chiếm toàn chiều ngang */
        display: flex;
        /* NEW: Sử dụng flex để căn giữa */
        justify-content: center;
        /* NEW: Căn giữa theo chiều ngang */
        align-items: center;
        margin-top: 30px;
    }

    .pagination-list {
        display: flex;
        /* Đã inline-flex → flex là đủ */
        list-style: none;
        padding: 0;
        margin: 0;
    }


    .pagination-item {
        margin: 0 5px;
    }

    .pagination-item a {
        padding: 8px 12px;
        border: 1px solid #0073e6;
        color: #0073e6;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .pagination-item a:hover {
        background-color: #0073e6;
        color: white;
    }

    .pagination-item.active a {
        background-color: #0073e6;
        color: white;
        pointer-events: none;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .shop-sidebar {
            width: 100%;
            margin-bottom: 20px;
        }

        .shop-products {
            width: 100%;
        }

        .product-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 480px) {
        .product-grid {
            grid-template-columns: 1fr;
        }
    }
</style>