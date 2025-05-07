<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
    <meta charset="<?php bloginfo('charset') ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
        .wrap-404 {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            max-width: 600px;
        }

        h1 {
            font-size: 6em;
            color: #333;
            margin: 0;
        }

        p {
            font-size: 1.2em;
            color: #555;
        }

        a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
    <!-- calling wp head -->
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <!-- wp header -->
    <?php get_header();?>
    <div class="container wrap-404">
    <div class="">

        <h1>404</h1>
        <p>Oops! Page not found.</p>
        <p>Sorry, but the page you are looking for might be in another castle.</p>
        <p>
            Go back to <a href="<?php echo home_url(); ?>">home</a>
        </p>
    </div>    
    </div>
    
    <!-- wp footer -->
    <?php get_footer();?>

    <!-- calling wp foot -->
    <?php wp_footer(); ?>
</body>

</html>