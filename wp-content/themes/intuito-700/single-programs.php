<?php get_header(); ?>
</div>
	<div class=page-hero style="background-image:url('<?php the_field('hero_image'); ?>');">
		<h1 class=page-title>
			<?php the_title(); ?>
		</h1>
	</div>
<div class='wrapper wrapper-sidebar'>
	<h2 class='page-headline'><?php the_title(); ?></h2>

	<section class="content content-with-sidebar">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    	<article class="post">
			<?php the_content(); ?>
			<?php endwhile; else: ?>
				<p>Sorry, no posts matched your criteria.</p>
			<?php endif; ?>
    	</article>
    </section><!-- close content -->
	<aside class=sidebar>
		<?php
			if( have_rows('sidebar_products') ):
				while ( have_rows('sidebar_products') ) : the_row();

					$po = get_sub_field('product');
					if ( $po ) {
						$post = $po;
						$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
						setup_postdata($post); ?>

							<div class=product-image>
								<a href="<?php the_permalink(); ?>">
									<img src="<?php echo $thumb_url[0]; ?>" />
								</a>
							</div>
							<div class=product-detail>
								<h3>
									<a href="<?php the_permalink(); ?>"/>
										ooo<?php the_title(); ?>--000
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

						<?php
						wp_reset_postdata();
					}

				endwhile;
			endif;
		?>
	</aside>
	<div style=clear:both></div>
<?php get_footer();?>