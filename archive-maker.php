<?php
	get_header();
?>
    <!-- Page Content -->
    <div class="container">
		
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Творчество</h1>
                <div class="col-md-4">
                    
                        <?php if ( is_active_sidebar( 'searcher' ) ) : ?>
 
	                   <div id="news_sidebar" class="searcher">
 
		                  <?php dynamic_sidebar( 'searcher' ); ?>
                            <?php endif; ?>
                        </div>
                    
                <ol class="breadcrumb">
                    <li><a href="index.html">Home</a>
                    </li>
                    <li class="active">Творческая лаборатория</li>
                </ol>
            </div>
                </div>
        </div>
        <!-- /.row -->
		<?php if (have_posts()) { ?>
			<?php while (have_posts()) { ?>
				<?php the_post(); ?>
        <?php $plink = get_post_permalink($post->ID); ?>
				<!-- Blog Post Row -->
		
        
        <div class="row">
					<div class="col-md-1 text-center">
						<p><i class="fa fa-camera fa-4x"></i>
						</p>
						<p><?php the_time('j F Y'); ?></p>
					</div>
					<div class="col-md-5">
						<a href="<?php echo $plink; ?>">
							<img class="img-responsive img-hover" src="<?php echo get_the_post_thumbnail_url($post->ID); ?>" alt="">
						</a>
					</div>
					<div class="col-md-6">
						<h3>
							<a href="<?php echo $plink; ?>"> <?php the_title(); ?></a>
						</h3>
						<p>автор <a href="#"><?php the_author(); ?></a>
						</p>
						<p><?php the_excerpt(); ?></p>
						<a class="btn btn-primary" href="<?php echo $plink; ?>">Посмотреть...  <i class="fa fa-angle-right"></i></a>
					</div>
				</div>
				<!-- /.row -->
				<hr>
			<?php } ?>
		<?php } ?>


        <!-- Pager -->
        <div class="row">
            <ul class="pager">
                <li class="previous"><a href="#">&larr; Older</a>
                </li>
                <li class="next"><a href="#">Newer &rarr;</a>
                </li>
            </ul>
        </div>
          
        <!-- /.row -->

        <hr>
<?
        get_footer();
?>
