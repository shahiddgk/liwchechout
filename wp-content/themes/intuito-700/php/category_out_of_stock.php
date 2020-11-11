<style>
	.out-of-stock-container { margin-bottom:50px; }
	.out-of-stock-container a { font-weight:bold; text-align:center; }
	.out-of-stock-top { border-top:1px solid #eee; padding-top:20px; text-align:center; }
	.out-of-stock-top { margin-bottom:20px; color:#002e6d; } 
	@media screen and (max-width: 1100px) { 
		.out-of-stock-container { margin-left:0; width:auto;
	}
	
</style>

<?php

$query = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
	'product_cat' => $term->slug,
    'meta_query' => array(
        array(
            'key' => '_stock_status',
            'value' => 'outofstock'
        )
        //array(
        //    'key' => '_backorders',
        //    'value' => 'no'
        //),
    )
);

$wp_query = new WP_Query($query);
if ($wp_query->have_posts()) : ?>
	<div class="out-of-stock-container">
		<div class="out-of-stock-top">
			<h3>
				Out of stock items for this category 
			</h3>
			<p>
				Unfortunately some of our items are out of stock, but the good news is that you can get notified when we get them restocked. 
				Click through to a product page and sign up! 
			</p>
		</div>
		<ul class="products" style="width:100%">	
		<?php
		while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
			<li class="product">
				<a href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail() ) { the_post_thumbnail(); } ?>
					<?php the_title(); ?>
				</a>
			</li>
		<?php endwhile; 
 		?></ul>
	</div>
	<?php
endif;
?>
