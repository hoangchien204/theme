<?php
/*
Template Name: Shop Page
Description: Trang hiển thị danh sách sản phẩm từ bảng wp_hanghoa kèm tìm kiếm và danh mục.
*/

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

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

    <!-- Main Shop Section -->
    <section class="shop-page">
        <div class="container">
            <div class="shop-sidebar">
                <!-- Product Categories -->
                <div class="shop-categories">
                    <h2>Danh mục sản phẩm</h2>
                    <?php
                    // Display product categories (assuming a custom taxonomy or manual categories)
                    // If using wp_hanghoa MaLoai, you may need a separate table for categories
                   
// Lấy danh sách danh mục từ bảng wp_loai
$categories = $wpdb->get_results("SELECT MaLoai, TenLoai FROM {$wpdb->prefix}loai");

if ($categories) :
    echo '<ul>';
    foreach ($categories as $category) :
        $maLoai = intval($category->MaLoai);
        $tenLoai = esc_html($category->TenLoai);

        // Gắn link lọc sản phẩm theo MaLoai (dùng query string ?maloai=)
        echo '<li><a href="' . esc_url(add_query_arg('maloai', $maLoai, get_permalink())) . '">' . $tenLoai . '</a></li>';
    endforeach;
    echo '</ul>';
else :
    echo '<p>Không có danh mục nào.</p>';
endif;
?>

                </div>

                
            </div>

            <div class="shop-products">
                <h1>Sản phẩm của chúng tôi</h1>

                <div class="product-grid">
                    <?php
                    global $wpdb;
                    $maloai = isset($_GET['maloai']) ? intval($_GET['maloai']) : 0;
$where_clause = $maloai > 0 ? "WHERE MaLoai = $maloai" : "";

$results = $wpdb->get_results("
    SELECT MaHH, TenHH, DonGia, SoLanMua, Hinh
    FROM {$wpdb->prefix}hanghoa
    $where_clause
    ORDER BY MaHH DESC
    LIMIT 12
");

                    if ($results) :
                        foreach ($results as $product) :
                            $product_id = $product->MaHH;
                            $product_name = $product->TenHH;
                            $price = $product->DonGia;
                            $sales = $product->SoLanMua;
                            $image_path = $product->Hinh ? get_template_directory_uri() . '/img/' . $product->Hinh : '';
                            ?>
                            <div class="product-item">
                                <a href="<?php echo esc_url(home_url('/chi-tiet-san-pham/?id=' . $product_id)); ?>">
                                    <?php if ($image_path) : ?>
                                        <div class="product-thumbnail">
                                            <img src="<?php echo esc_url($image_path); ?>" alt="<?php echo esc_attr($product_name); ?>">
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-info">
                                        <h3 class="product-title"><?php echo esc_html($product_name); ?></h3>
                                        <p class="product-price"><?php echo number_format($price, 0, ',', '.') . ' VND'; ?></p>
                                        <p class="product-sales">Đã bán: <?php echo $sales ? esc_html($sales) : 0; ?></p>
                                    </div>
                                </a>
                            </div>
                            <?php
                        endforeach;
                    else :
                        echo '<p>Không có sản phẩm nào.</p>';
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </section>

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

.container {
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

.product-thumbnail img {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
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