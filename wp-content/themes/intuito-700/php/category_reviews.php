<?php
				echo "<div id=test data-tax='".$tax_id."'></div>";
				if( have_rows('category_reviews', 'product_cat_'.$tax_id) ):
					echo "<div class='category-comments-container'>";
					while ( have_rows('category_reviews', 'product_cat_'.$tax_id) ) : the_row();
						echo "<div class='single-comment'>"; 
								$product_post_id = get_sub_field('more_link'); 
								if ( $product_post_id ) { ?>
									<div class='comment-product'>
										<a style="color:#002e6d" href="<?php echo get_permalink($product_post_id); ?>" title="Check out this product"><?php echo get_the_title($product_post_id); ?></a>
									</div>
							<?php } 
							the_sub_field('review_text');
							echo " ~ <strong>".get_sub_field('reviewer_name')."</strong>";
							$stars = get_bloginfo('template_directory') . "/images/five-stars.png";
							echo "<img src='" .$stars. "' />";
						echo "</div>";
					endwhile;
					echo "</div>";
				endif;

?>
<style>
	.single-comment {
		position: relative;
	}
	.comment-product { 
		font-size: 10pt;
		text-transform: uppercase;
		padding-bottom:7px;
		/*
		text-align: right;
		padding-top:10px; 
		padding-right: 6%;
		position:absolute;
		bottom:0;
		right:0;
		*/
	}
</style>