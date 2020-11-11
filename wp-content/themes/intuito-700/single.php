<?php get_header(); ?>
<style>
	body { background-image:url('<?php bloginfo('template_directory'); ?>/images/news_bg.jpg'); background-size:cover; background-attachment:fixed; }
	.total-wrap { background:transparent; }
</style>
	<section class="content single-post-content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<?php 
			if ( get_field('hero_image') != '' ) { ?>
				<div class="post-hero">
					<div class="post-hero-header">
						<?php previous_post_link('<div class="prev-post">%link</div>','<i class="fa fa-chevron-left" aria-hidden="true"></i>'); ?>
						<div class="post-title-preview"><?php the_title(); ?></div>
						<?php previous_post_link('<div class="next-post">%link</div>','<i class="fa fa-chevron-right" aria-hidden="true"></i>'); ?>
					</div>
					<img src="<?php the_field('hero_image'); ?>" />
					<?php if ( get_field('header_text') ) { ?>
						<div class="post-hero-text" style="display:none">
							<?php the_field('header_text'); ?>	
						</div>
					<?php } ?>
				</div>		
			<?php } ?>
		<?php //get_sidebar(); ?>
				<header class="post-page-title">
					<h1 class=post-title><?php the_title(); ?></h1>
					<div class="post-info">
						<?php the_time('F jS, Y') ?>
						<?php if (have_comments()) { ?>
						    | <?php comments_number( 'no responses', 'one response', '% responses' ); ?>
						<?php } ?>
					</div>
				</header>
				<article class="post">
				    <?php if (has_post_thumbnail()); { ?>
        				<div class="thumbnail-in-post">
        				    <?php the_post_thumbnail(); ?>
        				</div>
    				<?php } ?>
    				<?php the_content(); ?>
					<?php 
						$post_objects = get_field('post_related_products');
						if ( $post_objects ) {
							echo "<div class='post-related-products'>"; 
							echo "<h2>Products related to this post</h2>";
							foreach( $post_objects as $post) {
								setup_postdata($post) ?>
								<div class="post-related-product">
									<?php if ( has_post_thumbnail() ) { ?>
										<div class="related-thumb">
											<?php the_post_thumbnail('medium'); ?> 
										</div>
								<?php } ?>
									<div class="related-info">
										<h3>
											<a href="<?php the_permalink(); ?>">oooo<?php the_title(); ?></a>	
										</h3>
										<span style="color:#c5c0c0"><?php echo get_the_excerpt(); ?></span>
									</div>
									<div style="clear:left"></div>
								</div>
								<?php 
							}
							echo "</div>";
						}
						wp_reset_postdata();
					?>
        			<?php endwhile; else: ?>
        				<p>Sorry, no posts matched your criteria.</p>
        			<?php endif; ?>
        		</article>
        <div style="clear:both"></div>
    </section><!-- close content -->
<?php get_footer(); ?>