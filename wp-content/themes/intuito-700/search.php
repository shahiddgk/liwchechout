<?php get_header() ?>
	<div class="content">
		<article class="post-list">
			<h1 id="post-list"><?php wp_title(''); ?></h1>
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<div class="post-in-list" id="post-<?php the_ID(); ?>">
						<header class="category-post-title-section">
							<h2>
								<a href="<?php the_permalink() ?>" 
									rel="bookmark" 
									title="Permanent Link to <?php the_title_attribute(); ?>">
									<?php the_title(); ?>
								</a>
							</h2>
							<div class="post-info">
								<?php the_time('F jS, Y') ?> | 
								By: <?php the_author(); ?> | 
								Category: <?php the_category(' '); ?> | 
								<?php if ( has_tag() ) { ?>
									Tags: <?php the_tags(); ?> |
								<?php } ?>
								<?php comments_number( 'no responses', 'one response', '% responses' ); ?><br />
							</div>
						</header>
						<div class="post-content">
							<a href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) { the_post_thumbnail(); } ?>
							</a>
							<?php $ismore = @strpos( $post->post_content, '<!--more-->'); ?>
							<?php if ( $ismore != '' ) 
								{ the_content('more &raquo;'); } 
								else 
								{ echo '<p>'.get_the_excerpt().' <span style="more-link"><a href="'.get_permalink().'">more &raquo;</a></span></p>'; } ?>
						</div>
					</div>
				<?php endwhile; ?><?php else : ?>
					<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php endif; ?>
		</article>
		<?php get_sidebar(); ?>
		<div style="clear:both"></div>
	</div><!--end content -->
<?php get_footer(); ?>