<?php
	/* Template Name: Videos */
	get_header();
?>
<div class=page-hero style="background-image:url('<?php the_field('hero_image'); ?>');">
			</div>
	<section class="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    		<article class="post">
    				<?php the_content(); ?>
    			<?php endwhile; else: ?>
    				<p>Sorry, no posts matched your criteria.</p>
    			<?php endif; ?>
    		</article>	
			<?php
				if( have_rows('videos') ):
					$count = 1;
					echo '<div>';
					while ( have_rows('videos') ) : the_row(); ?>
						<div class=video>
							<div class="video-container">
								<iframe src="<?php the_sub_field('embed_code'); ?>" width="958" height="539" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
							</div>
							<h3>
								<?php the_sub_field('title'); ?>
							</h3>
							<p>
								<?php the_sub_field('description'); ?>
							</p>
						</div>
					<?php endwhile;
					echo '</div>';
				endif;
			?>
			<div style=clear:both></div>
    </section><!-- close content -->
	<?php if ( get_field('sidebar') != '') { ?>
		<style>.content {float:left;width:70%;}</style>
		<aside class=sidebar>
			<?php the_field('sidebar'); ?>
		</aside>
		<div style=clear:both></div>
	<?php } ?>
<?php get_footer();?>