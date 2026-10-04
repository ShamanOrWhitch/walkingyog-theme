<?php
get_header();
?>
<?
/**
 * Template Name: Vk.com Include
*/
?>
<!-- Page Content -->
<div class="container">
<? 
    if (have_posts()) { ?>
    <? while (have_posts()) { ?>
        <? the_post(); ?>
    <!-- Page Heading/Breadcrumbs -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?php the_title(); ?>
                <small>автор <a href="#"><?php the_author(); ?></a>
							</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.html">Home</a>
                </li>
                <li class="active">Sidebar Page</li>
            </ol>
        </div>
    </div>
    <!-- /.row -->

    <!-- Content Row -->
    <div class="row">
        
        <!-- Content Column -->
        <div class="col-md-12">
            <p><? the_content(); ?></p>
        </div>
    </div>
    
    <? } ?>
    <? } ?>
    <!-- /.row -->
</div>
    <hr>


<?php
get_footer();
?>