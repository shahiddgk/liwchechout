<?php /* Template name: No Sidebar */ ?>
<?php get_header(); ?>
<?php if ( get_field('hero_image') != '' ) { ?>}
	<div class=page-hero style="background-image:url('<?php the_field('hero_image'); ?>');"></div>
<?php } else { ?>
	<style>
		.content { padding:60px 0; }
	</style>
<?php } ?>
	<section class="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<h1 style="text-align:center; font-size:30pt; padding-bottom:20px; line-height:30pt;"><?php the_title(); ?></h1>
    		<article class="post">
    				<?php the_content(); ?>
    			<?php endwhile; else: ?>
    				<p>Sorry, no posts matched your criteria.</p>
    			<?php endif; ?>
    		</article>
				
			<?php
				if( have_rows('gallery') ):
					while ( have_rows('gallery') ) : the_row(); ?>
						<a href="<?php the_sub_field('image'); ?>" data-lightbox="page-gallery" class=single-gallery-image style="background-image:url('<?php the_sub_field('image'); ?>');">
							<img src="<?php the_sub_field('image'); ?>" style="visibility:hidden" />
						</a>
					<?php endwhile;
				endif;
			?>
    </section><!-- close content -->
<?php get_footer(); ?>