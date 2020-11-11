<?php get_header(); ?>
	<?php if ( !is_shop() ) { 
		if 	(is_tax('product_cat')) {
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
	$user = wp_get_current_user();
	if ( (get_field('hide_quantity') != '') AND (!in_array( 'club-member', (array) $user->roles)) ) { ?>
		<style>
			.quantity { display:none!important; }
		</style>
	<?php } 
	if ( (get_field('hide_quantity_for_clubs') != '') AND (in_array( 'club-member', (array) $user->roles) ) ) { ?>
		<style>
			.quantity { display:none!important; }
		</style>
		<?php }?>
	<section class="content <?php if ( is_product() ) { echo 'single-product-page'; } elseif (is_tax('product_cat')) { echo 'product_cat'; } elseif ( is_shop() ) { echo 'shop_page'; }?>">
		
		<?php 
		get_sidebar();
		/*
			if (is_product()) {
				get_sidebar();
			} else {
				get_sidebar('our-seafood');	
			}
		*/
		?>
		<?php woocommerce_content(); ?>
		<div style=clear:both></div>
    </section><!-- close content -->
<?php get_footer(); ?>