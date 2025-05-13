<?php
/*
Template Name: Product Detail Page
Description: Trang hiển thị chi tiết sản phẩm từ bảng wp_hanghoa
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php get_header(); ?>
<?php get_template_part("Templates/common-banner"); ?>

<section class="product-detail-page">
    <div class="container">
        <?php
        global $wpdb;
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $product = $wpdb->get_row("
    SELECT MaHH, TenHH, DonGia, MoTa, Hinh, SoLanMua, MaLoai
    FROM {$wpdb->prefix}hanghoa
    WHERE MaHH = $id
");


        if ($product) :
            $image_path = $product->Hinh ? get_template_directory_uri() . '/img/' . $product->Hinh : '';
        ?>
        <div class="product-detail-wrapper">
            <div class="product-image">
                <?php if ($image_path) : ?>
                    <img src="<?php echo esc_url($image_path); ?>" alt="<?php echo esc_attr($product->TenHH); ?>">
                <?php endif; ?>
            </div>
            <div class="product-info">
                <h1 id="product-name"><?php echo esc_html($product->TenHH); ?></h1>
                <p class="product-price"><?php echo number_format($product->DonGia, 0, ',', '.') . ' VND'; ?></p>
                <p class="product-sales">Đã bán: <?php echo esc_html($product->SoLanMua); ?></p>
                <div class="product-description">
                    <h3>Mô tả sản phẩm:</h3>
                    <p><?php echo esc_html($product->MoTa); ?></p>
                </div>

                <!-- Nút hành động -->
                <div class="product-actions">
    <button class="btn-add-to-cart" onclick="addToCart(<?php echo $product->MaHH; ?>)">
        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
    </button>
    <button class="btn-buy-now" onclick="buyNow(<?php echo $product->MaHH; ?>)">
        <i class="fas fa-bolt"></i> Mua ngay
    </button>
</div>
            </div>

            <?php
    // Truy vấn sản phẩm cùng loại (trừ sản phẩm hiện tại)
    $related_products = $wpdb->get_results("
        SELECT MaHH, TenHH, DonGia, Hinh
        FROM {$wpdb->prefix}hanghoa
        WHERE MaLoai = {$product->MaLoai}
        AND MaHH != {$product->MaHH}
        LIMIT 4
    ");
?>
<?php if ($related_products): ?>
<div class="related-products">
    <h3>Sản phẩm cùng loại</h3>
    <div class="related-products-list">
        <?php foreach ($related_products as $rp): ?>
            <div class="related-product-item">
                <a href="?id=<?php echo $rp->MaHH; ?>">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/img/' . $rp->Hinh); ?>" alt="<?php echo esc_attr($rp->TenHH); ?>">
                    <h4><?php echo esc_html($rp->TenHH); ?></h4>
                    <p><?php echo number_format($rp->DonGia, 0, ',', '.') . ' VND'; ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="related-products-more">
        <a href="/wordpress/shop?maloai=<?php echo $product->MaLoai; ?>" class="btn-view-more">Xem thêm</a>
    </div>
</div>
<?php endif; ?>

        </div>
        <?php else : ?>
            <p>Không tìm thấy sản phẩm.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
<?php wp_footer(); ?>

<!-- JS mô phỏng -->
<script>
function addToCart(productId) {
    // Lấy thông tin sản phẩm từ HTML
    const name = document.getElementById('product-name').innerText;
    const priceText = document.querySelector('.product-price').innerText;
    const price = parseInt(priceText.replace(/[^\d]/g, ''));
    const image = document.querySelector('.product-image img').src;

    // Tạo object sản phẩm
    const product = {
        id: productId,
        name: name,
        price: price,
        image: image,
        quantity: 1
    };
    console.log("🛒 Thêm vào giỏ:", product);
    // Lấy giỏ hàng từ LocalStorage hoặc tạo mới
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Kiểm tra sản phẩm đã có trong giỏ chưa
    const existing = cart.find(item => item.id === productId);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push(product);
    }

    // Lưu lại vào localStorage
    localStorage.setItem('cart', JSON.stringify(cart));

    // Chuyển đến trang giỏ hàng
    window.location.href = "/wordpress/gio-hang";
}

function buyNow(productId) {
    // Lấy thông tin sản phẩm từ HTML
    const name = document.getElementById('product-name').innerText;
    const priceText = document.querySelector('.product-price').innerText;
    const price = parseInt(priceText.replace(/[^\d]/g, ''));
    const image = document.querySelector('.product-image img').src;

    // Tạo object sản phẩm
    const product = {
        id: productId,
        name: name,
        price: price,
        image: image,
        quantity: 1
    };
    const selectedItems = [product];

    // Lưu vào localStorage để dùng ở trang thanh toán
    localStorage.setItem('selectedItemsForCheckout', JSON.stringify(selectedItems));

    // Chuyển đến trang thanh toán
    window.location.href = "/wordpress/check-out";
}
</script>
</body>
</html>

<style>
.product-detail-page {
    padding: 30px;
}

.common-banner {
    width: 100%;
    height: 100px;
}

.product-detail-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
}

.product-image {
    flex: 1 1 40%;
    max-width: 400px;
}

.product-image img {
    width: 100%;
    height: auto;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.product-info {
    flex: 1 1 55%;
}

.product-info h1 {
    font-size: 28px;
    margin-bottom: 15px;
}

.product-price {
    font-size: 22px;
    color: #0073e6;
    margin-bottom: 10px;
    font-weight: bold;
}

.product-sales {
    font-size: 16px;
    color: #555;
    margin-bottom: 20px;
}

.product-description {
    font-size: 16px;
    line-height: 1.6;
}

.product-actions {
    margin-top: 25px;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-add-to-cart{
    flex: 1 1 100%;
    padding: 14px 28px;
    border: none;
    border-radius: 999px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    background-color: #0c3f14;
    color: #fff;
    transition: background-color 0.3s ease;
    text-align: center;
}
.btn-buy-now {
    flex: 1 1 100%;
    padding: 14px 28px;
    border: none;
    border-radius: 999px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    background-color: #d68282;
    color: #fff;
    transition: background-color 0.3s ease;
    text-align: center;
}
.btn-add-to-cart:hover{
    background-color: #000;
}
.btn-buy-now:hover {
    background-color: #913636;
}
.btn-add-to-cart i,
.btn-buy-now i {
    margin-right: 8px;
    font-size: 16px;
    vertical-align: middle;
}


.related-products {
    margin-top: 50px;
}

.related-products h3 {
    font-size: 22px;
    margin-bottom: 20px;
}

.related-products-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.related-product-item {
    width: 200px;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    text-align: center;
    background-color: #fff;
    transition: box-shadow 0.3s ease;
}

.related-product-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.related-product-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.related-product-item h4 {
    font-size: 16px;
    margin: 10px 0;
    padding: 0 10px;
    height: 42px;
    overflow: hidden;
}

.related-product-item p {
    color: #0073e6;
    font-weight: bold;
    margin-bottom: 10px;
}

.related-products-more {
    margin-top: 20px;
    text-align: center;
}

.btn-view-more {
    display: inline-block;
    padding: 10px 20px;
    background-color: #444;
    color: #fff;
    border-radius: 999px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s ease;
}

.btn-view-more:hover {
    background-color: #000;
}

</style>
