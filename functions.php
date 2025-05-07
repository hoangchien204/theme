<?php

/** 
 * Theme Functions goes here 
 **/


add_theme_support('title-tag');

// calling all important files like css js jquery and bootstrap
function fruity_register_important_files()
{
    wp_enqueue_style('fruity_style', get_stylesheet_uri());

    // registering bootstrap
    wp_register_style('fruity_bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_bootstrap');
    
    // registering custom css
    wp_register_style('fruity_root_stylesheet', get_template_directory_uri() . '/css/root.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_root_stylesheet');
    wp_register_style('fruity_hero_stylesheet', get_template_directory_uri() . '/css/home/hero.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_hero_stylesheet');
    wp_register_style('fruity_home_cetegory_stylesheet', get_template_directory_uri() . '/css/home/category.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_home_cetegory_stylesheet');
    wp_register_style('fruity_hurry_up_stylesheet', get_template_directory_uri() . '/css/home/hurry_up.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_hurry_up_stylesheet');
    wp_register_style('fruity_why_chose_us', get_template_directory_uri() . '/css/home/why_chose_us.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_why_chose_us');
    wp_register_style('fruity_any_qs', get_template_directory_uri() . '/css/home/any_qs.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_any_qs');
    wp_register_style('fruity_our_clints', get_template_directory_uri() . '/css/home/our_clints.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_our_clints');
    wp_register_style('fruity_banner', get_template_directory_uri() . '/css/home/banner.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_banner');
    wp_register_style('fruity_header', get_template_directory_uri() . '/css/header.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_header');
    wp_register_style('fruity_footer', get_template_directory_uri() . '/css/footer.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_footer');
    
    wp_register_style('fruity_login', get_template_directory_uri() . '/css/log-in.css', array(), "4.0.0", "all");
    wp_enqueue_style('fruity_login');
    //google font
    wp_enqueue_style('fruity_google_font', 'https://fonts.googleapis.com/css2?family=Ubuntu&display=swap', false);
    // font awesome
    wp_enqueue_style('fruity_font_awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', false);


    // registering js
    wp_enqueue_script('jquery');
    wp_enqueue_script('fruity_bootstrap_js', get_template_directory_uri() . '/js/bootstrap.js', array(), "4.0.0", "true");
    wp_enqueue_script('fruity_root_js', get_template_directory_uri() . '/js/root.js', array(),  "1.0.0", "true");
    wp_enqueue_script('fruity_menu_js', get_template_directory_uri() . '/js/responsive_menu.js', array(),  "1.0.0", "true");
    wp_enqueue_script('fruity_hurry_up_counter_js', get_template_directory_uri() . '/js/home/hurry_up_countdown.js', array(),  "1.0.0", "true");
    wp_enqueue_script('fruity_hero_js', get_template_directory_uri() . '/js/home/hero.js', array(),  "1.0.0", "true");
}
add_action("wp_enqueue_scripts", "fruity_register_important_files");

// registering menu
function Fruity_Menus() {
  register_nav_menus(
    array(
      'header-menu' => __( 'Header' ),
      'footer-menu' => __( 'Footer' )
     )
   );
 }
 add_action( 'init', 'Fruity_Menus' );

 add_action('after_setup_theme', 'fruity_theme_setup');
 function fruity_theme_setup() {
     add_theme_support('page-attributes');
 }

 //shop
 function fruity_enqueue_shop_styles() {
  if (is_page_template('page-shop.php')) {
      wp_enqueue_style('fruity_shop', get_template_directory_uri() . '/css/shop.css', array(), '1.0.0', 'all');
  }
}
add_action('wp_enqueue_scripts', 'fruity_enqueue_shop_styles');