<?php
	/* Template name: Tip-Recipe */
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

			<div class=tips-recipe-container>
				<?php
					if( have_rows('single_recipe_and_tip') ):
						while ( have_rows('single_recipe_and_tip') ) : the_row(); ?>
							<div class=single-recipe-tip>
								<div class=left>
									<a href="<?php $image = get_sub_field('food_image_lg'); echo $image['url']; ?>" class="mpopup"><img src="<?php $image = get_sub_field('food_image'); echo $image['url']; ?>" alt="<?php $image['alt']; ?>" /></a>
								</div>
								<div class=right>
									<div class=food-description>
										<?php the_sub_field('food_description'); ?>
									</div>
									<div class="tip">
										<div class="tip-author-image"><img src="<?php $image = get_sub_field('tip_author_image'); echo $image['url']; ?>" alt="<?php $image['alt']; ?>" /></div>
										<div class="tip-content-container">
											<h4><span>Tip - </span><?php the_sub_field('author_name'); ?></h4>
											<p class="author-title"><?php the_sub_field('author_title');?></p>
											<div class="tip-content"><?php the_sub_field('tip_content'); ?></div>
										</div>

									</div>
								</div>
							</div>
						<?php endwhile;
					endif;
				?>
			</div>

    </section><!-- close content -->
	<?php if ( get_field('sidebar') != '') { ?>
		<style>.content {float:left;width:70%;}</style>
		<aside class=sidebar>
			<?php the_field('sidebar'); ?>
		</aside>
		<div style=clear:both></div>
	<?php } ?>
<?php get_footer();?>
