<?php get_header(); ?>
	<section class="content single-post-content">
		<?php //get_sidebar(); ?>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<header class="page-title">
					<h1><?php the_title(); ?></h1>
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
				<article class="post">
				    <?php if (has_post_thumbnail()); { ?>
        				<div class="thumbnail-in-post">
        				    <?php the_post_thumbnail(); ?>
        				</div>
    				<?php } ?>
    				<?php the_content(); ?>
        			<?php endwhile; else: ?>
        				<p>Sorry, no posts matched your criteria.</p>
        			<?php endif; ?>
        		</article>
        	    <?php get_sidebar(); ?>
        <div style="clear:both"></div>
    </section><!-- close content -->
<?php get_footer(); ?>