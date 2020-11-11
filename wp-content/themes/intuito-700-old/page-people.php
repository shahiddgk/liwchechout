<?php 
	/* template name: People */ 
	get_header(); 
?>
</div>
	<div class=page-hero style="background-image:url('<?php the_field('hero_image'); ?>');">
		<div class=hero-title>
			<?php the_field('header_text'); ?>
		</div>
	</div>
<div class=wrapper>
	<section class="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<h1 class='page-headline'><?php the_title(); ?></h1>
    		<article class="post">
    				<?php the_content(); ?>
    			<?php endwhile; else: ?>
    				<p>Sorry, no posts matched your criteria.</p>
    			<?php endif; ?>
    		</article>
		
			<?php
			if ( have_rows('partners') ) :
				while( have_rows('partners') ) : the_row();  ?>
					<?php if (  the_sub_field('people_type') != '') { ?>
						<h2 class="people-type-label"><?php the_sub_field('people_type'); ?></h2> 
					<?php } ?>
					<?php
					if( have_rows('people') ): 
						echo "<div class=partners-list>";
							while ( have_rows('people') ) : the_row(); ?>
							<div class=single-partner>
								<div class=people-image>
									<?php if ( get_sub_field('photo') != '' ) { ?>
										<img src="<?php the_sub_field('photo'); ?>" alt="Photo of <?php the_sub_field('name'); ?>" />
									<?php } ?>
								</div>
								<div class=people-content>
									<h3>
										<?php the_sub_field('name'); ?> <span><?php the_sub_field('position'); ?></span>
									</h3>
									<div class="bio">
										<?php the_sub_field('bio'); ?>
									</div>
									<h4>What am I eating?</h4>
									<p class=partner-product-choice>
										<?php $post_object = get_sub_field('product_link');
										if ($post_object) {
											$post = $post_object;
											setup_postdata( $post );
											?>
												<a href="<?php the_permalink(); ?>" >
													<?php the_title(); ?>
												</a>
											<?php
											wp_reset_postdata();
										} ?>
										<?php if (get_sub_field('preparation') != '') { ?>
											<span class=preparation><?php the_sub_field('preparation'); ?></span>
										<?php } ?>
									</p>
								</div>
								<div style=clear:left></div>
							</div>
							<?php endwhile;
						echo "</div>";
					endif;
					  
			endwhile;
				endif;
			?>

			<?php
				if( have_rows('gallery') ):
					while ( have_rows('gallery') ) : the_row(); ?>
						<a href="<?php the_sub_field('image'); ?>" data-lightbox="page-gallery" class=single-gallery-image style="background-image:url('<?php the_sub_field('image'); ?>');">
							<img src="<?php the_sub_field('image'); ?>" style="visibility:hidden" />
						</a>
					<?php endwhile;
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