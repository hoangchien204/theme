<?php
/*
Template Name: Sign Up
Description: A custom sign-up page template for Fruity theme.
*/
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php get_header(); ?>
    <?php get_template_part("Templates/common-banner"); ?>
    
    <!-- Sign up form start -->
    <div class="container w-50">
        <h1 class="main-color text-center mt-5 mb-4">Create an Account</h1>

        <?php
        // Kiểm tra trạng thái đăng nhập bằng cookie của hệ thống tùy chỉnh
        $is_custom_logged_in = isset($_COOKIE['custom_user_logged_in']) && isset($_COOKIE['custom_user_id']);
        if ($is_custom_logged_in) {
            echo '<p class="text-center">Bạn đã đăng nhập! <a href="' . esc_url(add_query_arg('logout', 'true', home_url())) . '">Đăng xuất</a></p>';
        } else {
            // Hiển thị thông báo lỗi hoặc thành công
            if (isset($_GET['signup'])) {
                switch ($_GET['signup']) {
                    case 'empty_fields':
                        echo '<p class="text-center text-danger">Vui lòng điền đầy đủ thông tin.</p>';
                        break;
                    case 'user_exists':
                        echo '<p class="text-center text-danger">Tên đăng nhập hoặc email đã tồn tại.</p>';
                        break;
                    case 'failed':
                        echo '<p class="text-center text-danger">Đăng ký thất bại. Vui lòng thử lại.</p>';
                        break;
                    case 'terms_not_agreed':
                        echo '<p class="text-center text-danger">Bạn phải đồng ý với điều khoản dịch vụ.</p>';
                        break;
                    case 'password_mismatch':
                        echo '<p class="text-center text-danger">Mật khẩu không khớp. Vui lòng nhập lại.</p>';
                        break;
                }
            }
        ?>

        <form method="POST" action="">
            <?php wp_nonce_field('clp_signup_action', 'clp_signup_nonce'); ?>

            <div class="mb-4">
                <input
                    type="text"
                    name="username"
                    id="fruity-signup-name"
                    class="w-100 form-control-lg p-2 border-1 border"
                    required
                />
                <label class="form-label" for="fruity-signup-name">Your Name</label>
            </div>

            <div class="form-outline mb-4">
                <input
                    type="email"
                    name="email"
                    id="fruity-signup-email"
                    class="w-100 form-control-lg p-2 border-1 border"
                    required
                />
                <label class="form-label" for="fruity-signup-email">Your Email</label>
            </div>

            <div class="form-outline mb-4">
                <input
                    type="password"
                    name="password"
                    id="fruity-signup-pass"
                    class="w-100 form-control-lg p-2 border-1 border"
                    required
                />
                <label class="form-label" for="fruity-signup-pass">Password</label>
            </div>

            <div class="form-outline mb-4">
                <input
                    type="password"
                    name="password_again"
                    id="fruity-signup-pass-again"
                    class="w-100 form-control-lg p-2 border-1 border"
                    required
                />
                <label class="form-label" for="fruity-signup-pass-again">Repeat your password</label>
            </div>

            <div class="form-check d-flex mb-5">
                <input
                    class="form-check-input me-2"
                    type="checkbox"
                    name="terms_agreed"
                    id="fruity-signup-check"
                    required
                />
                <label class="form-check-label" for="fruity-signup-check">
                    I agree all statements in
                    <br />
                    <a href="#!" class="text-body"><u>Terms of service</u></a>
                </label>
            </div>

            <div class="d-flex justify-content-center">
                <button
                    type="submit"
                    name="clp_signup_submit"
                    class="log-in-button btn-block mb-4 p-2 text-white"
                >
                    Register
                </button>
            </div>

            <p class="text-center text-muted mt-5 mb-0">
                Have already an account?
                <a class="main-color" href="<?php echo esc_url(home_url('/dang-nhap')); ?>" class="fw-bold text-body">
                    <u>Login here</u>
                </a>
            </p>
        </form>
        <?php } ?>
    </div>
    <!-- Sign up form end -->
    
    <?php get_footer(); ?>
    <?php wp_footer(); ?>
</body>
</html>