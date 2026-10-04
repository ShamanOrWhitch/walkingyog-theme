<?php
	get_header();
?>

    <!-- Page Content -->
    <div class="container">
		<?php if (have_posts()) { ?>
			<?php while (have_posts()) { ?>
				<?php the_post(); ?>
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
							<li class="active">Blog Post</li>
						</ol>
					</div>
				</div>
				<!-- /.row -->

				<!-- Content Row -->
				<div class="row">
					<!-- Blog Post Content Column -->
					<div class="col-lg-8">
						<!-- Blog Post -->
						<hr>
						<!-- Date/Time -->
						<p><i class="fa fa-clock-o"></i> <?php the_date(); ?></p>
						<hr>
						<!-- Preview Image -->
						<img class="img-responsive" src="<?php echo get_the_post_thumbnail_url($post->ID); ?>" alt="">
						<hr>
						<!-- Post Content -->
						<p>
							<?php
								the_content();
							?>
						</p>
						<hr>
					
                    <?php } ?>
                <?php } ?>
                    </div>
                    <!-- Blog Sidebar Widgets Column -->
                    <div class="col-md-4">
                        <?php if ( is_active_sidebar( 'news_sidebar' ) ) : ?>
//sidebar 
                           <div id="news_sidebar" class="sidebar">
                               <?php dynamic_sidebar( 'news_sidebar' ); ?>
                               <hr>
                            <h3>Описание проекта</h3> //дополнительная документация
                            <p>
                                <?php 
                                    echo get_field( "project_description" ); 
                                ?>
                            </p>

                            <h3>Детали проекта</h3>
                                <?php 
                                    echo get_field( "project_details" ); 
                                ?>        

                           </div>
 
                        <?php endif; ?>
                    </div>

// slider                
                        <div class="carousel-inner">
						<?php
							$images = array();
						?>
						<?php
							$images[0] = get_field("traveling_images1");
							$images[1] = get_field("traveling_images2");
							$images[2] = get_field("traveling_images3");
						?>
						<?php
							$i=0;
						?>
						<?php foreach ($images as $image) { ?>
						<?php if (isset($image[$i])) { ?>
							<?php 
								if ($i==0) { 
									$active="active";
								} else {
									$active='';
								}
							?>
							<div class="item <?php echo $active; ?>">
								<img class="img-responsive" src="<?php echo $image; ?>" alt="123">
							</div>
						<?php } ?>
						<?php $i++; ?>
					<?php } ?>
                    </div>

        <!-- Related Projects Row -->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header">Еще Новости</h3>
            </div>

				<?php
					$args = array(
						'post_type'=>"news",
						'posts_per_page' => 3,
						'post__not_in' => array( $post->ID )
					);
					//print_r($args);
					$m_news = new WP_Query($args);
					
				?>
                <?php while ($m_news->have_posts()) { ?>
                   <?php $m_news->the_post(); ?>
                   <?php $miniature_url_relate = get_the_post_thumbnail_url($post->ID);?>

            <div class="col-sm-3 col-xs-6">
                <a href="<?php echo get_post_permalink($post->ID); ?>">
                    <img class="img-responsive img-hover img-related" src="<?php echo $miniature_url_relate; ?>" alt="">
                </a>
            </div>

            <?php  } ?>
        </div>
        </div>
        <!-- /.row -->
                  
        </div>
<hr>

<hr>


<?
get_footer();
?>
