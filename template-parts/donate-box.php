<?php
/**
 * Та же шкатулка, что на главной. Не ссылка на bcm4sa.
 */
if (!defined('ABSPATH')) {
    exit;
}
$img = 'https://walkingyog.com/wp-content/uploads/2025/12/Стеклянная-донатная-коробка_1.png';
?>
<a class="wy-chest" href="<?php echo esc_url(home_url('/donatik')); ?>" aria-label="Донат">
    <img src="<?php echo esc_url($img); ?>" alt="">
</a>
