<?php
// Safety
if (!defined('ABSPATH')) exit;

// Custom styles
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'custom-core',
    get_stylesheet_directory_uri() . '/core.css',
    [],
    time()
  );
});

add_action('wp_enqueue_scripts', function () {

    // основной стиль темы (если есть)
    wp_enqueue_style(
        'theme-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    // core.css — НАШ БАЗОВЫЙ СЛОЙ
    wp_enqueue_style(
        'core-style',
        get_stylesheet_directory_uri() . '/css/core.css',
        ['theme-style'], // зависит от style.css
        filemtime(get_stylesheet_directory() . '/css/core.css')
    );

});
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'namaste-local',
    get_stylesheet_directory_uri() . '/fonts/namaste.css',
    [],
    '1.0'
  );
});
