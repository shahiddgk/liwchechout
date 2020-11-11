<?php
	$terms = get_terms( array(
		'taxonomy' => 'category',
		'hide_empty' => false,
		'exclude' => '1,138',
	) );
	global $wp;
	//echo home_url( $wp->request );
	$url = $wp->request;
	$cat_url = end(explode('/', $url));
?>
<div class="category-links">
	<ul>
		<li><a href="<?php bloginfo('url'); ?>/community" <?php echo $url == 'community' ? "style='text-decoration:underline'" : ''; ?>>All</a></li>
		<?php 
			foreach ( $terms as $cat ) { ?>
			<li><a href="<?php bloginfo('url'); ?>/category/<?php echo $cat->slug; ?>" <?php echo $cat_url == $cat->slug ? "style='text-decoration:underline'" : ''; ?> ><?php echo $cat->name; ?></a></li>
			<?php }
		?>
	</ul>
</div>