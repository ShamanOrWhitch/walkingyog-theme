<?php
get_header();
?>

<?php if (have_posts()) { ?>
    <?php
        the_post()
    ?>
<!-- Page Content -->
 <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php the_title(); ?>
                    <small>Subheading</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/">Home</a>
                    </li>
                    <li class="active">traveling Item</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

        <!-- Traveling Item Row -->
        <div class="row">
    <?php
    $args = array("post_type" => 'traveling');
    $traveling = new WP_Query($args);
    $miniature_url = get_the_post_thumbnail_url($post->ID);
    ?>


            <div class="col-md-8">
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                   <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                    </ol> 

                    <!-- Wrapper for slides -->
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
								<img class="img-responsive" src="<?php echo $image; ?>" alt="">
							</div>
						<?php } ?>
						<?php $i++; ?>
					<?php } ?>
                    </div>
                    <? echo $miniature; ?>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <h3>Описание проекта</h3>
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

        </div>
        <!-- /.row -->

        <!-- Related Projects Row -->
        <div class="row">

            <div class="col-lg-12">
                <h3 class="page-header">Еще проекты</h3>
            </div>

				<?php
					$args = array(
						'post_type'=>"traveling",
						'posts_per_page' => 3,
						'post__not_in' => array( $post->ID )
					);
					//print_r($args);
					$traveling = new WP_Query($args);
					
				?>
                <?php while ($traveling->have_posts()) { ?>
                   <?php $traveling->the_post(); ?>
                   <?php $miniature_url_relate = get_the_post_thumbnail_url($post->ID);?>
            
            <div class="col-sm-3 col-xs-6">
                <a href="<?php echo get_post_permalink($post->ID); ?>">
                    <img class="img-responsive img-hover img-related" src="<?php echo $miniature_url_relate; ?>" alt="">
                </a>
            </div>

            <?php  } ?>
        </div>
        <!-- /.row -->

        <hr>

    </div>
<?php } ?>

<?php
get_footer();
?>
