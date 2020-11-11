<?php

if (current_user_can('administrator') && $_GET['taxonomy'] == 'clubs' && !empty($_GET['tag_ID']) ){
    
    $weight_unit = get_option('woocommerce_weight_unit');

    $meta_club = get_term($_GET['tag_ID']);
    $meta = get_term_meta($_GET['tag_ID']);
    
    if(isset( $_POST['filter_date_in_tax'] ) ){
        $temp['date_after'] = $_POST['date_after'];
        $temp['date_before'] = $_POST['date_before'];
        $temp['order_status'] = $_POST['order_status'];
    }
   
    if($meta["clubs_open_date"][0]){
        $date_after = \LW\Settings::getDateFormat('this_date',array(
        'date' => $meta["clubs_open_date"][0]
            ));
    }
    
    $date_before = current_time('m-d-Y h:i a',0);
?>
<div id="to_taxonomy_orders" class="wpwrap-in-taxonomy">
    <div class="lw-taxonomy-orders-container">
        <table class="filter-date-form">
            <tr>
                <td class="tax_order_heading"><p>Orders</p></td>
                <form id="filter_form" method="POST">
                    <td class="filter-date-field">
                        <label for="date_after">After Date</label>
                        <input id="filter_date_manager_after" type="text" name="date_after" value="<?php if($temp['date_after']){ echo $temp['date_after'];}elseif($date_after){echo $date_after;}?>" autocomplete="off"/>
                    </td>
                    <td class="filter-date-field">
                        <label for="filter_date_manager_before">Before Date</label>
                        <input id="filter_date_manager_before" type="text" name="date_before" value="<?php if($temp['date_before']){echo $temp['date_before']; }elseif(date_before){echo $date_before; } ?>" autocomplete="off"/>
                    </td>
                    <td class="filter-date-field">
                        <select name="order_status">
                            <option>All</option>
                            <option value="wc-completed" <?php if($temp['order_status'] == 'wc-completed'){echo 'selected';}?>>Total</option>
                            <option value="wc-processing" <?php if($temp['order_status'] == 'wc-processing'){echo 'selected';}?>>Processing</option>
                        </select>
                    </td>
                    <td class="filter-date-field">
                        <input id="pdf_gen_id" type="checkbox" name="pdf_create" value="1" /><span>Generate PDF</span>
                    </td>
                    <td>
                        <?php submit_button( 'Filter', 'primary', 'filter_date_in_tax' ); ?>
                    </td>
                </form>
            </tr>
        </table><br/>
        <?php
        ?>
        <?php  if($attributes["orders_data"] !== null) { ?>
            <?php
            foreach ($attributes["orders_data"] as $orders) {

                foreach ($orders as $product) {
                    $we = $product['total_weight'];
                    $qty_n = $product['total_qty'];
                    $orderID[] = array(
                        'OrderID'       => $product["OrderID"],
                        'OrderDate'     => $product['OrderDate'],
                        'UserName'      => $product['UserName'],
                        'UserPhone'     => $product['UserPhone'],
                        'OrdCount'      => $product['OrdersCount'],
                        'CurrSymbol'    => $product['CurrencySymbol'],
                        'PostStatus'    => $product['post_status'],
                        'Weight'        => $product['weight'],
                        'RawPrice'      => $product['raw_price'],
                        'ItemDiscount'  => $product['item_discount'],
                        'TotalDiscount' => $product['total_discount'],
                        'TotalWeight' => $product['total_weight'],
                        'NewProductWeight' => $product['new_weight_product'],
                    );
                    foreach ($product['products'] as $key) {

                        $ord[$product["OrderID"]][] = array(
                            'ID' => $key['product_id'],
                            'QTY' => $key['qty'],
                            'Name' => $key['name'],
                            'total' => $key['line_total'],
                            'variation_id' =>$key['variation_id'],
                        );
                    }
                }
            }

            $total_weight = 0;
            $total_qty = 0;
            $tpwd = 0;
            $tdp = 0;
            $td = 0;
	        $counter = 0;
	        if($orderID){
		        foreach ($orderID as $order) {


			        $td += $order['ItemDiscount'];
			        $tpwd += $order["RawPrice"];
			        $tdp = $order["TotalDiscount"];

			        foreach ($ord[$order['OrderID']] as $key){
				        $d = '';
				        $d =  $key['QTY'].' x '.$order['NewProductWeight'][$counter].' '.$weight_unit;
				        $counter++;
				        if($order['PostStatus'] == 'completed'){
					        $name_prefix = 'Total';
					        $c = true;
				        }
                        elseif($order['PostStatus'] == 'processing'){
					        $name_prefix = 'Processing';
					        $b = true;
				        }
				        $tot_weight_new = $order["TotalWeight"];
			        }
		        }
            }

            if($c == true && $b == true){
                $name_prefix = 'All';
            }

            echo $attributes['display'];
	        if( isset($attributes["orders_data"]["order_items"]) ){
		        foreach ( $attributes["orders_data"]["order_items"] as $shipping ) {
			        $weight_price = $shipping["shipping_total"];

		        }
            }

            $grand_total = $weight_price + $tdp;

            $html .= '<table id="order_table" class="wp-list-table widefat fixed striped">
                    <tr>
                        <td width="200" height="20" colspan="6" align="right" style="font-size: 16px;">
                             '.$name_prefix.' Orders: <span style="color: green; font-size: 18px;">'.$order['OrdCount'].'</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="200" height="20" colspan="6" align="right" style="font-size: 16px;">
                            Total Order Weight:<span style=" color: green; font-size: 18px;"> '.$tot_weight_new.' '.$weight_unit.'</span><br/>
                        </td>
                    </tr>
                    <tr>
                        <td width="200" height="20" colspan="6" align="right" style="font-size: 16px;">
                            Shipping Total: <span style=" color: green; font-size: 18px;"> '.$order['CurrSymbol'].number_format($weight_price,2).'</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="200" height="20" colspan="6" align="right" style="font-size: 16px;">
                            Total Products Price: <span style=" color: green; font-size: 18px;">'.$order['CurrSymbol'].number_format($tdp,2).'</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="200" height="20" colspan="6" align="right" style="font-size: 16px;">
                            Grand Total: <span style=" color: green; font-size: 18px;">'.$order['CurrSymbol'].number_format($grand_total,2).'</span>
                        </td>
                    </tr>
        </table>';
        }
        echo $html;
        ?>
    </div>
</div>
<?php } ?>