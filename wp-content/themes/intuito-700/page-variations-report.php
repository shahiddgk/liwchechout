<?php 
/* Template name: Variations Report */
get_header(); ?>
</div>
    <?php if ( get_field('hero_image') != '' ) { ?>
        <div class=page-hero style="background-image:url('<?php the_field('hero_image'); ?>');">
            <div class=hero-title>
                <?php the_field('header_text'); ?>
            </div>
        </div>
    <?php } ?>
<style>
	.page-headline { padding-top:220px!important; }
	.starthing { font-weight:bold; font-family:'fontawesome'; }
	.starthing:after { content:"\f005"; display:block; width:100px; height:100px;  }
</style>
<div class='wrapper wrapper-sidebar'>
	<h1 class='page-headline' style="padding-top:40px!important"><?php the_title(); ?></h1>
	
	<section class="content content-with-sidebar">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<article class="post">
			<?php the_content(); ?>
			<?php endwhile; else: ?>
				<p>Sorry, no posts matched your criteria.</p>
			<?php endif; ?>
<style>
	.wrapper-sidebar .content-with-sidebar h3 { font-size:12pt; margin-top:16px; }
	.wrapper-sidebar .content-with-sidebar h3 a { color:#514b46; }
	.wrapper-sidebar .content-with-sidebar h3 a:hover { text-decoration:underline; }
</style>
<?php // Test area
	if ( current_user_can('administrator') ) {
		$product = wc_get_product(1986);
		$products = wc_get_products(array('status' => 'published'));
		//print_r($products);
		//print_r($product);
		echo '<p>Note: variations with no stock are not listed</p>';
		foreach ($products as $product) {
			echo '<h3><a href="https://lummiislandwild.com/wp-admin/post.php?post='.$product->id.'&action=edit" target="_blank">' . $product->name . '</a></h3>';
			if ( $product->is_type( 'variable' ) ) {
				echo "<span style=font-size:10pt;font-style:italic>Variable product</span>";
				//$variations = $product->get_variation_attributes();
				$variations = $product->get_available_variations();
				if ( $variations ) { ?>
						<table>
					<?php foreach ($variations as $key => $value) {
						$count=0;
						foreach ($value['attributes'] as $at) {
							if ( $count == 0 ) {
								$attribute_name = $at;
							}
							$count++;
						}
						echo '<div style="margin:0">';
						//echo $attribute_name . ", <strong>" . $value['max_qty'] . "</strong>";
						//echo "In stock: " . $value['availability_html'];
						//echo "<br />Available Stock: " . $value['max_qty'];
						//echo  $scs_wc_size. ' ' . $value['price_html'];
						?>
							<tr>
								<td><?php echo $attribute_name; ?></td>
								<td><strong><?php echo $value['max_qty']; ?></strong></td>
							</tr>
						<?php
					}
						echo '</table>';
					//print_r($variations);
					//print_r($value['attributes'][0]);
				} else {
					echo "<div>All variations are out of stock</div>";
				}
				
			} else {
				echo "<div style=font-size:10pt;font-style:italic;>Regular Product</div>";
				echo "Stock: <strong>" . $product->stock_quantity . "</strong>"; 
				//print_r($product);
			}
		}
	} else {
		echo "<h3>You must log in to view this page</h3>";
	}
?>
		
		
    </section><!-- close content -->

	<div style=clear:both></div>
<?php get_footer();?>
