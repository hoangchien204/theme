<?php
/*
Template Name: Shop Page
Description: Trang hiển thị danh sách sản phẩm kèm tìm kiếm và danh mục.
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
    <?php get_header();?>
    <?php get_template_part( "Templates/common-banner" );?>
    
    

    <!-- Main Shop Section -->
    <section class="shop-page">
        <div class="container">
            <div class="shop-sidebar">
                <!-- Product Categories -->
                <div class="shop-categories">
                    <h2>Danh mục sản phẩm</h2>
                    <?php
                    // Display product categories if WooCommerce is installed and product_cat exists
                    if (taxonomy_exists('product_cat')) {
                        wp_list_categories(array(
                            'taxonomy' => 'product_cat', // WooCommerce product categories
                            'title_li' => '',
                            'hide_empty' => false
                        ));
                    }
                    ?>
                </div>

                <!-- Product Search Form -->
                <div class="shop-search">
                    <h2>Tìm kiếm sản phẩm</h2>
                    <?php get_search_form(); // Default WordPress search form ?>
                </div>
            </div>

            <div class="shop-products">
                <h1>Sản phẩm của chúng tôi</h1>

                <div class="product-grid">
                    <?php
                    // Query to get all products
                    $args = array(
                        'post_type' => 'product', // WooCommerce products
                        'posts_per_page' => 12,   // Number of products to display
                        'post_status' => 'publish'
                    );
                    $query = new WP_Query($args);

                    if ($query->have_posts()) :
                        while ($query->have_posts()) : $query->the_post();
                            ?>
                            <div class="product-item">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="product-thumbnail">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-info">
                                        <h3 class="product-title"><?php the_title(); ?></h3>
                                        <p class="product-price">
                                            <?php echo wc_price(get_post_meta(get_the_ID(), '_price', true)); // WooCommerce price ?>
                                        </p>
                                    </div>
                                </a>
                            </div>
                            <?php
                        endwhile;
                    else :
                        echo '<p>Không có sản phẩm nào.</p>';
                    endif;

                    wp_reset_postdata();
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
.common-banner {
    background-image: url(img/banner/banner-single-page.jpg);
    background-attachment: fixed;
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    background-color: #071c1f;
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