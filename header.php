<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17165078467"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-17165078467');
</script>

    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(''); ?></title>
    <?php wp_head(); ?>
    <style>
#wy-preloader {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999999;
    transition: opacity 1s ease;
}
#wy-preloader.hide {
    opacity: 0;
    pointer-events: none;
}
#wy-preloader img {
    width: 180px;
    height: auto;
}
</style>

    <style>
        body {margin:0; background:#000; color:#0ff; font-family:Arial; overflow-x:hidden;}
        .navbar-inverse {background:rgba(0,0,0,0.8)!important; border:none;}
        .container {padding-top:80px;}
    </style>
    
    <script>
    var wy_template_uri = "<?php echo get_template_directory_uri(); ?>";
</script>

</head>
<body <?php body_class(); ?>>

<div class="container">