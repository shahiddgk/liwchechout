<?php
	/* Template Name: Photos */
	get_header();
	global $post;
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
				
			<ul class=photo-links>
			<?php
				$my_query = new wp_query(array(
					'post_type' => 'page',
					'posts_per_page' => -1,
					'post_parent' => 278,
					'orderby' => 'menu_order',
					'order' => 'ASC'
					)
				);
				
			while( $my_query->have_posts() ) : $my_query->the_post(); ?>
				<li>
					<a href="#photos-<?php echo $post->ID; ?>">
						<?php the_title(); ?>
					</a>
				</li>
			<?php endwhile; ?>
			</ul>
			<hr>
			<?php
				$my_query = new wp_query(array(
					'post_type' => 'page',
					'posts_per_page' => -1,
					'post_parent' => 278,
					'orderby' => 'menu_order',
					'order' => 'ASC'
					)
				);
				
			while( $my_query->have_posts() ) : $my_query->the_post(); 
				echo '<div id="photos-' . $post->ID . '" style=padding-bottom:40px;>';
				echo '<h2>'.get_the_title().'</h2>';
				if( have_rows('gallery') ):
					while ( have_rows('gallery') ) : the_row(); ?>
						<?php 
							$image = get_sub_field('image');
							$large_image = $image['url'];
							$thumbnail = $image['sizes']['thumbnail'];
						?>
						<a href="<?php echo $large_image; ?>" data-lightbox="page-gallery" class=single-gallery-image style="background-image:url('<?php echo $thumbnail ?>');">
						</a>
					<?php endwhile;
				endif;
				echo '</div><div style=clear:both></div>';
			endwhile;
			?>
    </section><!-- close content -->
	<?php if ( get_field('sidebar') != '') { ?>
		<style>.content {float:left;width:70%;}</style>
		<aside class=sidebar>
			<?php the_field('sidebar'); ?>
		</aside>
		<div style=clear:both></div>
	<?php } ?>
<?php get_footer();?>