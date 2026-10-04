<?php
	get_header();
?>

<?php if (have_posts()) { ?>
    <?php
        the_post()
    ?>

<!-- Page Content -->
<div class="container">
    <?php
    $args = array("post_type" => 'yoga_guide');
    $yoga_guide = new WP_Query($args);
    $miniature_url = get_the_post_thumbnail_url($post->ID);
    ?>
            <!-- Page Heading/Breadcrumbs -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"> Walking  Yog
                        <small>Subheading</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="index.html">Home</a>
                        </li>
                        <li class="active"><?php the_category('yoga_guide'); ?></li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->

    <!-- Project One -->

<?php while ($yoga_guide->have_posts()){ ?>
    <?php $yoga_guide->the_post(); ?>
    <?php $miniature_url_relate = get_the_post_thumbnail_url($post->ID);?>
    <?php $title_yoga_guide = get_the_title($post->ID);?>
    <div class="row">
        <div class="col-md-7">
            <a href="<?php echo get_post_permalink($post->ID); ?>">
                <img class="img-responsive img-hover" src="<?php echo $miniature_url_relate; ?>" alt="">
            </a>
        </div>
        <div class="col-md-5">
            <h3><?php echo $title_yoga_guide; ?></h3>
            <p><?php the_excerpt(); ?></p>
            <a class="btn btn-primary" href="<?php echo get_post_permalink($post->ID); ?>">Ознакомиться</i></a>
        </div>
    </div>
    <!-- /.row -->
    <hr>
<?php } ?>

     

    <hr>



<?php } ?>

<?php
get_footer();
?>
