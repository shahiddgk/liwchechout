<?php 
	/* Template name: Place Page */
	get_header();
?>
	</div>
		<div class=place-map>
	 		<iframe src="https://www.google.com/maps/d/embed?mid=1XQJhVKvMRk65M_HMvN-lqzynfHE" style="height:100%;width:100%;"></iframe>
		</div>
	<div class=wrapper style=display:none>
		<section class="content">
			<h1><?php the_title(); ?></h1>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<article class="post">
						<?php the_content(); ?>
					<?php endwhile; else: ?>
						<p>Sorry, no posts matched your criteria.</p>
					<?php endif; ?>
				</article>
				<div class=col-3-container>
					<?php
						if( have_rows('places') ):
							$count = 0; 
							echo "<div class=place-row>";
							while ( have_rows('places') ) : the_row(); 
								?>
								<div class=col-3>
									<div class=place-image>
										<img src="<?php 
											 $image = get_sub_field('image');
											 echo $image['url']; ?>" 
											 alt = "<?php echo $image['alt']; ?>"	
											 />
									</div>
									<h3><?php the_sub_field('place_name'); ?></h3>
									<h4><?php the_sub_field('fish'); ?></h4>
									<p><?php the_sub_field('description'); ?></p>
								</div>
								<?php 
									$count = $count + 1;
									if ( $count % 3 == 0 ) {
										echo "<div class=clear></div></div><div class=place-row>";
									} 
							endwhile;
							echo "</div>";
						endif;
					?>
					<div class=clear></div>
				</div>
				
		</section><!-- close content -->
<?php get_footer();?>