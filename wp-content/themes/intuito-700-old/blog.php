<?php  ?>
<?php get_header() ?>
	<div class="content">
		<div class="post-list">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<div class="col-4"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></div>
					<div class="post-in-list" id="post-<?php the_ID(); ?>">
						<header class="category-post-title-section">
							<h2>
								<a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
									<?php the_title(); ?>
								</a>
							</h2>
							<div class="post-info">
								<?php the_time('F jS, Y') ?> | By: <?php the_author(); ?> | Category: <?php the_category(' '); ?>  
								<?php if ( has_tag() ) { ?>
									| Tags: <?php the_tags(); ?> 
								<?php } ?>
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
								{ echo '<p>'.get_the_excerpt().' <span style="more-link"><a href="'.get_permalink().'">more &raquo;</a></span></p>'; } ?>
						</article>
					</div>
				<?php endwhile; ?><?php else : ?>
					<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php endif; ?>
		</div>
		<?php get_sidebar('home'); ?>
		<div style="clear:both"></div>
	</div><!--end content -->
<?php get_footer(); ?>

