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

    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();

    $sheet = $dir . '/css/sheet.css';
    if (file_exists($sheet)) {
        wp_enqueue_style(
            'wy-sheet',
            $uri . '/css/sheet.css',
            ['core-style'],
            filemtime($sheet)
        );
    }

    $levels_css = $dir . '/css/wy-levels.css';
    if (file_exists($levels_css)) {
        wp_enqueue_style(
            'wy-levels',
            $uri . '/css/wy-levels.css',
            ['core-style'],
            filemtime($levels_css)
        );
    }

    $levels_js = $dir . '/js/wyLevels.js';
    if (file_exists($levels_js)) {
        wp_enqueue_script(
            'wy-levels',
            $uri . '/js/wyLevels.js',
            [],
            filemtime($levels_js),
            true
        );
    }

});
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'namaste-local',
    get_stylesheet_directory_uri() . '/fonts/namaste.css',
    [],
    '1.0'
  );
});

add_shortcode('wy_cell', function ($atts, $content = null) {
    $atts = shortcode_atts(array(
        'lane' => '',
        'title' => '',
    ), $atts, 'wy_cell');
    $lane = $atts['lane'] !== '' ? ' data-lane="' . esc_attr($atts['lane']) . '"' : '';
    $title = $atts['title'] !== '' ? '<h2 class="wy-cell__title">' . esc_html($atts['title']) . '</h2>' : '';
    return '<section class="wy-cell" data-stick' . $lane . '>' . $title . '<div class="wy-cell__body">' . do_shortcode($content) . '</div></section>';
});

add_shortcode('wy_levels', function ($atts, $content = null) {
    return '<div class="wy-levels" data-wy-levels tabindex="0">' . do_shortcode($content) . '</div>';
});
