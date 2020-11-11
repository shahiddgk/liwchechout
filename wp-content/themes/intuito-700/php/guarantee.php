        <div class="guarantee">
			<?php 
				$club = \LW\Settings::currentUser(); 
				$cart_info = $club["user_meta"]['_woocommerce_persistent_cart']['cart'][0];
				$cart_info = unserialize($cart_info);
				print_r($cart_info); 
				echo $cart_info['variation']['attribute_pa_club-type'];
			?>
			<div class="guarantee-item" id="money-back-guarantee">
				<i class="fas fa-check-circle"></i> <span class="guarantee-top-line">Money Back</span>
				<div class="guarantee-container">
					<span class="guarantee-bottom-line">guarantee</span> <i class="fas fa-question-circle"></i>
				</div>
				<div class="bubble" id="money-back-guarantee-popup">
					<?php the_field('guarantee_text','option'); ?>
				</div>
			</div>
			<div class="guarantee-item" id="free-shipping">
				<i class="fas fa-check-circle"></i> <span class="guarantee-top-line">Free Shipping</span>
				<div class="guarantee-container">
					<span class="guarantee-bottom-line">orders over $99</span> <i class="fas fa-question-circle"></i>
				</div>
				<div class="bubble" id="free-shipping-popup">
					<?php the_field('free_shipping_text','option'); ?>
				</div>
			</div>
			<div class="guarantee-item" id="fresh-delivery">
				<i class="fas fa-check-circle"></i> <span class="guarantee-top-line">Fresh Delivery</span>
				<div class="guarantee-container">
					<span class="guarantee-bottom-line">guarantee</span> <i class="fas fa-question-circle"></i>
				</div>
				<div class="bubble" id="fresh-delivery-popup">
					<?php the_field('fresh_delivery_guarantee','option'); ?>
				</div>
			</div>
			<div class="clear:left"></div>
        </div>
<script>
	jQuery('.bubble').click(function() {
		jQuery(this).hide();
	})
</script>
<style>
	.guarantee img { display:block; }
	.guarantee-item { float:left; cursor:default; }
	.guarantee-item#money-back-guarantee { padding-right:15px; }
	.guarantee-item#free-shipping { padding-right:15px; }
	.guarantee .fa-check-circle { color:green; }
	.guarantee .fa-question-circle { font-size:9pt; }
	.guarantee-top-line { text-transform:uppercase; font-size:10pt; }
	.guarantee-bottom-line { font-size:10pt; font-style:italic; line-height:14pt; display:inline-block; }
	.guarantee-container { margin-top:-10px; }
	#money-back-guarantee-popup, #free-shipping-popup, #fresh-delivery-popup { position:absolute; display:none; color:white; font-size:12px; line-height:16px; right:5px; top:56px; }
	#money-back-guarantee:hover #money-back-guarantee-popup, #free-shipping:hover #free-shipping-popup, #fresh-delivery:hover #fresh-delivery-popup { display:block; }
	#money-back-guarantee-popup { left:5%; max-width:200px; }
	#free-shipping-popup { left:25%; max-width:200px; }
	#fresh-delivery-popup { left:27%; max-width:200px; }
	#free-shipping-popup { left:36%; }
	#free-shipping, #fresh-delivery { display:none; }
	body.public-user #free-shipping, body.public-user #fresh-delivery { display:inline-block; }
	body.public-user #fresh-delivery-popup { left:46%; }
	@media screen and (min-width: 1100px) and (max-width: 1200px ) {
		.guarantee-top-line { font-size:9pt; }
	}
	@media screen and (min-width: 790px) and (max-width: 900px ) {
		.guarantee-top-line { font-size:9pt; }
		.guarantee-item#money-back-guarantee { padding-right:5px; }
		.guarantee-item#free-shipping { padding-right:5px; }
	}
	@media screen and (max-width: 789px ) {
		.guarantee-container {display:none;}
		.guarantee-item { display:block!important; float:none; width:auto; }
		
	}
.bubble
{
   position: relative;
   padding: 6px;
   background: #00316a;
   -webkit-border-radius: 0px;
   -moz-border-radius: 0px;
   border-radius: 0px;
    border: #8c6d4d solid 2px;
}

.bubble:after
{
    content: '';
    position: absolute;
    border-style: solid;
    border-width: 0 8px 19px;
    border-color: #00316a transparent;
    display: block;
    width: 0;
    z-index: 1;
    top: -19px;
    left: 21px;
}

.bubble:before
{
    content: '';
    position: absolute;
    border-style: solid;
    border-width: 0 9px 20px;
    border-color: #8c6d4d transparent;
    display: block;
    width: 0;
    z-index: 0;
    top: -22px;
    left: 21px;
}
.public-user .bubble#fresh-delivery-popup:after { left:100px; }
.public-user .bubble#fresh-delivery-popup:before { left:101px; }
.public-user #fresh-delivery-popup { right:0; max-width:200px; }	
</style>