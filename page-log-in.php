<?php
/*
Template Name: Login Page
Description: A custom login page template for Fruity theme.
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

    <!-- Log in form start -->
    <div class="container mt-5">
        <h1 class="text-center mb-4 main-color">Sign In</h1>

        <?php
        // Kiểm tra trạng thái đăng nhập bằng cookie của hệ thống tùy chỉnh
        $is_custom_logged_in = isset($_COOKIE['custom_user_logged_in']) && isset($_COOKIE['custom_user_id']);
        if ($is_custom_logged_in) {
            echo '<p class="text-center">Bạn đã đăng nhập! <a href="' . esc_url(add_query_arg('logout', 'true', home_url())) . '">Đăng xuất</a></p>';
        } else {
            // Hiển thị thông báo lỗi nếu đăng nhập thất bại
            if (isset($_GET['login']) && $_GET['login'] === 'failed') {
                echo '<p class="text-center text-danger">Đăng nhập thất bại. Vui lòng kiểm tra tên đăng nhập hoặc mật khẩu.</p>';
            }
        ?>

        <form class="w-50 m-auto" method="POST" action="">
            <?php wp_nonce_field('clp_login_action', 'clp_login_nonce'); ?>

            <div class="mb-4">
                <input
                    type="text"
                    id="fruity-login-username"
                    name="username"
                    class="w-100 p-2 border-1 border"
                    required
                />
                <label class="form-label" for="fruity-login-username">Tên đăng nhập</label>
            </div>
            <div class="form-outline mb-4">
                <input
                    type="password"
                    id="fruity-login-pass"
                    name="password"
                    class="w-100 p-2 border-1 border"
                    required
                />
                <label class="form-label" for="fruity-login-pass">Mật khẩu</label>
            </div>
            <div class="row mb-4">
                <div class="col d-flex justify-content-center">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="rememberme"
                            id="fruity-login-check"
                            checked
                        />
                        <label class="form-check-label" for="fruity-login-check">
                            Remember me
                        </label>
                    </div>
                </div>
                <div class="col">
                    <a class="main-color" href="<?php echo esc_url(home_url('/forgot-password')); ?>">Quên mật khẩu?</a>
                </div>
            </div>
            <button
                type="submit"
                class="log-in-button btn-block mb-4 p-2 text-white"
            >
                Đăng nhập
            </button>
            <div class="text-center">
                <p>
                    Chưa có tài khoản? <a class="main-color" href="<?php echo esc_url(home_url('/dang-ky')); ?>">Đăng ký</a>
                </p>
                <p>hoặc đăng nhập bằng:</p>
                <button type="button" class="main-color btn border-5 border btn-floating mx-1">
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button type="button" class="main-color btn border-5 border btn-floating mx-1">
                    <i class="fab fa-google"></i>
                </button>
                <button type="button" class="main-color btn border-5 border btn-floating mx-1">
                    <i class="fab fa-twitter"></i>
                </button>
                <button type="button" class="main-color btn border-5 border btn-floating mx-1">
                    <i class="fab fa-github"></i>
                </button>
            </div>
        </form>
        <?php } ?>
    </div>
    <!-- Log in form end -->

    <?php get_footer(); ?>
    <?php wp_footer(); ?>
</body>
</html>