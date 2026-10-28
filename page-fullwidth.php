<?php
/**
 * Template Name: Full Width 12
 * Template Post Type: page
 *
 * Страница на всю ширину. Без строки автора, без хлебных крошек, без сайдбара.
 * Шкатулка доната — та же картинка, что на главной, и ведёт в донат, не на bcm4sa.
 */
get_header();
?>
<article class="wy-sheet">
<?php
if (have_posts()) {
    while (have_posts()) {
        the_post();
        ?>
        <h1 class="wy-sheet__title"><?php the_title(); ?></h1>
        <div class="wy-sheet__content">
            <?php the_content(); ?>
        </div>
        <?php
    }
}
?>
</article>
<?php get_template_part('template-parts/donate-box'); ?>
<?php
get_footer();
