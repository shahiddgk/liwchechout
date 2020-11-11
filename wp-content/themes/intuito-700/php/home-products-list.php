<!-- Products -->
<style>
.as_add_to_cart_btn{
	float: right;
    position: relative;
    bottom: 24px;
    width: 110px;
    background-color: red;
    padding: 0px 10px;
}
}
</style>
<div class=wrapper>
    <div class=products-container>
        <h2 class="products-title"><?php the_field('home_page_categories_title'); ?></h2>
		<?php if ( get_field('home_page_categories_description') != '' ) { ?>
			<div class="featured-description">
				<?php the_field('home_page_categories_description'); ?>
			</div>
		<?php } ?>
        <div class="inner home-products">
        <?php
            // Get the current user data
            $user = \LW\Settings::currentUser();
            // Get club status - open or close
            $is_closed_club = \LW\Settings::is_closed_club();
            // Get ids - for all clubs
            $clubs = get_terms(array('taxonomy' => 'clubs', 'fields' => 'ids'));
            // Find default club ID
            $club_id = null;
            foreach ($clubs as $ids) {
                if (get_term_meta($ids, 'default_club', true)) {
                    $club_id = $ids;
                    break;
                }
            }
            // Check the status of the current user club
            if (is_user_logged_in() && !$is_closed_club && !$user["club_term_meta"]["default_club"]) {
                $club_id = $user["club_taxonomy"]->term_id;
            }
			
			if ( current_user_can('administrator') ) {
				$club_id = '37';
			}

			// Add to WP_Query - in key 'tax_query'
            $tax_club = array(
                'taxonomy' => 'clubs',
                'field' => 'term_id',
                'terms' => $club_id,
                'operator' => 'IN'
            );
            
			?>


				<?php if( have_rows('categories') ): ?>
					<?php while( have_rows('categories') ): the_row(); ?>

						<?php 
							$cats = get_sub_field('category');
						?>
						<div style="display:none">
							<?php print_r($cats); ?>	
						</div>


			
		<?php
				$my_query = new WP_Query(
						array(
						'post_type' => 'product',
						'orderby' => 'menu_order',
						'order' => 'ASC',
						'status' => 'publish',
						'posts_per_page' => 3,
						'meta_query' => array (
							array(
								'key' => '_stock_status',
								'value' => 'instock'
							)
						),
						'tax_query' => array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'product_cat',
								'field' => 'slug',
								'terms' => $cats->slug
							),
							$tax_club
						)
					)
				);
			?>
			<!-- Product category row -->
            <div class=product-row>
				<div class="hp-products-title-responsive"><span>explore</span><br /><?php echo $cats->name; ?></div>
			<?php 
	            while ($my_query->have_posts()) : $my_query->the_post();          
				$product = wc_get_product($my_query->post->ID);
				
				// echo '<pre>';
				// print_r($product);
				// echo '</pre>';

				$thumbnail_id = get_woocommerce_term_meta( $cats->term_id, 'thumbnail_id', true );
			    $image = wp_get_attachment_url( $thumbnail_id ); 
                ?>
				<div style="display:none"><?php 
					//print_r($product); 

					echo $product->get_stock_status();
					?></div>
                <div class=col-4>
                    <div class=product-image>
                        <a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail('large');} ?></a>
						<a href="javascript:void(0);" onclick="as_add_product_to_cart(<?=$product->id?>);" class="as_add_to_cart_btn">Add to Cart</a>
                    </div>
                    <div class=product-detail>
                        <h3>
                            <a href="<?php the_permalink(); ?>"/>
    							<?php the_title(); ?>
                            </a>
                        </h3>
                    </div>
                </div>
    		<?php
			endwhile;
			wp_reset_postdata();
			?>
			<div class="hp-responsive-more">
				<a href="<?php echo home_url(); ?>/product-category/<?php echo $cats->slug; ?>/">More <?php echo $cats->name; ?></a>
			</div>
            <div class="col-4 desktop-view-explore">
                <div class="home-cat-link" style="background-image: url('<?php echo $image; ?>');">
                    <a href="<?php echo home_url(); ?>/product-category/<?php echo $cats->slug; ?>/">
						<span>explore</span><br /><?php echo $cats->name; ?>
					</a>
                </div>
            </div>
        </div>
			
			
		<?php endwhile; ?>
	<?php endif; ?>


<div class=clear></div>
</div>
</div>