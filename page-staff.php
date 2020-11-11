<?php /* Template name: Staff */ ?>

<?php get_header(); ?>

<script src="<?php bloginfo('template_directory'); ?>/js/isotope.pkgd.min.js"></script>

	<div class="content">

	

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    		<header class="page-title">

    			<h1><?php the_title(); ?></h1>

    		</header>

    		<article class="post">

    				<?php the_content(); ?>

    			<?php endwhile; else: ?>

    				<p>Sorry, no posts matched your criteria.</p>

    			<?php endif; ?>

    		</article>

	

	

			<?php if (have_posts()) : ?>

				<div class="post-list staff">

					<?php $my_query = new WP_Query( array(

                                                  'post_type' => 'staff',

                                                  'posts_per_page' => -1

                                                  //'order_by' => 'menu_order'

                                             ) );



					while( $my_query->have_posts() ) : $my_query->the_post(); ?>

 

						<div class="col-3"><a href="<?php the_permalink(); ?>" class="smooth"><?php the_post_thumbnail(); ?></a></div>



                    <?php endwhile; ?>

				</div>

			<?php else : ?>

				<h2 class="center">Not Found</h2>

			<?php endif; ?>

			<?php //get_sidebar(); ?>

			<div style="clear:both"></div>

	</div>



<script type="text/javascript">

// initialize Isotope after all images have loaded

var $container = $('#container').imagesLoaded( function() {

  $container.isotope({

    // options

  });

});

</script>

<?php get_footer(); ?>