<?php

	$this_post_type = get_post_type();
	if ( $this_post_type == 'product' ) { 
		global $post;
		//$cat = get_the_category();
		//$cat_id = $cat[0]->cat_ID;
		//$cats = wp_get_post_categories($post->ID);
		$cats = get_the_terms($post->ID, 'category');
		print_r($cats);
	?>
		<style>
			.cat-item-<?php echo $cat_id; ?> { font-weight:bold; }
		</style>
	<?php 
	}
