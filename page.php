<?php
/**
 * Обычная страница: колонка на всю ширину, без автора и без демо-сайдбара Start Bootstrap.
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
