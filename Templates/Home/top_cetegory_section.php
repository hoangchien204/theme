<style>
.category-item {
    border: 1px solid #eee;
    border-radius: 15px;
    padding: 20px;
    transition: all 0.3s ease;
    background-color: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    height: 100%;
}
.category-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.category-item img {
  
    object-fit: cover;
    border-radius: 10px;
    display: block;
    margin: 0 auto;       /* căn giữa ảnh */
}
.category-item-link {
    text-decoration: none;
    color: #333;
    font-size: 1.1rem;
}
.category-item-link:hover {
    color: #e67e22;
}
.font-size-small {
    font-size: 0.9rem;
    color: #666;
}

.row2{
    margin-top: 50px;
    padding: 0px 280px;
   
    margin-left: 15px;
}
.row2 div{

    max-width: 95%;
    margin: 0;

}
.hover-border {
    width: 50%;
    height: 3px;
    background-color: #e67e22;
    margin: 10px auto 0 auto;
    border-radius: 2px;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.category-item:hover .hover-border {
    opacity: 1;
}

</style>

<?php
global $wpdb;
$results = $wpdb->get_results("
    SELECT l.MaLoai, l.TenLoai, l.Hinh, COUNT(h.MaHH) AS SoLuong
    FROM wp_loai l
    LEFT JOIN wp_hanghoa h ON l.MaLoai = h.MaLoai
    GROUP BY l.MaLoai, l.TenLoai, l.Hinh
");

echo '<div class="row row2">';

foreach ($results as $row):
?>
<div class="col-lg-3 col-md-4 col-sm-6 text-center mb-4">
    <div class="category-item pt-5">
        <img class="w-50" src="<?php echo esc_url($row->Hinh); ?>" alt="<?php echo esc_attr($row->TenLoai); ?>">
        <p class="font-weight-bold mt-3">
            <a href="<?php echo site_url('/shop/?maloai=' . urlencode($row->MaLoai)); ?>" class="category-item-link">
                <?php echo esc_html($row->TenLoai); ?>
            </a>
        </p>
        <p class="font-weight-bold font-size-small">(<?php echo intval($row->SoLuong); ?> Item)</p>
        <div class="hover-border mt-3"></div>
    </div>
</div>
<?php endforeach;

echo '</div>';
?>
 