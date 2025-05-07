<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
    <meta charset="<?php bloginfo('charset') ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- calling wp head -->
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <!-- wp header -->
    <?php get_header();?>
    
    <!-- hero slider -->
    <?php get_template_part("Templates/Home/hero"); ?>
    
    <!-- top cetegory -->
    <?php get_template_part("Templates/Home/banner"); ?>
    
    <!-- top cetegory -->
    <?php get_template_part("Templates/Home/top_cetegory_section"); ?>
    
    <!-- hurry up -->
    <?php get_template_part("Templates/Home/hurry_up_section"); ?>
    
    <!-- why chose us -->
    <?php get_template_part("Templates/Home/why_chose_us"); ?>
    
    <!-- any qs -->
    <?php //get_template_part("Templates/Home/any_qs"); ?>
    
    <!-- wp footer -->
    <?php get_footer();?>

    <!-- calling wp foot -->
    <?php wp_footer(); ?>
</body>

</html>