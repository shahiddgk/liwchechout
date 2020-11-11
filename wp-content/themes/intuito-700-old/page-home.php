<?php /* template name:home */ get_header(); ?>

	</div><!-- Close wrapper for this page -->
	<div class=home-slideshow>
		<div class=home-video>
			<video playsinline autoplay muted loop poster="<?php bloginfo('template_directory') ?>/images/home-video-poster.jpg" id="bgvid">
					<source src="<?php bloginfo('template_directory') ?>/images/liw-home-video.webm" type="video/webm">
					<source src="<?php bloginfo('template_directory') ?>/images/liw-home-video.mp4" type="video/mp4">
					<source src="<?php bloginfo('template_directory') ?>/images/liw-home-video.ogv" type="video/ogv">
			</video>
		</div>
		<h1 class=home-title><?php the_title(); ?></h1>
		<!-- <a href="<?php/* the_field('video_link'); */?>" class="mpopup_iframe gold-button">Video</a> -->
		<a href="/why-reefnetting/" class="gold-button">Why We Reefnet</a>
	</div>
	<div class=feature>
		<div class=inner>
			<div class=left>
				<h2>
					<?php the_field('feature_title'); ?>
				</h2>
				<div class=feature_content>
					<?php the_field('feature_content'); ?>
				</div>
				<a href="<?php the_field('feature_link'); ?>" class="mpopup_iframe blue-button-patagonia">
					<span class=text>Our Story with</span> <span class=logo><img src="<?php bloginfo('template_directory') ?>/images/logo-patagonia.svg" alt='Patagonia Logo' /></span>
				</a>
			</div>
			<div class=right >
				<?php
				$image = get_field('feature_image');
				if ( !empty($image) ) { ?>
					<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
				<?php } ?>
			</div>
		</div>
	</div>
	<!-- Products -->
	<div class=wrapper>
		<div class=products-container>
			<h2 class="products-title">The Products</h2>
			<div class="inner home-products">
				<?php
					$my_query = new WP_Query( array(
						'post_type' => 'product',
						'product_cat' => 'featured-on-home-page',
						'orderby' => 'menu_order',
						'posts_per_page' => 8
					 ) );
					$count = 1;
					echo '<div class=product-row>';
					global $product; global $post;
                	while( $my_query->have_posts() ) : $my_query->the_post(); ?>
						<div class=col-4>
							<div class=product-image>
								<a href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) { the_post_thumbnail(); } ?>
								</a>
							</div>
							<div class=product-detail>
								<h3>
									<a href="<?php the_permalink(); ?>"/>
										<?php the_title(); ?>
									</a>
								</h3>
								<p>
									<span class=product-list-price><?php echo $product->get_price_html(); ?></span>
									<span class="product-list-cart <?php if (woo_in_cart($post->ID)) { echo 'in-cart'; } ?>" >
										<a href="<?php the_permalink(); ?>">
											<i class="fa fa-cart-arrow-down" aria-hidden="true"></i>
										</a>
									</span>
								</p>
							</div>
						</div>
					<?php
					if ( $count == 4 ) { echo "</div><div class=product-row>"; }
					$count = $count + 1;

					endwhile;
					echo '</div>';
				?>
				<div class=product-row>
					<?php
						if( have_rows('home_categories','option') ):

							while ( have_rows('home_categories', 'option') ) : the_row(); $image = get_sub_field('image'); ?>
							  <div class=col-4>
								<div class=product-image>
									<a href="<?php the_sub_field('link'); ?>">
										<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
									</a>
								</div>
								<div class=product-detail>
									<h3>
										<a href="<?php the_sub_field('link'); ?>"/>
											<?php the_sub_field('title'); ?>
										</a>
									</h3>
								</div>
							</div>
							<?php endwhile;
						endif;
					?>
				</div>
				<div class=clear></div>
			</div>
		</div>

		<div class=home-programs>
			<h2 class=home-programs-title>Programs</h2>
			<?php
				$my_query = new WP_Query( array(
					'post_type' => 'programs',
					'orderby' => 'menu_order',
					'posts_per_page' => 2
				 ) );
				while( $my_query->have_posts() ) : $my_query->the_post();
				if ( has_post_thumbnail() ) {
					global $post;
					$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
				}
			?>
			<div class=single-program style="background:url('<?php echo $thumb_url[0]; ?>') right bottom no-repeat; ">
				<div class="single-inner">
					<h3>
						<a href="<?php the_field('program_link') ?>">
							<?php the_title(); ?>
						</a>
					</h3>
					<?php the_content(); ?>
					<a class="program-link" href="<?php the_field('program_link') ?>">Learn More</a>
				</div>
			</div>
		<?php endwhile; ?>
		<div class=clear></div>
	</div>

<?php get_footer(); ?>
