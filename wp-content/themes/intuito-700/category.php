<?php get_header() ?>
<style>
	body { background-image:url('<?php bloginfo('template_directory'); ?>/images/news_bg.jpg'); background-size:cover; background-attachment:fixed; }
	.total-wrap { background:transparent; }
</style>
	<div class="content news-page">
		<div class="post-list">
			
			<h1 class="post-list-title">Community<?php //wp_title(''); ?></h1>
			
			<?php include 'php/category_menu.php'; ?>
			
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<div class="post-in-list" id="post-<?php the_ID(); ?>">
						
						<div class="post-preview-image">
							<img src="<?php the_field('hero_image'); ?>" />
						</div>
						<header class="category-post-title-section">
							<h2>
								<a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
									<?php the_title(); ?>
								</a>
							</h2>
							<div class="post-info">
								<?php the_time('F jS, Y') ?>
								<?php if (have_comments()) { ?>
								    | <?php comments_number( 'no responses', 'one response', '% responses' ); ?>
								<?php } ?>
							</div>
						</header>
						<article class="post-content">
							<?php if ( has_post_thumbnail() ) { ?>
								<div class="post-thumbnail">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail(); ?>
									</a>
								</div>
							<?php } ?>
							<?php $ismore = @strpos( $post->post_content, '<!--more-->'); ?>
							<?php if ( $ismore != '' ) 
								{ the_content('more &raquo;'); } 
								else 
								{ echo '<p>'.get_the_excerpt().' <span class="more-link"><a href="'.get_permalink().'">more &raquo;</a></span></p>'; } ?>
						</article>
					</div>
				<?php endwhile; ?><?php else : ?>
					<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php endif; ?>
			
		</div>
		<?php //get_sidebar('home'); ?>
		<div style="clear:both"></div>
	</div><!--end content -->
<?php get_footer(); ?>

