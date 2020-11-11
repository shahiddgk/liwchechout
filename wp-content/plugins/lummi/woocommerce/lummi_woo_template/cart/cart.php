<?php
/**
 * Cart Page
 * 
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$club_user = \LW\Settings::currentUser();
$is_closed = \LW\Settings::is_closed_club();
$is_chefs = intval($club_user["club_term_meta"]["chefs_active"]);
$chefs_percent = $club_user["club_term_meta"]["chefs_prc"];
$prc_to_cart_total = 0;
$price_title = 'Price';
$is_private = false;
wc_print_notices();
if ( !$club_user OR ( $club_user['user_meta']['user_club'] != '37' ) ) { ?>
	<style>
		.woocommerce-shipping-destination { display:none; }
	</style>
<?php } 

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<table class="shop_table shop_table_responsive cart" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">&nbsp;</th>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>

			<?php if( is_user_logged_in() && ! $club_user["club_term_meta"]["default_club"] && ! $is_closed) :
				$price_title = 'Your Price';
                ?>
				<th class="product-discount"><?php _e( 'Regular Price', 'woocommerce' ); ?></th>
			<?php endif; ?>
			<th class="product-price"><?php _e( $price_title, 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<td class="product-remove">
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
								'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
								esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
								__( 'Remove this item', 'woocommerce' ),
								esc_attr( $product_id ),
								esc_attr( $_product->get_sku() )
							), $cart_item_key );
						?>
					</td>

					<td class="product-thumbnail">
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $product_permalink ) {
								echo $thumbnail;
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
							}
						?>
					</td>

					<td class="product-name" data-title="<?php _e( 'Product', 'woocommerce' ); ?>">
						<?php
							if ( ! $product_permalink ) {
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
							} else {
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_title() ), $cart_item, $cart_item_key );
							}

							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

							// Backorder notification
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
							}
						?>
					</td>
					
						<?php
						

//						if( is_user_logged_in() && ! $club_user["club_term_meta"]["default_club"] && ! $is_closed){
//							echo '<td class="product-percent-discount" >';
//							if($is_chefs){
//								echo $club_user["club_term_meta"]["clubs_discount_p"] + $chefs_percent . '%';
//                            }else{
//								echo $club_user["club_term_meta"]["clubs_discount_p"] . '%';
//                            }
//							echo '</td>';
//						}

						?>
					
						<?php

						if( is_user_logged_in() && ! $club_user["club_term_meta"]["default_club"] && ! $is_closed){
							$col_span = '8';
							$is_private = true;
							if($_product->variation_id){

								$product_variation = wc_get_product($_product->variation_id);

								$total_price = $product_variation->get_regular_price() * $cart_item['quantity'];
								$regular_price = $product_variation->get_regular_price();

							}else{
								$total_price = $_product->get_regular_price() * $cart_item['quantity'];
								$regular_price = $_product->get_regular_price();
							}


							if ( $is_chefs ){
								$club_percent  = $club_user["club_term_meta"]["clubs_discount_p"] + $chefs_percent;
                            }else{
								$club_percent  = $club_user["club_term_meta"]["clubs_discount_p"];
                            }


							$cur_prefix = get_woocommerce_currency_symbol();
							$price_discount =  ($club_percent / 100) * $total_price;
							$prc_to_cart_total += $price_discount;
							global $club_discount;
							
							if ( $club_user['user_meta']['user_club'] != '201') { 
								$club_discount = '<p class="club_discount_notice">Your Bonus '.$club_percent.'% Club Discount Saved You '.$cur_prefix . number_format($prc_to_cart_total,2,'.','').'</p>';
							}
							echo '<td class="product-discount">';
									echo $cur_prefix . number_format($regular_price,2,'.','');
							echo '</td>';

						}
						?>
					<td class="product-price" data-title="<?php _e( 'Price', 'woocommerce' ); ?>">
						<?php
							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
					</td>

					<td class="product-quantity" data-title="<?php _e( 'Quantity', 'woocommerce' ); ?>">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
						?>
					</td>

					<td class="product-subtotal" data-title="<?php _e( 'Total', 'woocommerce' ); ?>">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>
		<tr>
			<td colspan="<?php echo ( $col_span ) ? $col_span : '6'; ?>" class="actions">

				<?php if ( wc_coupons_enabled() ) { ?>
					<div class="coupon">

						<label for="coupon_code"><?php _e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply Coupon', 'woocommerce' ); ?>" />

						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php } ?>

				<input type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update Cart', 'woocommerce' ); ?>" />

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
			</td>
		</tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table>
<div style="display:none">
	<?php print_r($club_user); ?>	
</div>
<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<div class="cart-collaterals">

	<?php do_action( 'woocommerce_cart_collaterals' ); ?>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>

<?php
    if($is_private){
        echo '<script type="text/javascript">
                 jQuery(\'[data-title="Shipping"]:contains("Club Method:")\').each(function(){jQuery(this).html(jQuery(this).html().split("Club Method:").join(""));});
              </script>';
    }
?>
