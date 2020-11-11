<?php

namespace LW;

class AdminPageClubs
{
    /**
     * Contains current manager data and his club data
     *
     * @var array
     */
    private $user = array();
	private $_csv_emails_all_users = array();
	private $_csv_emails_order_users = array();
	private $_csv_emails_order_users_meta = array();
	private $_csv_emails_all_users_meta = array();

    public function __construct()
    {
    }

    /**
     * Register manager pages
     */
    public function doAdminClubPages(){
	    add_action('admin_init',array($this,'packingSlipPdf'));
	    add_action('admin_init',array($this,'invoiceListPdf'));
	    add_action('admin_init',array($this,'generatePriceList'));
//        add_action('admin_init',array($this,'pdfsHistorySections'),11,1);
//        add_action('admin_init',array($this,'getCurrentUser'));
	    add_action('admin_init',array($this,'pdfGenerator'));
	    add_action('admin_menu',array($this,'structureAdminClubPages'),20);
	    add_action('admin_init',array($this,'managerRmUser'));
	    add_action('admin_menu', array($this,'removeMediaMenuForManagers' ));
	    add_action('admin_bar_menu', array($this,'adminBarChanges' ),999);
	    add_action('admin_enqueue_scripts',array($this,'emailInvitationAssets'));
	    add_action( 'wp_ajax_nopriv_ajaxGetUsersClubInfo', array($this,'ajaxGetUsersClubInfo') );
	    add_action('wp_ajax_ajaxGetUsersClubInfo',array($this,'ajaxGetUsersClubInfo'));
	    add_action('admin_init',array($this,'exportEmails'));
	    add_action( 'admin_head',array($this,'hideNoticeForManagers'));
		add_action( 'admin_init',array($this,'getUserCsvEmails'));
    }

	function hideNoticeForManagers(){
		if ( current_user_can( 'club_manager_options' )) {
			echo '<style>.error, .updated, .settings-error, .notice, .is-dismissible, .update-nag, .updated { display: none !important; }</style>';
		}
	}

    /**
     * Remove media menu for managers
     */
    public function removeMediaMenuForManagers() {
        if(current_user_can('club_manager_options')){
            remove_menu_page( 'upload.php' );
            remove_menu_page( 'index.php' );
        }
    }


    /**
     * Remove some unnecessary links from admin bar and add new for quick user access
     * @param $wp_admin_bar
     */
    function adminBarChanges( $wp_admin_bar ) {
        if(!current_user_can('administrator')){
            $wp_admin_bar->remove_node( 'new-content' );
            $wp_admin_bar->remove_node('dashboard');

            $new_content_node = $wp_admin_bar->get_node('site-name');

            $new_content_node->href = site_url().'/wp-admin/admin.php?page=manage-club-home';

            $wp_admin_bar->add_node($new_content_node);

            $args = array(
                'id'    => 'club-manager',
                'title' => 'Manage Club',
                'parent' => 'site-name',
                'href'  => site_url().'/wp-admin/admin.php?page=manage-club-home'
            );
            $wp_admin_bar->add_node( $args );
        }
    }

    /**
     * Pages structures for managers
     */
    public function structureAdminClubPages(){

        add_menu_page(
            'Manage Club',
            'Manage Club',
            'club_manager_options',
            'manage-club-home','','',1
        );

        add_submenu_page(
            'manage-club-home',
            'Manage Club',
            'Manage Club',
            'club_manager_options',
            'manage-club-home',
            array($this, 'manageClubPageHome')
        );
	    add_submenu_page(
		    'manage-club-home',
		    'Club Members',
		    'Club Members',
		    'club_manager_options',
		    'manage-club-home#club-members',
		    array($this, 'manageClubPageHome')
	    );

        add_submenu_page(
            'manage-club-home',
            'Club Emails',
            'Club Emails',
            'club_manager_options',
            'manage-club-email',
            array($this,'manageClubPageEmail')
        );
        add_submenu_page(
            'manage-club-home',
            'Current Orders',
            'Current Orders',
            'club_manager_options',
            'manage-club-orders',
            array($this,'manageClubOrdersPage')
        );
        add_menu_page(
            'Email Invitation',
            'Email Invitation',
            'administrator',
            'email-invitation','','dashicons-email-alt',59
        );
        add_submenu_page(
            'email-invitation',
            'Email Sender',
            'Email Sender',
            'administrator',
            'email-invitation',
            array($this,'emailInvitationSender')
        );
        add_submenu_page(
            'email-invitation',
            'Email Settings',
            'Email Settings',
            'administrator',
            'email-invitation-settings',
            array($this,'emailInvitationSettings')
        );

        if(current_user_can('administrator')){
            add_menu_page(
                'Clubs History',
                'Clubs History',
                'administrator',
                'clubs-history',
                array($this,'clubsHistoryPage'),
                'dashicons-clipboard',
                57
            );
        }else{
            add_submenu_page(
                'manage-club-home',
                'Club History',
                'Club History',
                'club_manager_options',
                'clubs-history',
                array($this,'clubsHistoryPage')
            );
        }

	    add_submenu_page(
		    'manage-club-home',
		    'Price List',
		    'Price List',
		    'club_manager_options',
		    'manage-club-pricelist',
		    array($this,'rendManagerPriceList')
	    );
    }

	/**
	 * Rending PDF Price List Page
	 */
	public function rendManagerPriceList(){

		$club_data = \LW\Settings::currentUser();
		$club_id = (int) $club_data["user_meta"]["user_club"];
		$attributes['data'] = $this->getProductsPriceList($club_id);

		\LW\LW_Template::getTemplate('adm_templates', 'clubs_price_list',$attributes);
	}

	/**
	 * Get products price list data
	 * @param $club_id
	 *
	 * @return array
	 */
	public function getProductsPriceList($club_id){
		$args = array(
			'post_type'             => 'product',
			'post_status'           => 'publish',
			'posts_per_page'        => -1,
			'tax_query'             => array(
				array(
					'taxonomy'      => 'clubs',
					'field'         => 'term_id',
					'terms'         => $club_id,
					'operator'      => 'IN'
				)
			)
		);

		$products = new \WP_Query($args);

		$pr = array();

		if($products->have_posts()){
			while ($products->have_posts()){

				$products->the_post();

				$_product = wc_get_product($products->post->ID);

				if($_product->has_child()){

					$variations = $_product->get_available_variations();

					foreach ($variations as $k => $value){
						if($value["attributes"]["attribute_pa_club-type"] == 'private-club'){
							$first_value = reset($value["attributes"]);
							$pr[$products->post->ID]['product_title'] = $products->post->post_title;
							$pr[$products->post->ID]['product_image'] = $this->getPriceListImage($products->post->ID);
							$pr[$products->post->ID]['variations'][$value["variation_id"]]['variation_title'] = $first_value;
							$pr[$products->post->ID]['variations'][$value["variation_id"]]['raw_price'] = number_format($value["display_price"],2,'.','');
						}
					}
				}else{
					$pr[$products->post->ID]['product_title'] = $_product->get_title();
					$pr[$products->post->ID]['product_image'] = $this->getPriceListImage($products->post->ID);
					$pr[$products->post->ID]['product_price'] = number_format($_product->get_price(),2,'.','');
				}
			}
		}

		return $pr;
	}

	/**
	 * Generate Price List PDF
	 */
	public function generatePriceList(){

		if( isset($_POST['get_prc_list'])){
			$club_data = \LW\Settings::currentUser();
			$club_id = (int) $club_data["user_meta"]["user_club"];
			$club_term_meta = get_term_meta($club_id);
			$club_meta = get_term($club_id);

			$pr = $this->getProductsPriceList($club_id);

			$pdf = new \LW\PriceListTablePdf();
			$pdf->AddPage("P","A4");
			$pdf->AliasNbPages('{nb}');
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(80);
			$pdf->Cell(30,10,$club_data["club_taxonomy"]->name .' Price List',0,0,'C');
			$pdf->Ln(6);
			$pdf->Cell(80);
			$pdf->Cell(30,10,'Valid ' .$club_term_meta["clubs_open_date"][0] .' - '.$club_term_meta["clubs_close_date"][0],0,0,'C');
			$pdf->Ln(6);
			$pdf->Cell(80);
			$pdf->Cell(30,10,'Shipping Rate $' . number_format($club_term_meta["clubs_ship_price"][0],2,'.','') .' per lb',0,0,'C');
			$pdf->Ln(20);
			$pdf->SetTitle( $club_data["club_taxonomy"]->name );
			$pdf->SetAuthor($club_data["club_taxonomy"]->name);

			$output = '';
			$num = 0;
			$bh = 0;
			$prod_price = 0;

			foreach ($pr as $key => $val){
				$img = $val["product_image"]['path'];
				$pdf->SetFillColor(247,240,217);

				// Increase the height of the cell with the background if the products are more than 3
				if( $val['variations'] ){
					foreach ($val['variations'] as $kv => $vv){
						$bh++;
					}
				}else{
					$prod_price = $val["product_price"];
				}

				if($bh  > 3 && $bh < 4){
					$calc_bh = $bh;
				}
				elseif ($bh > 4){
					$calc_bh = $bh + 2;
				}
				else{
					$calc_bh = 0;
				}

				$pdf->Cell(185,30 + $calc_bh,'', 0,0,0,'L',true);
				$pdf->SetX(10);
				$image = $pdf->Image($img, $pdf->GetX(), $pdf->GetY(), 30);
				$pdf->Cell(30,30,$image,0,0,'L',false);
				$pdf->SetFont('Arial', 'B', 11);
				$pdf->Cell(5);
				$pdf->Ln(2);
				$pdf->SetX(45);
				$pdf->Cell(50,5,$val["product_title"],0,0,'L');
				$pdf->Ln(6);
				$pdf->Cell(35);
				if( $val['variations'] ){
					$pdf->SetFont('Arial', '', 10);
					foreach ($val['variations'] as $kv => $vv){
						$output .= $vv["variation_title"] .', $'.$vv ["raw_price"]."\r\n";
						$num++;
					}
				}else{
					$pdf->SetFont('Arial', 'B', 11);
					$output .= '$'.$prod_price;
				}
				$pdf->MultiCell(150,6,$output,0,'L');
				switch($num){
					case 1 : $pdf->Ln(23);
						break;
					case 2 : $pdf->Ln(17);
						break;
					case 3 : $pdf->Ln(11);
						break;
					case 4 : $pdf->Ln(9);
						break;
					case 5 : $pdf->Ln(6);
						break;
					default : $pdf->Ln(23);
				}
				if($pdf->GetY() > 260){
					$pdf->AddPage("P","A4");
				}
				$output = '';
				$num = 0;
				$bh = 0;
			}

			$pdf->Output('I',$club_meta->slug.'-'.date('m-d-Y').'.pdf',true);

			die();
		}
	}

	/**
	 * Get Featured Image From Product
	 * @param type $pid
	 * @return type array
	 */
	public function getPriceListImage($pid)
	{
		if(has_post_thumbnail($pid))
		{
			$image = wp_get_attachment_image_src(get_post_thumbnail_id( $pid ),array(130));
			$img['path'] =  get_home_path().substr($image[0], strpos($image[0],'wp-content/'));
			$img['url'] = $image[0];
			$img['html_image'] = get_the_post_thumbnail($pid,array(130));
			$img['w'] = $image[1];
			$img['h'] = $image[2];
		}else{
			$img['path'] =  LW_PLUGIN_DIR.'/additional/img/no-thumb-250.jpg';
			$img['url'] = LW_PLUGIN_URL.'additional/img/no-thumb-250.jpg';
			$img['html_image'] = get_the_post_thumbnail($pid,array(130));
			$img['w'] = '130';
			$img['h'] = '130';
		}

		return $img;
	}
    
    /**
     * Rending and processing club history page
     * @global type $wpdb
     */
    public function clubsHistoryPage(){
        global $wpdb;

        if( current_user_can('administrator') ){
            
            $q_all_clubs = "SELECT DISTINCT club_id, club_name FROM {$wpdb->prefix}clubs_history WHERE create_date IS NOT NULL ORDER BY club_name ASC";
            $attributes["all_clubs"] = $wpdb->get_results($q_all_clubs);
            
            $club_id = $_COOKIE['HCLUB_ID'];

            $c_id = ( isset( $club_id ) ) ? 'club_id='.$club_id : 1;
            
            $attributes["selected_club"] = ( isset($club_id ) ) ? $club_id : 'all';
            
        }else{
           $user = \LW\Settings::currentUser();
           $c_id = 'club_id='.$user["club_taxonomy"]->term_id;
        }
        
        
        $order_by = ( $_GET['order_by'] ) ? $_GET['order_by'] : 'id';
        $asc_desc = ( $_GET['order'] ) ? $_GET['order'] : 'DESC';

        $attributes["db_data"] = $wpdb->get_results( "SELECT id, club_id, club_name, club_slug, open_date, close_date, create_date, update_date 
                                                            FROM {$wpdb->prefix}clubs_history 
                                                            WHERE create_date IS NOT NULL
                                                            AND $c_id
                                                            ORDER BY $order_by 
                                                            $asc_desc");

        $attributes["order"] = ( $_GET['order'] === 'ASC' ) ? 'DESC' : 'ASC';

        $attributes["dbd_count"] = count($attributes["db_data"]);
        
        \LW\LW_Template::getTemplate('adm_templates', 'clubs_history_page',$attributes);
    }

    /**
     * Rend email form for send
     */
    public function emailInvitationSender(){

        $attributes["available_clubs"] = get_terms('clubs', array('fields' => 'id=>name'));

        $args = array(
            'meta_key'     => 'user_club',
            'meta_value'   => "",
            'order'        => 'ASC',
            'count_total'  => true,
        );
        $attributes["all_users"] = get_users( $args );

        $this->_sendInvitationEmail($_POST,'send_email_invitation');

        \LW\LW_Template::getTemplate('adm_templates', 'email_invitation_page_sender',$attributes);
    }

    /**
     * Rend email invitation settings
     */
    public function emailInvitationSettings(){

        $this->_saveEmailInvitationSettings($_POST,'update_email_settings');

        \LW\LW_Template::getTemplate('adm_templates', 'email_invitation_page_settings');
    }

    /**
     * Save default settings from email
     * @param $post
     * @param $submit
     */
    private function _saveEmailInvitationSettings($post,$submit){
        if(isset($post[$submit])){
            unset($post[$submit]);
            foreach ($post as $opt_key => $opt_value){
                if(get_option($opt_key) === false){
                    add_option($opt_key,trim($opt_value));
                }else{
                    update_option($opt_key,trim($opt_value));
                }
            }
        }
    }

    /**
     * Validate and send invitation email
     * @param $post
     * @param $submit
     * @return bool
     */
    private function _sendInvitationEmail($post,$submit){
        if(isset($post[$submit])){
            $from = $post['email_from'];

            $name = $post['email_from_name'];

            $subject = $post['email_subject'];

            $attributes['from'] = $from;

            $attributes['subject'] = $subject;

            $emails = null;

            $list = null;

            if( !empty($post['other_email']) ){
                $emails[][] = $post['other_email'];
            }
            if( !empty($post['clients_addresses'])){
                $emails[] = $post['clients_addresses'];
            }
            if(!empty($post['invitation_message_submit'])){
                $attributes['lw_content_message'] = stripslashes($post['invitation_message_submit']);
                $message = \LW\LW_Template::getTemplate('email_templates','mail_to_users',$attributes,false);
            }

            if(!$emails){
                return $emails;
            }
            foreach ($emails as $k => $mail){
                foreach ($mail as $m){
                    $list .= $m.',';
                }
            }

            $to = rtrim($list,',');
            $headers   = array();
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-type: text/html; charset=UTF-8";
            $headers[] = "From: {$name} <{$from}>";
            $headers[] = "Subject: {$subject}";
            $headers[] = "X-Mailer: PHP/".phpversion();

            $success = mail($to, $subject, $message, implode("\r\n", $headers));

            return $success;
        }
    }

    /**
     * Add some styles and scripts only in invitation screen
     */
    public function emailInvitationAssets(){
        $screen = get_current_screen();

        $allow_screens = array(
            'toplevel_page_email-invitation',
            'email-invitation_page_email-invitation-settings'
        );

        if(in_array($screen->id,$allow_screens)){
            wp_enqueue_script('email_invitation', LW_PLUGIN_URL.'additional/js/email_invitation_script.js', array('jquery'), '', true);
            wp_localize_script('email_invitation','ei',array(
                'base_url' => admin_url( 'admin-ajax.php' )
            ));
            wp_enqueue_style('email_invitation',LW_PLUGIN_URL.'additional/css/email_invitation_styles.css');
        }
    }

    /**
     * Send ajax data for query club users
     */
    public function ajaxGetUsersClubInfo(){
        if(isset($_POST['club_id'])){
            $filtered_post = filter_input(INPUT_POST,'club_id',FILTER_SANITIZE_NUMBER_INT);
            $club_request = ($filtered_post !== '' ) ? $_POST['club_id'] : '';
            $output = array();
            $args = array(
                'meta_key'     => 'user_club',
                'meta_value'   => $club_request,
                'order'        => 'ASC',
                'count_total'  => true,
            );
            $users = get_users( $args );

            foreach ($users as $key => $val){
                $output[] =  array('user_email' => $val->data->user_email,'user_name' => $val->data->display_name);
            }
            wp_send_json($output);
            die();
        }
    }

    /**
     * Render first page for Manage Club
     *
     */
    public function manageClubPageHome(){

        if(current_user_can('club_manager_options')){
            $attributes['date_status'] = \LW\CommerceConfig::clubsDateCloseOpenOrder();

            // action method after submit form
            $this->updateClubsMeta();

            $_list_table = new \LW\LW_WP_List_Members();

            $club = \LW\Settings::currentUser();

            $attributes['club_meta'] = $club["club_term_meta"];

            $attributes['club_id'] = $club['club_taxonomy']->term_id;

            $attributes['manager_data'] = $club["user_info"]["display_name"];

            $attributes['wellcome'] = $club['club_taxonomy']->name;

            // Prepare item for display method
            $_list_table->prepare_items();

            //Get output buffer for users list table and store in attribute
            ob_start();

            $_list_table->display();
            $attributes['display'] = ob_get_clean();
        }

        \LW\LW_Template::getTemplate('adm_templates', 'settings_club_page_home', $attributes);

    }

    /**
     * Remove user from club (removed users get club default)
     */
    public function managerRmUser(){
        if(isset($_POST['remove_user'])){
            $users_ids = $_POST['rm_user'];
            $dclub = get_terms('clubs', array('fields' => 'id=>slug'));

            foreach ($dclub as $k => $slug) {
                if (get_term_meta($k, 'default_club',true)) {
                    $default_club_id = $k;
                    break;
                }
            }

            if($users_ids){
                foreach ($users_ids as $user_id){
                    update_user_meta( $user_id, 'user_club', $default_club_id );
                }
            }
        }
    }

    /**
     * Rending Order Page and attributes
     *
     */
    public function manageClubOrdersPage(){
        $attributes['club_info'] = $club_info = \LW\Settings::currentUser();

	    $open_date = $club_info["club_term_meta"]["clubs_open_date"];
	    $close_date = $club_info["club_term_meta"]["clubs_close_date"];
	    $open_date = str_replace('-','/',$open_date);
	    $close_date = str_replace('-','/',$close_date);
	    $attributes["open_date"] = date("Y-m-d H:i:s", strtotime($open_date));
	    $attributes["close_date"] = date("Y-m-d H:i:s", strtotime($close_date));

	    $attributes["club_ID"] = $club_info["club_taxonomy"]->term_id;

        $attributes['orders_data'] = $this->getOrdersClub();

        $attributes['date_status'] = \LW\CommerceConfig::clubsDateCloseOpenOrder();

        $_list_order = new \LW\LW_WP_List_Orders();

        $attributes['ord'] = $this->sortOrdersForWPList();
        $_list_order->prepare_items($attributes['ord']);

        ob_start();
        $_list_order->display();
        $attributes['display'] = ob_get_clean();

        \LW\LW_Template::getTemplate('adm_templates', 'settings_club_page_orders',$attributes);

    }

    /**
     * @return array
     */
    public function sortOrdersForWPList(){
        $pre_order = $this->getOrdersClub();
        $weight_unit = get_option('woocommerce_weight_unit');
        $output = array();
        if($pre_order) {
	        foreach ( $pre_order as $orders ) {

		        foreach ( $orders as $product ) {
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
			        );
			        foreach ( $product['products'] as $key ) {

				        $ord[ $product["OrderID"] ][] = array(
					        'ID'           => $key['product_id'],
					        'QTY'          => $key['qty'],
					        'Name'         => $key['name'],
					        'total'        => $key['line_total'],
					        'variation_id' => $key['variation_id'],
				        );
			        }
		        }
	        }

	        foreach ( $orderID as $order ) {
		        $output[] = array(
			        'display_name' => $order['UserName'],
			        'order_date'   => $order['OrderDate'],
			        'user_phone'   => $order['UserPhone'],
			        'user_email'   => $order['UserEmail'],
			        'order_id'     => $order['OrderID'],
			        'order_status' => $order['PostStatus'],
			        'order_items'  => $this->mapArray( $ord[ $order['OrderID'] ], $order['Weight'], $weight_unit )
		        );
	        }

        }
        return $output;
    }

    /**
     * Format products name, quantity, lbs
     * @param $value1
     * @param $value2
     * @param $value3
     * @return string
     */
    public function mapArray($value1,$value2,$value3){
        
        $output = '';
        foreach ($value1 as $key){
            
            $d = '';
	        $variation = wc_get_product($key['variation_id']);
            $prod = wc_get_product($key['ID']);
                
	        if( $variation ){
		        $variation_attributes = $variation->get_variation_attributes();
		        $variation_name = ' | '.reset($variation_attributes);
                        $d .= $variation->weight;
	        }else{
                    $variation_name = '';
                }
                if(! $variation->weight ){
                    $d .= $prod->weight;
                }
                
                
                $d =  $key['QTY'];

            $output .= '<b>'. $key['Name'] .'</b>'. $variation_name .' | '.trim($d,',').'<br/>'."\n";
        }

        return $output;
    }

	/**
	 * @return array
	 */
    public function getOrdersClub(){

	    $prod = array();

        if( ! current_user_can('administrator') ){
            
            $user_club = \LW\Settings::currentUser();

            $club_data = $user_club["club_term_meta"];
            $discount = $user_club["club_term_meta"]["clubs_discount_p"];

            $date_after = \LW\Settings::getDateFormat('this_mysql_date',array(
                'date' => $user_club["club_term_meta"]["clubs_open_date"]
            ));

	        $date_before = \LW\Settings::getDateFormat('this_mysql_date',array(
		        'date' => $user_club["club_term_meta"]["clubs_close_date"]
	        ));

            $order_status = 'any';

            $filters = array(
                'post_type' => 'shop_order',
                'post_status' => $order_status,
                'posts_per_page' => -1,
                'meta_key' => 'clubs-slug',
                'meta_compare' => '=',
                'meta_value' => $user_club["club_taxonomy"]->slug,
                'orderby' => 'date',
                'order' => 'DESC',
                'date_query' => array(
                    array(
                        'column' => 'post_date',
                        'after'     => $date_after,
                        'before'    => $date_before,
                    )
                )
            );

            $loop = new \WP_Query($filters);

            $i = 0;

            $tot = 0;
            $weight = array();
	        $total_discounted = 0;
	        $total_qty = 0;
	        $total_w = 0;
	        $shipping_total = 0;

            while ($loop->have_posts()) {
                $loop->the_post();
                $productWeight = array();

                $order = wc_get_order($loop->post->ID);
                $order_id = $order->get_id();
                $items = $order->get_items();
                $date = new \DateTime($order->order_date);

                $a=array();
                foreach ($items as $item) {

                    $product_variation_id = $item['variation_id'];

                    if ($product_variation_id) {
                        $product = wc_get_product($item['variation_id']);
	                    $weight[] = $product->weight;
	                    $pw = $product->get_weight();
	                    $chefs_percent = get_post_meta($order_id,'order_variation_chef_prc_'.$item['variation_id'],true);
	                    if( $product->weight !== ''){
		                    $total_w += $product->weight * $item['qty'];
		                    $pw = $product->get_weight();
	                    }else{
	                    	$parent = wc_get_product($product->post->post_parent);
		                    $total_w += $parent->weight * $item['qty'];
		                    $pw = $product->get_weight();
	                    }
                    } else {
                    	if($item['product_id'] !== 0){
		                    $product = wc_get_product($item['product_id']);
		                    $weight[] = $product->get_weight();
		                    $chefs_percent = get_post_meta($order_id,'order_product_chef_prc_'.$item['product_id'],true);
		                    $total_w += $product->get_weight() * $item['qty'];
		                    $pw = $product->get_weight();
	                    }
                    }

                    $total_qty += $item['qty'];

                    $tot = $product->get_price() * $item['qty'];

                    $discount_chefs = ( empty($chefs_percent) ) ? $discount : $chefs_percent + $discount;

                    $dis = $tot * $discount_chefs / 100;

                    $total_discounted += $tot - $dis;
					if($item['product_id'] !== 0){
						$products = wc_get_product($item['product_id']);
					}

                    if($item["variation_id"] !=='0') {
                        $productWeight = get_post_meta($item["variation_id"]);

                        if($productWeight["_weight"] !== NULL) {
                            $a['ORDER-' . $order_id][] = array($item["variation_id"]=>$productWeight["_weight"], 'shipWeight'=>'');
                        }else{
                            $a['ORDER-' . $order_id][] = array('empty'=>'0', 'shipWeight'=>$products->get_weight());
                        }

                        $i = $i + 1;
                    }else{
                        $a['ORDER-' . $order_id][] = array('empty'=>'0', 'shipWeight'=>$products->get_weight());;
                    }

	                if ( $order->get_shipping_total() != 0){
		                $shipping_total += $order->get_shipping_total();
	                }else{
		                $shipping_total += $user_club["club_term_meta"]["clubs_ship_price"] * ($product->get_weight() * $item['qty']);
	                }

                }

                $i = 0;

                $prod['order_items'][] = array(
                    'OrderID' => $order_id,
                    'OrderDate' => $date->format('m-d-Y'),
                    'UserName'=>$order->billing_first_name .' '.$order->billing_last_name,
                    'UserPhone'=> $order->billing_phone,
                    'UserEmail'=>$order->billing_email,
                    'CurrencySymbol' => get_woocommerce_currency_symbol(),
                    'OrdersCount' => $loop->post_count,
                    'products' => $order->get_items(),
                    'weight' => $a,
                    'raw_price' => $tot,
                    'item_discount' => $dis,
                    'total_discount' => $total_discounted,
                    'post_status' => str_ireplace('wc-','',$order->post->post_status),
                    'shipping_price' => $user_club["club_term_meta"]["clubs_ship_price"],
                    'new_weight_product' => $weight,
                    'total_weight' => $total_w,
                    'total_qty' => $total_qty,
                    'shipping_total' => $shipping_total
                );
                $tot = '';
            }
        }
        return $prod;
        
    }

    /**
     * Generate PDF by Filter Orders
     */
    public function pdfGenerator(){

        if (defined( 'DOING_AJAX' ) && DOING_AJAX) {

          return;
        }

        $user_club = \LW\Settings::currentUser();

        $attributes['orders_data'] = $this->getOrdersClub();

        $attributes['club_percent'] = $user_club["club_term_meta"]["clubs_discount_p"];

        if($attributes['orders_data']) {
            $date = date('m-d-Y');

            include_once(LW_PLUGIN_DIR . 'LwApp/htmltable/html_table.php');
            $pdf = new \PDF_HTML_Table('L');
            $pdf->AddPage('L','A4');
            $pdf->SetTitle('Lummi');
            $pdf->SetAuthor('Lummi Wild');
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetMargins(5,5,0);

            if (isset($_POST['filter_date'])) {
                if ($_POST['pdf_create']) {
                    ob_start();
                    include_once(LW_PLUGIN_DIR . 'LwApp/temp_pdf/pdftemper.php');
                    $out = ob_get_clean();
                    $pdf->WriteHTML($out,array(38,23,25,50,14,21,115),array(
                    	6 => array( 'font-family' => 'Arial','font-size' => '9', 'font-weight' => '')
	                    )
                    );
                    $pdf->Output($user_club["club_taxonomy"]->slug . '-' . $date . '.pdf', 'I');
                    exit;
                }
            }
        }
    }

    /**
     * Packing Slip PDF
     */
    public function packingSlipPdf(){

        if(isset($_POST['packing_slip']) || (isset($_GET['section']) && $_GET['section'] == 'pdf')){
            include_once(LW_PLUGIN_DIR . 'LwApp/htmltable/html2pdf.php');
            $pdf = new \PDF_HTML();
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetTitle('Lummi');
            $pdf->SetAuthor('Lummi Wild');
            $pdf->SetFont('Arial', '', 14);
            ob_start();
            include_once(LW_PLUGIN_DIR . 'LwApp/temp_pdf/packing_slip_temp.php');
            $out = ob_get_clean();
            
            $user = \LW\Settings::currentUser();
            
            $club_slug = get_term($_GET["tag_ID"]);
            
	        $filename = ( $user["club_taxonomy"]->slug ) ? $user["club_taxonomy"]->slug : $club_slug->slug;
            
            $pdf->WriteHTML($out,array(38,38,38,38,38,85,34));
            $pdf->Output($filename . '-' . $date = date('m-d-Y') . '.pdf', 'I');
            exit;
        }
    }
    
    /**
     * Invoices List PDF
     * @param array $history_date Date from club history list
     */
    public function invoiceListPdf()
    {
        if(isset($_POST['invoices_list']) || ( isset( $_GET['section'] ) && $_GET['section'] == 'invoice' ) ) {
                       
            if ( current_user_can('administrator') || ( $_GET['taxonomy'] == 'clubs' && ! empty( $_GET['tag_ID'] ) ) ) {
                
                $club_id = ( $_GET['tag_ID'] ) ? $_GET['tag_ID'] : $_GET['club_id'];
                
                $user["club_taxonomy"] = get_term_by('id', $club_id, 'clubs');
                $meta = get_term_meta($club_id);
                $manager_id = absint($meta["clubs_manager_id"][0]);
                $manager_meta = get_user_meta($manager_id);
                $manager = get_user_by('id',$manager_id);
                $club_data["clubs_address"] = $meta["clubs_address"][0];
                $club_data["clubs_ship_price"] = $meta["clubs_ship_price"][0];
                $club_country = $manager_meta["billing_country"][0];
                $club_name = iconv('UTF-8', 'windows-1252', html_entity_decode($user["club_taxonomy"]->name));
                $club_owner = $manager->data->display_name;
                $club_phone = $meta["clubs_phone"][0];
                $discount = $meta["clubs_discount_p"][0];
                $date_after = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $meta["clubs_open_date"][0]));
                $date_before = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $meta["clubs_close_date"][0]));
            }
            else 
            {
                $user = \LW\Settings::currentUser();
                $club_data = $user["club_term_meta"];
                $club_name = iconv('UTF-8', 'windows-1252', html_entity_decode($user["club_taxonomy"]->name));
                $club_owner = $user["user_info"]["display_name"];
                $club_country = $user["user_meta"]["billing_country"];
                $club_phone = $user["club_term_meta"]["clubs_phone"];
                $discount = $user["club_term_meta"]["clubs_discount_p"];
                $date_after = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $user["club_term_meta"]["clubs_open_date"]));
                $date_before = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $user["club_term_meta"]["clubs_close_date"]));
            }
            
            $currency = iconv('UTF-8', 'windows-1252', html_entity_decode(get_woocommerce_currency_symbol()));
            
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

            $loop = new \WP_Query($filters);
            $tot = 0;
            $weight = 0;
	        $new_dis = (float)0;
            if ($loop->post_count > 0) {
                include_once(LW_PLUGIN_DIR . 'LwApp/htmltable/html2pdf.php');
                $pdf = new \PDF_HTML();
                $pdf->AliasNbPages();
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTitle('Lummi');
                $pdf->SetAuthor('Lummi Wild');
                $grey = '169,169,169';
                $black = '0,0,0';

                while ($loop->have_posts()) {
                    $loop->the_post();
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', '', 11);
                    $pdf->Cell(0,5,'ORDER INVOICE',0,0,'L');
                    $pdf->Ln(10);

                    $order = wc_get_order($loop->post->ID);
					$order_id = $order->get_id();
                    $items = $order->get_items();

                    $od = date_create($order->order_date);
                    $order_date = $od->format('m-d-Y');

                    $pdf->Cell(50, 5, 'Order ID: ' . $order_id, 0, 0, 'L');
                    $pdf->Ln(6);
                    $pdf->Cell(60, 5, 'Order Date: ' . $order_date, 0, 0, 'L');
                    $pdf->Ln(10);
					if ( $club_name != 'Public' ) {
						$pdf->Cell(90, 6, 'Billing Address', 'BR', 0, 'L');
						$pdf->Cell(90, 6, 'Shipping Address', 'B', 0, 'R');
						$pdf->Ln(8);
						$pdf->Cell(90, 5, $order->billing_first_name . ' ' . $order->billing_last_name, 'R', 0, 'L');
						$pdf->Cell(90, 5, 'Club: ' . $club_name, 0, 0, 'R');
						$pdf->Ln(6);
						$pdf->Cell(90, 5, $order->billing_address_1, 'R', 0, 'L');
						$pdf->Cell(90, 5, $club_owner, 0, 0, 'R');
						$pdf->Ln(6);
						$pdf->Cell(90, 5, $order->billing_city . ', ' . $order->billing_postcode . ', ' . $order->billing_country, 'R', 0, 'L');
						$pdf->Cell(90, 5, $club_data["clubs_address"], 0, 0, 'R');
						$pdf->Ln(6);
						$pdf->Cell(90, 5, $order->billing_phone, 'R', 0, 'L');
						$pdf->Cell(90, 5, $club_country, 0, 0, 'R');
						$pdf->Ln(6);
						$pdf->Cell(90, 5, '', 'BR', 0, 'L');
						$pdf->Cell(90, 5, $club_phone, 'B', 0, 'R');
					} else {
						$pdf->Cell(90, 6, 'Billing Address', 'BR', 0, 'L');
						$pdf->Cell(90, 6, 'Shipping Address', 'B', 0, 'R');
						$pdf->Ln(8);
						$pdf->Cell(90, 5, $order->billing_first_name . ' ' . $order->billing_last_name, 'R', 0, 'L');
						$pdf->Cell(90, 5, $order->shipping_first_name . ' ' . $order->shipping_last_name, 0, 0, 'R');
						$pdf->Ln(6);
						$pdf->Cell(90, 5, $order->billing_address_1, 'R', 0, 'L');
						$pdf->Cell(90, 5, $order->shipping_address_1, 0, 0, 'R');
						$pdf->Ln(6);
						$pdf->Cell(90, 5, $order->billing_city . ', ' . $order->billing_postcode . ', ' . $order->billing_country, 'R', 0, 'L');
						$pdf->Cell(90, 5, $order->shipping_city . ', ' . $order->shipping_postcode . ', ' . $order->shipping_country, 0, 0, 'R');
						$pdf->Ln(6);
						$pdf->Cell(90, 5, $order->billing_phone, 'R', 0, 'L');
						$pdf->Cell(90, 5, '', 0, 0, 'R');
						$pdf->Ln(6);
						$pdf->Cell(90, 5, '', 'BR', 0, 'L');
						$pdf->Cell(90, 5, '', 'B', 0, 'R');
					}
                    $pdf->Ln(12);
                    $pdf->SetTextColor($grey);
                    $pdf->Cell(90, 5, 'Product:', 0, 0, 'L');
                    $pdf->Cell(30, 5, 'QTY:', 0, 0, 'L');
                    $pdf->Cell(30, 5, 'Price:', 0, 0, 'L');
                    $pdf->Cell(30, 5, 'Line Total:', 0, 0, 'L');
                    $pdf->Ln(10);
                    $pdf->SetTextColor($black);

                    foreach ($items as $item) {

                        $product_variation_id = $item['variation_id'];

                        if ($product_variation_id) {
                            $product = wc_get_product($item['variation_id']);
	                        $chefs_percent = get_post_meta($order_id,'order_variation_chef_prc_'.$item['variation_id'],true);
	                        if($product->get_weight() !== ''){
		                        $weight += $product->get_weight() * $item['qty'];
	                        }else{
	                        	$parent = wc_get_product($product->post->post_parent);
		                        $weight += $parent->get_weight() * $item['qty'];
	                        }
                        } else {
                        	if($item['product_id'] !== 0){
		                        $product = wc_get_product($item['product_id']);
		                        $chefs_percent = get_post_meta($order_id,'order_product_chef_prc_'.$item['product_id'],true);
		                        $weight += $product->get_weight() * $item['qty'];
	                        }
                        }

	                    $discount_chefs = ( empty( $chefs_percent ) ) ? $discount : $chefs_percent + $discount;


                        if ( $order_id ) {
                            $tot += $product->get_price() * $item['qty'];
	                        $new_dis += $product->get_price() * $item['qty'] * $discount_chefs / 100;
	                        $total_discounted = $tot - $new_dis;
	                        if( $order->get_shipping_total() != 0){
		                        $shipping = $order->get_shipping_total();
	                        }else{
		                        $shipping = $club_data["clubs_ship_price"] * $weight;
	                        }
                            $grand_total = $total_discounted + $shipping;
                            $pr_sku = $product->get_sku();
                        }

                        $item_len = strlen($item['name']);
                        if($item_len > 20){
                            $pdf->SetFont('Arial', '', 10);
                        }

                        $pdf->Cell(90, 5, $item['name'] .' (lot num.: '. $pr_sku .')', 0, 0, 'L');
                        $pdf->SetFont('Arial', '', 11);
                        //$pdf->SetXY($x + 90, $y);
                        $pdf->Cell(30, 5, $item['qty'], 0, 0, 'L');
                        $pdf->Cell(30, 5, $currency . number_format(floatval($product->price),2), 0, 0, 'L');
                        $pdf->Cell(30, 5, $currency . number_format($product->price * $item['qty'],2), 0, 0, 'L');
                        $pdf->Ln(10);
                    }
                    $pdf->Cell(180, 5, '', 'B', 0, 'R');
                    $pdf->Ln(6);
                    $pdf->Cell(150, 5, 'Total:', 'B', 0, 'R');
                    $pdf->Cell(30, 5, $currency . number_format($tot,2), 'B', 0, 'L');
                    $pdf->Ln(6);
                    $pdf->Cell(150, 5, $discount . '% Club Discount:', 'B', 0, 'R');
                    $pdf->Cell(30, 5, $currency . number_format($new_dis,2), 'B', 0, 'L');
                    $pdf->Ln(6);
                    $pdf->Cell(150, 5, 'Discounted Total:', 'B', 0, 'R');
                    $pdf->Cell(30, 5, $currency . number_format($total_discounted,2), 'B', 0, 'L');
                    $pdf->Ln(6);
                    $pdf->Cell(150, 5, 'Shipping:', 'B', 0, 'R');
                    $pdf->Cell(30, 5, $currency . number_format($shipping,2), 'B', 0, 'L');
                    $pdf->Ln(6);
                    $pdf->Cell(150, 5, 'Total:', 'B', 0, 'R');
                    $pdf->Cell(30, 5, $currency . number_format($grand_total,2), 'B', 0, 'L');
                    $weight = '';
                    $tot = '';
	                $new_dis = '';
                }
                
                $pdf->Output($user["club_taxonomy"]->slug . '-' . date('m-d-Y') . '.pdf', 'I');
                exit;
            }else{
                echo '<h1 style="text-align: center;">No Orders</h1>';
            }
        }
    }

	/**
	 * Sort data for CSV
	 * @param $array
	 *
	 * @return null|string
	 */
	private function _userData2Csv($array)
	{
		if (count($array) == 0) {
			return null;
		}
		ob_start();
		$df = fopen("php://output", 'w');
		foreach ($array as $row) {
			fputcsv($df, $row);
		}
		fclose($df);

		return ob_get_clean();
	}

	/**
	 * Headers for CSV force-download
	 * @param $filename
	 */
	private function _userDownloadCsvEmails($filename) {
		// disable caching
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: 0");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");
	}

	/**
	 * Get user emails ( sorted by: with orders and all )
	 */
	public function getUserCsvEmails(){

		if(current_user_can('club_manager_options')){ // condittion added because this was running for all users and slowing the admin page load to a crawl RAH
		
			$user_orders = array();
			$club_members = \LW\Settings::getCurrentClubMembersByManager('user_email,display_name,user_id');
			$club_manager = \LW\Settings::currentUser();
			$club_id = $club_manager["club_taxonomy"]->term_id;
			$open_date = get_term_meta($club_id,'clubs_open_date', true);
			$close_date = get_term_meta($club_id,'clubs_close_date', true);
			$open_date = str_replace('-','/',substr($open_date, 0, strpos($open_date, ' ')));
			$close_date = str_replace('-','/',substr($close_date, 0, strpos($close_date, ' ')));
			$open_date = date_parse(date("Y-m-d", strtotime($open_date)));
			$close_date = date_parse(date("Y-m-d", strtotime($close_date)));

			foreach ($club_members as $user){

				$this->_csv_emails_all_users[$user["user_id"]] = $user;
				$this->_csv_emails_all_users_meta[$user["user_id"]]['first_name'] = get_user_meta( $user["user_id"],'first_name',true );
				$this->_csv_emails_all_users_meta[$user["user_id"]]['last_name'] = get_user_meta( $user["user_id"],'last_name',true );
				$this->_csv_emails_all_users_meta[$user["user_id"]]['email'] = $user["user_email"];

				$user_orders[$user["user_id"]] = get_posts( array(
					'post_type'   => array( 'shop_order' ),
					'post_status' => array( 'any' ),
					'posts_per_page' => 1,
					'meta_key'     => '_customer_user',
					'meta_value'   => $user["user_id"],
					'meta_compare' => '=',
					'date_query' => array(
						array(
							'after'  => $open_date['year'] .'-'.$open_date['month'].'-'.$open_date['day'],
							'before' => $close_date['year'] .'-'.$close_date['month'].'-'.$close_date['day'],
							'compare'   => 'BETWEEN',
						)
					)
				) );
			}

			foreach ($user_orders as $k => $v){
				if( count($v) > 0){
					$this->_csv_emails_order_users[$k] = $this->_csv_emails_all_users[$k];
					$this->_csv_emails_order_users_meta[$k] = $this->_csv_emails_all_users_meta[$k];
				}
			}

			if( isset($_POST['ae_csv']) && count($this->_csv_emails_all_users_meta) > 0){
				$this->_userDownloadCsvEmails($club_manager["club_taxonomy"]->slug.'-all-users_' . date("Y-m-d") . ".csv");
				echo $this->_userData2Csv($this->_csv_emails_all_users_meta);
				die();
			}

			if( isset($_POST['aeo_csv']) && count($this->_csv_emails_order_users_meta) > 0){
				$this->_userDownloadCsvEmails($club_manager["club_taxonomy"]->slug.'-ordered-users_' . date("Y-m-d") . ".csv");
				echo $this->_userData2Csv($this->_csv_emails_order_users_meta);
				die();
			}

		}
	}

    /**
     * Rending Email form and attributes
     *
     */
    public function manageClubPageEmail(){

        $attributes['title'] = 'Club Emails';

	    $attributes['all_users'] = $this->_csv_emails_all_users;
	    $attributes['ordered_users'] = $this->_csv_emails_order_users;

        \LW\LW_Template::getTemplate('adm_templates','settings_club_page_email',$attributes);

    }

    /**
     * Validate field and send email to users
     *
     * @return bool
     */
    public function sendEmail($post,$submit){
        if(isset($post[$submit])){

            $user = \LW\Settings::currentUser();

            $from = $user['user_info']["user_email"];

            $name = $user['user_info']['display_name'];

            $attributes['from'] = $from;

            $attributes['subject'] = $_POST['email_subject'];

            $emails = null;

            $list = null;

            if( !empty($_POST['send_to_all'])){
                $emails = \LW\Settings::getCurrentClubMembersByManager('user_email','ARRAY_N');
            }
            if( !empty($_POST['other_email']) ){
                $emails[][] = $_POST['other_email'];
            }
            if( !empty($_POST['clients_addresses']) && empty($_POST['send_to_all'])){
                $emails[] = $_POST['clients_addresses'];
            }
            if(!empty($_POST['lw_content_message'])){
                $attributes['lw_content_message'] = stripslashes($_POST['lw_content_message']);
                $message = \LW\LW_Template::getTemplate('email_templates','mail_to_users',$attributes,false);
            }

            if(!$emails){
                return $emails;
            }
                foreach ($emails as $k => $mail){
                    foreach ($mail as $m){
                        $list .= $m.',';
                    }
                }

	        $email_list = rtrim($list,',');
	        $to = null;
	        $headers   = array();
	        $headers[] = "MIME-Version: 1.0";
	        $headers[] = "Content-type: text/html; charset=UTF-8";
	        $headers[] = "From: {$name} <{$from}>";
	        $headers[] = "Subject: {$attributes['subject']}";
	        $headers[] = "X-Mailer: PHP/".phpversion();
	        $headers[] .= "Bcc: $email_list";

	        $success = mail($to, $attributes['subject'], $message, implode("\r\n", $headers));

            return $success;
        }
    }

	/**
	 * Export Emails
	 */
	public function exportEmails(){

		if( isset( $_POST['exporter'] ) ){

				if( current_user_can('administrator') ){

					$club_id = ( ! empty( $_POST['choice_club'] ) && $_POST['choice_club'] !== 'all' ) ? $_POST['choice_club'] : '';
					$club_name = ( ! empty($_POST['choice_club_slug']) ) ? $_POST['choice_club_slug'] : 'All_Clubs';
					$args = array(
						'meta_key'     => 'user_club',
						'meta_value'   => $club_id,
						'order'        => 'ASC',
						'count_total'  => true,
					);
					$users = get_users( $args );

					foreach ($users as $uv){
						$att[]["user_email"] = $uv->data->user_email;
					}

				}else{
					$att = \LW\Settings::getCurrentClubMembersByManager('user_email');
					$user = \LW\Settings::currentUser();
					$club = $user["club_taxonomy"];
				}

			$data = '';
                        if( ! empty( $att ) ){
                            
                            if( $_POST['type_export'] === 'comma' ){
                                    foreach ( $att as $email ){
                                            $data .=  $email["user_email"] . ',';
                                    }
                                    $file_end = '-emails-list-comma_sep';
                            }

                            if( $_POST['type_export'] === 'per_line' ){
                                    foreach ( $att as $email ){
                                            $data .=  $email["user_email"] . "\r\n";
                                    }
                                    $file_end = '-emails-list-one-per-line';
                            }
                        }
                        
			if( ! $club->slug ){
				$filename = $club_name;
			}else{
				$filename = $club->slug;
			}
                        
			if( ! empty( $att ) ){
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.$filename.$file_end.'.txt');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . strlen($data));
				echo rtrim($data,',');
				exit;
			}
		}
	}

    /**
     * Update custom meta for club
     *
     * If they add new fields and their names
     * DO NOT begin with the keyword " clubs " followed by an UNDERSCORE
     * they will be ignored and will not be updated!
     *
     */
    public function updateClubsMeta(){

        if(isset($_POST['save_club']))
        {
            $term = \LW\Settings::currentUser();
            foreach ($_POST as $k => $v) {
                $exp = explode('_', $k);
                if ($exp[0] == 'clubs') {
                    update_term_meta($term["club_taxonomy"]->term_id, $k, $v);
                }
                if($exp[0] == 'newmember'){

                    $count = count($v);

                    for ($i=0; $i < $count; $i++){
                        update_user_meta( absint($v[$i]),'user_club',$term["club_taxonomy"]->term_id);
                    }
                }
            }
        }
    }

}