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
    <?php get_template_part( "Templates/common-banner" );?>
    
    
    <!-- wp footer -->
    <?php get_footer();?>

    <!-- calling wp foot -->
    <?php wp_footer(); ?>
</body>

</html>