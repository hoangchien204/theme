<?php
/**
 * Template Name: Order History
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

    <div class="container-oder order-history-page">
            <?php
            // Nội dung từ plugin qua shortcode
            echo do_shortcode('[order_history]');
            ?>
    </div>


    <?php get_footer(); ?>
    <!-- Calling wp_footer -->
    <?php wp_footer(); ?>
</body>

</html>

<style>
    .common-banner {
            width: 100%;
            height: 200px;
        }
</style>