<?php
	/* Template name: Partners */
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

			<div class=major-partners-list>
				<h2>Partners</h2>
				<?php
					if( have_rows('partners') ):
						while ( have_rows('partners') ) : the_row(); ?>
							<div class=single-major-partner>
								<div class=left>
									<img src="<?php $image = get_sub_field('partner_image'); echo $image['url']; ?>" alt="<?php $image['alt']; ?>" />
								</div>
								<div class=right>
									<h3><?php the_sub_field('partner_name'); ?> <span><?php the_sub_field('partner_location'); ?></span></h3>
									<div class=partner-description>
										<?php the_sub_field('description'); ?>
									</div>
									<?php if ( get_sub_field('website_link') ) { ?>
										<a href="<?php the_sub_field('website_link'); ?>" class="gold-button partner-button" target=_blank>Visit Site</a>
									<?php } ?>
									<?php if ( get_sub_field('video_link') ) { ?>
										<a href="<?php the_sub_field('video_link'); ?>" class="blue-button partner-button" target=_blank>Watch Video</a>
									<?php } ?>
								</div>
								<div class=clear></div>
							</div>
						<?php endwhile;
					endif;
				?>
				<div class=clear></div>
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
