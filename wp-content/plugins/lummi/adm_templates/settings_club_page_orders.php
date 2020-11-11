<?php
$weight_unit = get_option('woocommerce_weight_unit');
?>
<div class="wrap">
    <h1>Orders</h1>
	<div style="display:none">
		<?php print_r($attributes); ?>
	</div>
    <div class="lw-orders-container">

            <div class="filter-date-form">
                <form id="filter_form" method="POST" target="_blank" rel="noreferrer noopener">
                    <div class="filter-date-field">
                        <input style="display: none;" id="pdf_gen_id" type="checkbox" name="pdf_create" value="1" checked="checked"/>
                    </div>
                    <?php submit_button( 'Order Summary PDF', 'primary', 'filter_date' ); ?>
                </form>
                <p class="submit">
                <a href="<?php echo esc_url(admin_url('admin.php?page=manage-club-orders&club_id='.$attributes["club_ID"].'&open_date='.$attributes["open_date"].'&close_date='.$attributes["close_date"].'&section=pdf')); ?>" target="_blank" class="button button-primary">Packing Slip PDF</a></p>
            </div>
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
                        'UserEmail'     => $product['UserEmail'],
                        'OrdCount'      => $product['OrdersCount'],
                        'CurrSymbol'    => $product['CurrencySymbol'],
                        'PostStatus'    => $product['post_status'],
                        'Weight'        => $product['weight'],
                        'RawPrice'      => $product['raw_price'],
                        'ItemDiscount'  => $product['item_discount'],
                        'TotalDiscount' => $product['total_discount'],
                        'total_weight' => $product['total_weight'],
                        'total_qty' => $product['total_qty'],
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
            $tdp = 0;
	        $counter = 0;
	        if($orderID){
		        foreach ($orderID as $order) {
			        $tdp = $order["TotalDiscount"];

			        foreach ($ord[$order['OrderID']] as $key){
				        $d = '';

				        $d =  $key['QTY'].' x '.$order['NewProductWeight'][$counter].' '.$weight_unit;
				        $counter++;
				        if($order['PostStatus'] == 'completed'){
					        $name_prefix = 'Completed';
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

            // This display table sortable
            echo $attributes['display'];

			// -> This litle thing is useless and produces the wrong shipping cost. The $weight_price variable is replaced below
            if( isset($attributes["orders_data"]["order_items"]) ){
	            foreach ( $attributes["orders_data"]["order_items"] as $shipping ) {
		            $weight_price = $shipping["shipping_total"];
	            }
            }			
			// ->This fixes the incorrect shipping price inherited from BGO's code
			$club_shipping_rate = $attributes["club_info"]["club_term_meta"]["clubs_ship_price"];
			$weight_price = $tot_weight_new * $club_shipping_rate;

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
        else{
            echo '<h1 style="text-align: center;">No Orders</h1>';
            echo '<button class="back_button_orders" onclick="history.go(-1);"><< Back </button>';
        }
        echo $html;
        ?>
    </div>
</div>