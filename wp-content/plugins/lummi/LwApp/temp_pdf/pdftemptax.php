<?php

$club_data = get_term_meta($_GET["tag_ID"]);

$weight_unit = get_option('woocommerce_weight_unit');

$html = '<table id="order_table" class="wp-list-table widefat fixed striped">
            <tr>
                <td><strong>User Name</strong></td>
                <td><strong>Order Date</strong></td>
                <td><strong>Phone</strong></td>
                <td><strong>Order ID</strong></td>
                <td><strong>Status</strong></td>
                <td><strong>Items</strong></td>
            </tr>';

if($attributes["orders_data"]){

    foreach ($attributes["orders_data"] as $orders) {

        foreach ($orders as $product) {
            $orderID[] = array(
                'OrderID' => $product["OrderID"],
                'OrderDate' => $product['OrderDate'],
                'UserName' => $product['UserName'],
                'UserPhone' => $product['UserPhone'],
                'OrdCount' => $product['OrdersCount'],
                'CurrSymbol' => $product['CurrencySymbol'],
                'PostStatus' => $product['post_status'],
                'Weight' => $product['weight'],
                'RawPrice' => $product['raw_price'],
                'ItemDiscount' => $product['item_discount'],
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
//    $tpwd = 0;
    $tdp = 0;
//    $td = 0;
	$counter = 0;
    foreach ($orderID as $order) {

//        $td += $order['ItemDiscount'];
//        $tpwd += $order["RawPrice"];
        $tdp = $order["TotalDiscount"];

        $html .= '<tr>
                        <td width="200" height="20">'.$order['UserName'].'</td>
                        <td width="200" height="20">'.$order['OrderDate'].'</td>
                        <td width="200" height="20">'.$order['UserPhone'].'</td>
                        <td width="200" height="20">'.$order['OrderID'].'</td>
                        <td width="200" height="20">'.$order['PostStatus'].'</td>
                   <td width="200" height="20">';
        foreach ($ord[$order['OrderID']] as $key){
//            $total_qty += $key['QTY'];

            $d = '';
//            foreach ($order['Weight'] as $key1) {
//                foreach ($key1 as $am) {
//
//                    if ($am["empty"] !== '0') {
//                        if($am[$key['variation_id']][0] !== NULL) {
//                            $d = $am[$key['variation_id']][0] . ',';
//                        }
//                    } else {
//                        if ($am['shipWeight'] !== '') {
//                            $d = $am['shipWeight'];
//                        }
//
//                    }
//                }
//                $total_weight += $key['QTY'] * $d;
//            }
//
//            if($d ==''){
//                $d='';
//            }else{
//                $d =  $key['QTY'].' x '.trim($d, ',').' '.$weight_unit;
//            }
	        $d =  $key['QTY'].' x '.$order['NewProductWeight'][$counter].' '.$weight_unit;
	        $counter++;
	        $variation = wc_get_product($key['variation_id']);
	        if($variation){
		        $variation_attributes = $variation->get_variation_attributes();
		        $variation_name = ' | '.reset($variation_attributes);
	        }else{
		        $variation_name = '';
	        }

	        $html .= $key['Name'] . $variation_name .' | '.$d."\n".'';

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
        $html .= '</td>';
    }
    if($c == true && $b == true){
        $name_prefix = 'All';
    }

    $weight_price = $tot_weight_new * $club_data["clubs_ship_price"][0];
    $grand_total = $weight_price + $tdp;
    $html .= '</tr><tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            Orders:
                        </td>
                        <td>
                         '.$order['OrdCount'].'
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Total Order Weight: 
                        </td>
                        <td>
                        '.$tot_weight_new.' '.$weight_unit.'
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Shipping Total: 
                        </td>
                        <td>
                        '.$order['CurrSymbol'].number_format($weight_price,2)."\n".'
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Total Products Price:
                        </td>
                        <td>
                            '.$order['CurrSymbol'].number_format($tdp,2).'
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Grand Total: 
                        </td>
                        <td>
                        '.$order['CurrSymbol'].number_format($grand_total,2).'
                        </td>
                    </tr>
            </table>';
    echo $html;
}