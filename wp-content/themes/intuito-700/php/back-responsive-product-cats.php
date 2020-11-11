			<style>
				.responsive-product-categories { text-align: center; width: 100%; padding-bottom: 20px; }
				.responsive-product-categories a { display: inline-block; background: #0a2d6d; color: white; padding: 0px 10px; margin-right: 10px; text-transform: uppercase; font-size: 10pt; }
			</style>
			
		<div class="responsive-product-categories" style="display:none">
			<h2>
				Product Cateogries
			</h2>
			<?php
				$product_cats = wp_get_nav_menu_items('206');
				//print_r($product_cats);
				foreach ($product_cats as $cat) { ?>
					<a class="product-cat-item" href="<?php echo $cat->url; ?>">
						<?php echo $cat->title; ?>
					</a>
				<?php }
			?>
		</div>