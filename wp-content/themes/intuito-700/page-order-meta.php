<?php 
/* template name: Order Meta */ 
get_header(); 
?>
</div>
    <?php if ( get_field('hero_image') != '' ) { ?>
        <div class=page-hero style="background-image:url('<?php the_field('hero_image'); ?>');">
            <div class=hero-title>
                <?php the_field('header_text'); ?>
            </div>
        </div>
    <?php } ?>
<div class='wrapper wrapper-sidebar'>
	<h1 class='page-headline'><?php the_title(); ?></h1>
	<?php if ( !is_user_logged_in() AND is_page(5374) ) { ?>
		<div class="affiliate-login-link-responsive">
			<a href="https://lummiislandwild.com/affiliate-area/#affwp-user-pass2">Login to the affiliate program</a>
		</div>	
	<?php } ?>
	<section class="content content-with-sidebar">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<article class="post">
			<?php the_content(); ?>
			<?php endwhile; else: ?>
				<p>Sorry, no posts matched your criteria.</p>
			<?php endif; ?>
    	</article>
		<?php 
			if ( $_GET['order-meta'] ) {
				$order = wc_get_order( $_GET['order-meta'] );
				//print_r($order);
				//echo 'Club slug: '.$order->get_meta('clubs-slug');
				if ( $order->get_meta('clubs-slug') != 'no-membership') {
					echo "No membership is what tis order is";
				}
			}
		?>
    </section><!-- close content -->
	<aside class=sidebar>
		
		<?php
			if( have_rows('sidebar_products') ): ?>
				<h2 class="product-heading">Products</h2>
				<?php while ( have_rows('sidebar_products') ) : the_row();

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

						<?php
						wp_reset_postdata();
					}
				endwhile;?>
				<div class="product-button">
					<a href="/our-seafood/">View More Products</a>
				</div>

			<?php endif; ?>
	</aside>
	<div style=clear:both></div>
<?php get_footer();?>
