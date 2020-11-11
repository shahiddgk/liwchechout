	</div><!--End Wrapper-->
	<div class=pre-footer>
		<?php include ('mailchimp.php'); ?>
	</div>
    <div class="footer">
		<div class=footer-top>

				<?php
					if( have_rows('social_links','option') ):
						echo '<ul class=social-links>';
						while ( have_rows('social_links','option') ) : the_row(); ?>
							<li>
								<a href="<?php the_sub_field('link'); ?>" title="<?php the_sub_field('name'); ?>" target="_blank">
									<i class="<?php the_sub_field('icon'); ?>" aria-hidden="true"></i>
								</a>
							</li>
						<?php endwhile;
						echo '</ul>';
					endif;
				?>

		</div>
		<div class="footer-middle">
			<a href="https://lummiislandwild.com/lummi-island-wild-guarantee" target=_blank>Read Our Guarantee</a><br />
			<img src="<?php bloginfo('template_directory'); ?>/images/cards.png" alt="Credit cards" />
		</div>
		<div class=footer-bottom>
			<div class=inner>
				<div class=footer-logo>
					<a href="<?php bloginfo('url'); ?>">
						<img src="<?php bloginfo('template_url'); ?>/images/logo-footer.png" alt="Lummi Island Wild Logo" />
					</a>
				</div>

				<div class=footer-col>
					<h4>Account</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_six',
							'container' => false,
							'menu_id' => 'footer_six'
						));
					?>

					<a href="/buyers-club/" class="gold-button">Buyers Club</a>

				</div>

				<div class=footer-col>
					<h4>Our Seafood</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_five',
							'container' => false,
							'menu_id' => 'footer_five'
						));
					?>
				</div>

				<div class=footer-col>
					<h4>The Product</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_four',
							'container' => false,
							'menu_id' => 'footer_four'
						));
					?>

					<h4>The Purpose</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_three',
							'container' => false,
							'menu_id' => 'footer_three'
						));
					?>

				</div><!-- Footer Col End -->

				<div class=footer-col>
					<h4>The People</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_one',
							'container' => false,
							'menu_id' => 'footer_one'
						));
					?>

					<h4>The Place</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_two',
							'container' => false,
							'menu_id' => 'footer_two'
						));
					?>
				</div><!-- Footer Col End -->
				<div class=clear></div>
			</div>
		</div>
    </div><!--End Footer-->
</div>
<?php include 'php/mailchimp_form.php'; ?>
<?php if ( !is_user_logged_in() ) { ?>
	<script>
		jQuery(document).ready(function() {
			var yetVisited = localStorage['visited'];
			if (yetVisited != "yes") {
				setTimeout(function() {
					 jQuery('.subs-popup').css('display','block');
				}, 20000);
				localStorage['visited'] = "yes";
			}
		});
	</script>
<?php } ?>

</div>
<?php if ( is_user_logged_in() ) { ?>
	<div class=refer-button>
		<img src="<?php bloginfo('template_directory'); ?>/images/refer-button.png" alt="Get $10" />
	</div>
	<?php if ( !is_checkout() ) { ?>
		<div class=refer-button-responsive>
			<a href="https://lummiislandwild.com/refer-a-friend/">
				<img src="<?php bloginfo('template_directory'); ?>/images/refer-button.png" alt="Get $10" />
			</a>
		</div>
	<?php } ?>
	<div class="refer-overlay"></div>
	<?php 
		$my_query = new WP_Query( 
			array( 
				'page_id' => '5348'
			)
		);
        while( $my_query->have_posts() ) : $my_query->the_post(); ?>
			<div class="refer-overlay-inner">
				<?php the_content(); ?>
			</div>
    <?php endwhile; ?>
<?php } ?>

	<?php wp_footer(); ?>
	<script src="<?php bloginfo('template_directory'); ?>/js/lightbox.js"></script>
		<!-- Start of LiveChat (www.livechatinc.com) code -->
		<script type="text/javascript">
		window.__lc = window.__lc || {};
		window.__lc.license = 7017201;
		(function() {
		  var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
		  lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
		  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
		})();
	</script>
	<!-- End of LiveChat code -->
	<?php if ( ( is_page() || ( 'post' == get_post_type() ) ) && ( !is_cart() ) && ( !is_checkout() ) ) {
		include 'php/social.php'; 
	} ?>






		<!-- Modular cart -->
		<div class="modular-cart popup-cart as_new_style">
		<h2>In your cart</h2>
		<span class="close-modular-cart"><button onclick="as_close_cart_popup();" class="as_quan_btn">X</button></span>
		<div class="sb-cart-products" id="as_cart_items">
			
		</div>
		<div class="sb-cart-summary" style="border-bottom:0">
			<h3>Perishable Subtotal <span class="per-subtotal" id="as_perishable_Subtotal" >---</span>
				<br />
				<!-- <span class="modular-notice">Perishable total must be at least $65.00</span> -->
			</h3>
		</div>
		<div class="sb-cart-summary" style="border-bottom:0">
			<h3>Non-perishable Subtotal <span class="per-subtotal" id="as_non_perishable_Subtotal">---</span></h3>
		</div>
		<div class="sb-cart-total">
			<h2 class="sb-grand-total">Total: <span id="sb-grand-total" ><?=WC()->cart->cart_contents_total?></span><span>$</span>
				<br />
				<!-- <span class="modular-notice">Add $90.00 for free shipping</span> -->
				<span class="modular-notice" style="color:black">Your Shipping is free!</span>
			</h2>
		</div>
		<div class="modular-checkout">
			<button onclick="{ window.location='<?php bloginfo('url'); ?>/checkout/'; }" >Checkout</button>
		</div>
	</div>
	<!-- Modular cart -->
	<script>



function as_list_cart_popup_items(data){

	var items = data.items;
	var items_quan_count = data.items_quan_count;
	var total = data.total;
		jQuery('#as_perishable_Subtotal').html(total.perishable_Subtotal);
		jQuery('#as_non_perishable_Subtotal').html(total.non_perishable_Subtotal);
		jQuery('#sb-grand-total').html(total.total_cart);

		var as_cartHtml = '';
		jQuery(items).each(function(index,val){
			var img = jQuery(val.image).attr("src");
			as_cartHtml += '<div class="sb-cart-product">';
			as_cartHtml += '<div class="sb-cart-image"><img width="75px" heigth="75px" src="'+img+'"></div>';
			as_cartHtml += '<div class="sb-cart-title"><h4>'+val.title+' - $<span class="sb-cart-price">'+val.line_total+'</span></h4>';
			as_cartHtml += '<div class="scp-ajax-quantity">'; 
			as_cartHtml += '<input type="hidden" id="'+val.product_id+'_key" name="" value="'+val.key+'" >';
			as_cartHtml += '<input type="hidden" id="'+val.product_id+'_quan"   value="'+val.quantity+'" >';
			as_cartHtml += '<div class="cart-minus-one">';
			as_cartHtml += '<button class="as_quan_btn" onclick="as_update_quantity_to_cart('+val.product_id+',0);"  >-</button></div><div class="cart-quantity '+val.product_id+'_quan"  >'+val.quantity+'</div><div class="cart-plus-one"><button class="as_quan_btn" onclick="as_update_quantity_to_cart('+val.product_id+',1);" >+</button></div></div>';
			as_cartHtml += '</div><div style="clear:both"></div></div>';
		});

		jQuery('#as_cart_items').html(as_cartHtml);


		if(items_quan_count > 0){
			jQuery('#as_cart_counter').html(items_quan_count);
			//cart-icon-container
			jQuery('.cart-icon-container').show();

		}



}


	function as_load_cart_items_in_popup(){
		var param = {
			action: 'as_get_cart_items',
		};
        jQuery.post('/wp-admin/admin-ajax.php' , param, function(response) {
			 console.log(response);
			 as_list_cart_popup_items(response.data);
    	});
	}

	function toggle_popup(){
		jQuery('.popup-cart').toggle();
	}


	jQuery(document).ready(function($) {

	// WC()->cart->cart_contents_total
	//	alert(<?=WC()->cart->cart_contents_total?>);


	<?php 
	
	global $post;
	$post_slug = $post->post_name;
	
	?>

	var totalItems 	= <?=WC()->cart->cart_contents_total?>;
	var isPopUp 	= localStorage.getItem("isCartPopupOpen");

	var pageName = '<?=$post_slug?>';

	if( (totalItems > 0) && (isPopUp == 1) && (pageName != 'checkout') ){
		jQuery('.popup-cart').show();
	}

	as_load_cart_items_in_popup();


	});


	/*
	
	
	<a href="https://liwcheckout.wpengine.com/cart" class="cart-icon-container">
                                    <span class="header-cart-icon">
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        <span class="items">15</span>
                                    </span>
                                </a>
	
	*/


	

	function as_close_cart_popup(){
		localStorage.setItem("isCartPopupOpen", 0);
		jQuery('.popup-cart').hide();
	}


	function as_update_quantity_to_cart(productID,action){

		var quan = parseInt(jQuery('#'+productID+'_quan').val());
		if(action == 1){
			quan = (quan+1);
		}else{
			quan = (quan-1);
		}

		jQuery('#'+productID+'_quan').val(quan);
		jQuery('.'+productID+'_quan').html(quan);
		var key = jQuery('#'+productID+'_key').val();
	
		var param = {
			action: 'as_update_cart_quantity',
			product_id: productID,
			quan : quan,
			key:key
		};
        jQuery.post('/wp-admin/admin-ajax.php' , param, function(response) {
			 console.log(response);
			 as_list_cart_popup_items(response.data);
    	});
	}


	function as_add_product_to_cart(productID){
		localStorage.setItem("isCartPopupOpen", 1);
		jQuery('.popup-cart').show();
		var param = {
			action: 'as_add_to_cart',
			product_id: productID
		};
        jQuery.post('/wp-admin/admin-ajax.php' , param, function(response) {
			console.log(response);
			as_list_cart_popup_items(response.data);
    	});
	}



	</script>
	<style>
	.as_quan_btn{
		background-color: transparent;
    	border-color: transparent;
		cursor: pointer
	}
	.as_new_style{
		z-index:1115!important;
	}
	</style>
</body>
	<?php 
	

	
	
	




        if ( get_field('product_subheading') != '' ) { ?>
            <script>
                jQuery(document).ready(function($) {
                //alert('<?php the_field('product_subheading'); ?>');
                    $('#tab-description h2').replaceWith("<h2><?php the_field('product_subheading'); ?></h2>");
                });
            </script>
        <?php }
    ?>
	<script>
		jQuery(document).ready(function($) {




			$('.variations select').on('change', function() {
				if ( $('.stock').hasClass('out-of-stock') ) {
					console.log('Its out of stock');
				}
			})
		});



		
		// jQuery('.popup-cart').show();
		// setTimeout(() => {
		// 	jQuery('.popup-cart').show();
		// }, 5000);

/*
	<div class="sb-cart-product">
				<div class="sb-cart-image">
					<img src="images/product-1.png" />
				</div>
				<div class="sb-cart-title">
					<h4>Wild Coho Salmon Portions - $<span class="sb-cart-price">12.00</span></h4>
					<div class="scp-ajax-quantity">
						<div class="cart-minus-one">-</div>
						<div class="cart-quantity">1</div>
						<div class="cart-plus-one">+</div>
					</div>
				</div>
				<div style="clear:both"></div>
			</div>
			<div class="sb-cart-product">
				<div class="sb-cart-image" style="height:56px;">
					<img src="images/product-2.png" />
				</div>
				<div class="sb-cart-title">
					<h4>Keta Salmon - This is test - - $<span class="sb-cart-price">24.00</span></h4>
					<div class="scp-ajax-quantity">
						<div class="cart-minus-one">-</div>
						<div class="cart-quantity">1</div>
						<div class="cart-plus-one">+</div>
					</div>
				</div>
				<div style="clear:both"></div>
			</div>
	*/



	</script>
</html>
