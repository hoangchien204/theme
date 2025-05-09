<?php
/*
Template Name: admin order
*/
?>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <?php get_template_part('admin-sidebar'); ?>
    <div class="admin-content">
         

        <!-- Chèn phần shortcode hiển thị danh sách đơn hàng -->
      
        <?php echo do_shortcode('[admin_order_list]'); ?>
    </div>
    <?php wp_head(); ?>


</div>


