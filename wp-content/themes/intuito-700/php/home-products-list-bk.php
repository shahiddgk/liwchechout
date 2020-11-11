<!-- Products -->
<div class=wrapper>
    <div class=products-container>
        <h2 class="products-title">From the Sea to your Table</h2>
        <div class="featured-description">
            Healthy eaters and seafood lovers, enjoy fresh seafood from the wild West Coast, delivered straight to your door.
        </div>
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

			<div style=display:none>
				<?php if( have_rows('categories') ): ?>
					<?php while( have_rows('categories') ): the_row(); ?>

						<?php 
							$cats = get_sub_field('category');
							echo $cats->slug; 
						?>

					<?php endwhile; ?>
				<?php endif; ?>
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
								'terms' => 'seafood-gifts'
							),
							$tax_club
						)
					)
				);
			?>
			<!-- Product category row -->
            <div class=product-row>
			<?php 
	            while ($my_query->have_posts()) : $my_query->the_post();          
                $product = wc_get_product($my_query->post->ID);
                ?>
				<div style="display:none"><?php 
					//print_r($product); 
					echo $product->get_stock_status();
					?></div>
                <div class=col-4>
                    <div class=product-image>
                        <a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail();} ?></a>
                    </div>
                    <div class=product-detail>
                        <h3>
                            <a href="<?php the_permalink(); ?>"/>
    						ooo1	<?php the_title(); ?>
                            </a>
                        </h3>
                    </div>
                </div>
    		<?php
			endwhile;
			wp_reset_postdata();
			?>
            <div class=col-4>
                <div class="home-cat-link" style="background-image: url('<?php echo home_url(); ?>/wp-content/uploads/2019/12/gifts-cat.jpg');">
                    <a href="<?php echo home_url(); ?>/product-category/seafood-gifts/">
						<span>explore</span><br />Seafood Gifts
					</a>
                </div>
            </div>
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
								'terms' => 'wild-salmon'
							),
							$tax_club
						)
					)
				);
			?>
			<!-- Product category row -->
            <div class=product-row>
			<?php 
	            while ($my_query->have_posts()) : $my_query->the_post();          
                $product = wc_get_product($my_query->post->ID);
                ?>
				<div style="display:none"><?php 
					//print_r($product); 
					echo $product->get_stock_status();
					?></div>
                <div class=col-4>
                    <div class=product-image>
                        <a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail();} ?></a>
                    </div>
                    <div class=product-detail>
                        <h3>
                            <a href="<?php the_permalink(); ?>"/>
    						ooo2	<?php the_title(); ?>
                            </a>
                        </h3>
                    </div>
                </div>
    		<?php
			endwhile;
			wp_reset_postdata();
			?>
            <div class=col-4>
                <div class="home-cat-link" style="background-image: url('<?php echo home_url(); ?>/wp-content/uploads/2017/11/wild-salmon-cat.jpg');">
                    <a href="<?php echo home_url(); ?>/product-category/wild-salmon/">
						<span>explore</span><br />Wild Salmon
					</a>
                </div>
            </div>
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
						'terms' => 'non-perishable'
					),
					$tax_club
				)
			)
        );
		?>
		<!-- Product category row -->
        <div class="product-row home-non-perishable">
        <?php 
			while ($my_query->have_posts()) : $my_query->the_post();
        	$product = wc_get_product($my_query->post->ID);
            ?>
            <div class=col-4>
                <div class=product-image>
                    <a href="<?php the_permalink(); ?>">
                        <div class="home-sale"><img src="<?php bloginfo('template_directory'); ?>/images/sale.png" /></div>
							<?php if (has_post_thumbnail()) {
								the_post_thumbnail();
							} ?>
                    </a>
                </div>
                <div class=product-detail>
                    <h3>
                        <a href="<?php the_permalink(); ?>"/>
    					ooo3	<?php the_title(); ?>
                        </a>
                    </h3>
                </div>
            </div>
    <?php
endwhile;
wp_reset_postdata();
?>
        <div class=col-4>
            <div class="home-cat-link" style="background-image: url('<?php echo home_url(); ?>/wp-content/uploads/2017/11/non-perishable-cat.jpg');">
                <a href="<?php echo home_url(); ?>/product-category/non-perishable/">
					<span>explore</span><br />Non-Perishable
				</a>
            </div>
        </div>
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
					'terms' => 'tuna-halibut-black-cod'
				),
				$tax_club
			)
		)
    );
	?>	
	<!-- Product category row -->
    <div class="product-row home-perishable">
	<?php     
		while ($my_query->have_posts()) : $my_query->the_post();
		$product = wc_get_product($my_query->post->ID);
        ?>
        <div class=col-4>
            <div class=product-image>
                <a href="<?php the_permalink(); ?>">
					<?php if (has_post_thumbnail()) {
						the_post_thumbnail();
					} ?>
                </a>
            </div>
            <div class=product-detail>
                <h3>
                    <a href="<?php the_permalink(); ?>"/>
        		ooo4	<?php the_title(); ?>
                    </a>
                </h3>
            </div>
        </div>
    <?php
endwhile;
wp_reset_postdata();
?>
    <div class=col-4>
        <div class=home-cat-link style="background-image: url('<?php echo home_url(); ?>/wp-content/uploads/2017/11/tuna-cat.jpg');">
            <a href="<?php echo home_url(); ?>/product-category/tuna-halibut-black-cod/">
				<span>explore</span><br />Tuna, Halibut, Cod</a>
        </div>
    </div>
</div>



<div class=clear></div>
</div>
</div>