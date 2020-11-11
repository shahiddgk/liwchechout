<?php

if(current_user_can('administrator')){
    $club_id = ( $_POST['admin_tag_id'] ) ? $_POST['admin_tag_id'] : $_GET['club_id'] ;
    $user["club_taxonomy"] = get_term_by('id',$club_id,'clubs');
    $meta = get_term_meta($club_id);
    $discount = $meta["clubs_discount_p"][0];   
    $date_after = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $meta["clubs_open_date"][0]));
    $date_before = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $meta["clubs_close_date"][0]));
}else{
    $user = \LW\Settings::currentUser();
    $discount = $user["club_term_meta"]["clubs_discount_p"];
    $date_after = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $user["club_term_meta"]["clubs_open_date"]));
    $date_before = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $user["club_term_meta"]["clubs_close_date"]));
}


$club_shipping = ( ! empty($meta["clubs_ship_price"][0]) ) ? $meta["clubs_ship_price"][0] : $user["club_term_meta"]["clubs_ship_price"];

$weight_unit = get_option('woocommerce_weight_unit');

$currency = get_woocommerce_currency_symbol();

$filters = array(
    'post_type' => 'shop_order',
    'post_status' => array('wc-processing','wc-completed'),
    'posts_per_page' => -1,
    'meta_key' => 'clubs-slug',
    'meta_compare' => '=',
    'meta_value' => $user["club_taxonomy"]->slug,
    'orderby' => 'date',
    'order' => 'DESC',
    'date_query' => array(
        array(
            'column' => 'post_date',
            'after' => ( $_GET['open_date'] ) ? $_GET['open_date'] : $date_after,
            'before' => ( $_GET['close_date'] ) ? $_GET['close_date'] : $date_before,
            )
    )
);
if( isset($_GET['manager_order_id']) ){
    $manager_order_ID[] = $_GET['manager_order_id'];
	$filters = array(
		'post_type' => 'shop_order',
		'post_status' => array('wc-processing','wc-completed'),
		'posts_per_page' => -1,
		'post__in' => $manager_order_ID,
		'meta_key' => 'clubs-slug',
		'meta_compare' => '=',
		'meta_value' => $user["club_taxonomy"]->slug,
	);
}

$tot_price = 0;
$tot_percent = 0;
$tot_prod = 0;
$loop = new \WP_Query($filters);


if($loop->post_count > 0){
while ($loop->have_posts()) {

    $loop->the_post();

    $order = wc_get_order($loop->post->ID);
    $order_id = $order->get_id();
    $count_order += count($order_id);

    $items = $order->get_items();
    
    $allItems[] = $items;
    foreach ($items as $item) {
        $product_id = $item['product_id'];
        $product_variation_id = $item['variation_id'];

        if ($product_variation_id) {
            $product = wc_get_product($item['variation_id']);
	        $chefs_percent = get_post_meta($order_id,'order_variation_chef_prc_'.$item['variation_id'],true);
	        $prod_weight = $product->get_weight();
	        if( $prod_weight === '' ){
		        $parent = wc_get_product($product->post->post_parent);
		        $prod_weight = $parent->get_weight();
	        }
        } else {
            if($item['product_id'] !== 0){
	            $product = wc_get_product($item['product_id']);
	            $chefs_percent = get_post_meta($order_id,'order_product_chef_prc_'.$item['product_id'],true);
	            $prod_weight = $product->get_weight();
            }
        }

	    $discount_chefs = ( empty( $chefs_percent ) ) ? $discount : $chefs_percent + $discount;

	        $tot_price += $product->get_price() * $item['qty'];
            
            $tot_percent += $product->get_price() * $item['qty'] * $discount_chefs / 100;
            
            $price_grand_total = $tot_price - $tot_percent;

            $content_product[$product->get_id()][] = array(
                'id'=> $product->get_id(),
                'name'=>$item['name'],
                'qty'=>$item['qty'],
                'price'=>$product->get_price(),
                'weight'=>$prod_weight,
                'lot_num' => $product->get_sku()
            );
    }

}

foreach ($content_product as $product){
        foreach ($product as $key){
            $we += $key['weight'] *  $key['qty'];
            $tot_prod += $key['qty'];
        }
    }

?>
<h2>You are viewing product & variation totals of <b><?php echo $count_order; ?></b> unprocessed orders.</h2><br><hr>
<div>
    <p>Total Quantity: <b><?php echo $tot_prod; ?></b> products</p>
    <p>Total Weight: <b><?php echo $we; ?><?php echo $weight_unit; ?></b></p>
    <p>Total Value: <b><?php echo $currency; ?><?php echo number_format($tot_price,2);//number_format($tp,2); ?></b></p>
    <p>Total Discount: <b><?php echo $currency; ?><?php echo number_format($tot_percent,2); ?></b></p>
    <p>Total Products: <b><?php echo $currency; ?><?php echo number_format($price_grand_total,2); ?></b></p>
    <p>Shipping Total: <b><?php echo $currency; ?><?php echo $shipping_total = number_format($club_shipping * $we,2); ?></b></p>
    <p>Grand Total: <b><?php echo $currency; ?><?php echo number_format($shipping_total + $price_grand_total,2); ?></b></p>
    
    <p>----------------------------------------------------------------------------------------------</p><br><br>
    <?php
    foreach ($content_product as $product){
        foreach ($product as $key){
            $qty += $key['qty'];
            $bay[$key['name']][$key['id']] = array(
                'id'    => $key['id'],
                'name'  => $key['name'],
                'price' => $key['price'],
                'qty'   => $qty,
                'weight'=> $key['weight'],
                'sku'   => $key['lot_num']
            );
        }
        $qty = '';
    }
    foreach($bay as $key => $v){
        echo '<h2><b>'.$key.'</b></h2><br>';
        foreach ($v as $val){

            $variation = wc_get_product($val["id"]);

            if($variation && $variation->is_type('variation')){
	            $variation_attributes = $variation->get_variation_attributes();
	            $variation_name = '# Variation: '.reset($variation_attributes);
            }else{
	            $variation_name = '';
            }
            echo '<br><h4>&nbsp;&nbsp;&nbsp;<i>'.$variation_name.' ( Lot Number: '.$val['sku'].' )</i></h4>';
            echo '<p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Quantity: '.$val['qty']."</p>";
            echo '<p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Weight: '.$val['weight'] * $val['qty']." lbs</p>";
            echo '<p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Value: '.$currency. number_format($val['price'] * $val['qty'],2)."</p><br><br>";
        }
    }
}else{
        echo '<h1 style="text-align: center;">No Orders</h1>';
}
    ?>
</div>