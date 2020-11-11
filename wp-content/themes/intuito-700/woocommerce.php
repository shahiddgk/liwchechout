<?php 
	get_header();
	$user = wp_get_current_user();
?>

	<?php 
	if ( !is_shop() ) { 
		if (is_tax('product_cat')) {
			$tax_id = get_queried_object()->term_id;
			$term = get_term($tax_id,'product_cat');
			$title = $term->name;
			$image_url = get_field('hero_image', 'product_cat_'.$tax_id);
			if (empty($image_url)) {
				$image_url = get_field('default_page_hero','option');
			} ?>
			</div>
				<div class=page-hero style="background-image:url('<?php echo $image_url; ?>');">
					<h1 class=page-title>
						<?php echo $title; ?>
					</h1>
				</div>
			<div class='wrapper wrapper-product-page'>
			<style>.wrapper-product-page .page-title {display:none;}</style>
		<?php }
	}

	?>
				
	<section class="content <?php if ( is_product() ) { echo 'single-product-page'; } elseif (is_tax('product_cat')) { echo 'product_cat'; } elseif ( is_shop() ) { echo 'shop_page'; }?>" data-user-club="<?php echo $user_club; ?>" >
		
		<?php
			// Reviews at the top of category pages
			if ( is_tax('product_cat') ) { 
				include 'php/category_reviews.php';
			}
		?>

		<?php 
			// Sale banner
			$sale = get_field('sale_banner'); 
			if ( $sale[0] == 'yes') {?>     
				<div class="sale">Sale</div>
			<?php } 
		?>

		<?php 
			get_sidebar();
		?>
			
		<?php if (get_field('badge') != '') { // jQuery moves this into the .summary div from js/jquery-functions.js ?>
			<style>
				.product-badge {position:absolute; right:-16px; top:-20px; height:65px; }
				.product-badge img { max-height:100%; }
				.wrapper .single-product-page .product .summary h1.product_title { padding-right:90px; }
				@media screen and (max-width: 1100px) {
					.product-badge { top:0; }
				}
				@media screen and (max-width: 675px) {
					.summary.entry-summary { padding-top:20px; }
					.product-badge { top:20px; }
				}
			</style>
			<div class="product-badge"><img src="<?php the_field('badge'); ?>" alt="Badge"/></div>
		<?php } ?>
		<?php include 'php/guarantee.php'; ?>
		<?php if ( 1 == 2 ) : ?>
			<div class="guarantee">
				<img src="<?php bloginfo('template_directory'); ?>/images/guarantee.png" />
				<div class="guarantee-popup">
				   <?php the_field('guarantee_text','option'); ?>
				</div>
			</div>
		<?php endif; ?>
		<?php  woocommerce_content(); ?>
		<?php
			// Reviews at the top of category pages
			if ( is_tax('product_cat') ) { 
				include 'php/category_description.php';
				include 'php/category_out_of_stock.php';
			}
		?>
		<div style=clear:both></div>
		
    </section><!-- close content -->
                            
<?php get_footer(); ?>