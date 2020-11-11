<?php get_header();
?>
<div class=page-hero style="background-image:url('<?php the_field('hero_image'); ?>');display:none;">
			</div>
	<section class="content club-home">
		<?php show_wccart(); ?>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    		<article class="post club-home-left" <?php if ( !getManagerData() ) { echo 'style="float:none;width:100%"'; } ?> >
					<h1>My Account</h1>
					<?php //echo $_SERVER['PHP_SELF']; ?>
    				<?php the_content(); ?>
    			<?php endwhile; else: ?>
    				<p>Sorry, no posts matched your criteria.</p>
    			<?php endif; ?>
    		</article>
		
			<?php if ( getManagerData() ) { ?>
			<div class="club-home-right">
				<div class="fb-page" 
					 data-href="<?php echo getManagerData(); ?>" 
					 data-tabs="timeline" 
					 data-width="800"
					 data-small-header="false" 
					 data-adapt-container-width="true" 
					 data-hide-cover="false" 
					 data-show-facepile="true">
					<blockquote cite="<?php echo getManagerData(); ?>" class="fb-xfbml-parse-ignore">
						<a href="<?php echo getManagerData(); ?>"></a>
					</blockquote>
				</div>
				
			</div>
			<?php } ?>
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