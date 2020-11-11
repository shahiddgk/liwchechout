<h1>Price List</h1>
<div class="wrap" id="clubs-documents-section">
	<form method="POST" target='_blank' rel="noopener noreferrer">
		<input class="button button-primary" type="submit" name="get_prc_list" value="Generate Price List" />
	</form>
    <table style="border-spacing:1em;">
    <?php
        if ( count($attributes["data"]) > 0){
            foreach ($attributes["data"] as $product){
                echo '<tr>';
                echo '<td>'.$product["product_image"]['html_image'].'</td>';
	                echo '<td valign="top" style="padding: 10px 0 0 10px; width: 100%; background-color: #F7F0D9;">';
	                    echo '<p style="font-weight: 700; font-size: 16px; margin: 0; line-height: 1;">'.$product['product_title'].'</p><br/>';
	                    if( $product['variations'] ){
	                        foreach ($product['variations'] as $k => $v){
	                            echo '<p style="font-size: 14px; margin: 0; line-height: 1;" >'.$v["variation_title"] .', $'.$v["raw_price"].'</p><br/>';
                            }
                        }else{
	                        echo '<p style="font-size: 14px; margin: 0; line-height: 1;" >$'.$product["product_price"].'</p>';
                        }
	                echo '</td>';
                echo '</tr>';
            }
        }
    ?>
    </table>
</div>