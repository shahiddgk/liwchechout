<?php

namespace LW;

class ClubsTaxonomies
{

    public function __construct()
    {
    }

    /**
     * Apply actions and filters for taxonomies
     *
     */
    public function applyTaxonomies(){
        add_action( 'init', array($this, 'clubsTaxonomies'), 4 );
        add_action( 'clubs_add_form_fields', array($this,'clubsTaxonomiesFields'), 10, 2 );
        add_action( 'create_clubs', array($this,'saveClubsMeta'), 10, 2 );
        add_action( 'clubs_edit_form_fields', array($this,'renderEditFormsClubs'), 10, 2 );
        add_action( 'edited_clubs', array($this,'updateClubsMeta'), 10, 2 );
        add_action('delete_clubs',array($this,'del_tax'),10,2);
        add_filter( 'manage_edit-clubs_columns', array($this,'addClubsGroupColumn') );
        add_filter( 'manage_clubs_custom_column', array($this,'addClubsGroupColumnContent'), 10, 3 );
        add_filter( 'manage_edit-clubs_sortable_columns', array($this,'addClubsGroupColumnSortable') );
        add_action('admin_menu', array($this,'moveClubsMenu'));
        add_action('admin_footer',array($this,'rendOrderTaxonomyClubs'),999);
        add_action('admin_init',array($this,'pdfGeneratorInTax'),20);
        add_action('admin_enqueue_scripts',array($this,'taxonomyClubsStyles'),20);
		add_action( 'wp_ajax_chefs_save_options', array($this,'applyChefsClub'),30);
        add_action('wp_ajax_club_set_period_once',array($this,'setFirstClubPeriod'),30);
        add_action('wp_ajax_club_delete_period',array($this,'deleteClubPeriod'),40);
    }
    
    /**
     * Delete clubs period if club is deleted
     * @global \LW\type $wpdb
     * @param type $term_id
     * @param type $tt
     */
    function del_tax($term_id,$tt){
        global $wpdb;
        
        $wpdb->delete( $wpdb->prefix.'clubs_history', array( 'club_id' => (int)$term_id ), array( '%d' ) );
    }


    /**
     * Move menu link 'Clubs' to top if user is administrator
     *
     * @return array
     */
    function moveClubsMenu() {
        global $menu, $submenu;
        if(is_admin() && !current_user_can('club_manager_options')){
			if($submenu["edit.php?post_type=product"][15]){
				$clubs = $submenu["edit.php?post_type=product"][15];
				unset($submenu["edit.php?post_type=product"][15]);
				array_unshift($submenu["edit.php?post_type=product"], $clubs);
			}
        }
        return $submenu;
    }
    
    /**
     * Register new taxonomies
     *
     */
    public function clubsTaxonomies(){
//        global $menu, $submenu;
        $labels = array(
            'name'              => _x( 'Clubs', 'taxonomy general name','lummi-wild-se' ),
            'singular_name'     => _x( 'Club', 'taxonomy singular name','lummi-wild-se' ),
            'search_items'      => __( 'Search Clubs','lummi-wild-se' ),
            'all_items'         => __( 'All Clubs','lummi-wild-se' ),
            'parent_item'       => __( 'Parent Club','lummi-wild-se' ),
            'parent_item_colon' => __( 'Parent Club:','lummi-wild-se' ),
            'edit_item'         => __( 'Edit Club','lummi-wild-se' ),
            'update_item'       => __( 'Update Club','lummi-wild-se' ),
            'add_new_item'      => __( 'Add New Club','lummi-wild-se' ),
            'new_item_name'     => __( 'New Club Name','lummi-wild-se' ),
            'menu_name'         => __( 'Clubs','lummi-wild-se' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'clubs' ),
            'sort'              => true,
        );

        register_taxonomy('clubs', array('product'), $args);
    }
    
    /**
     * Rending taxonomy field in create club
     */
    public function clubsTaxonomiesFields($taxonomy){
        LW_Template::getTemplate( 'adm_templates','taxonomy_save_fields');
    }

    /**
    * Save custom meta.
    *
    * If they add new fields and their names
    * DO NOT begin with the keyword " clubs " followed by an UNDERSCORE
    * they will be ignored and will not be added to the database!
    *
    * @param $term_id
    * @param $tt_id
    */
    public function saveClubsMeta( $term_id, $tt_id ){
        
        if(isset($_POST['tag-name'])){
           
            foreach ($_POST as $k => $v){
                $exp = explode('_',$k);
                if($exp[0] == 'clubs'){
                    add_term_meta($term_id,$k,wp_kses_post($v),true);
                }
            }
            $invitation_link = home_url('lwu-register/?club='.$_POST['slug']);
            if($_POST['slug'] && $invitation_link){
                add_term_meta($term_id,'invitation_link',$invitation_link,true);
            }
            if(!empty($_POST['clubs_manager_id'])){
                $user = get_user_by('ID',$_POST['clubs_manager_id']);
                update_user_meta( $user->ID, 'user_club', $term_id );
            }
            if(!empty($_POST['default_club'])){
                    $dclub = get_terms('clubs',array('fields'=>'ids'));
                    foreach ($dclub as $id){
                        if(get_term_meta($id,'default_club',true)){
                            delete_term_meta($id,'default_club','default_club');
                        }
                    }
                    add_term_meta($term_id,'default_club',$_POST['default_club'],true);
            }
            
            $this->initPeriod($term_id, $_POST);
        }
    }
    
    private function initPeriod($term_id,$post_data){
        
        if($post_data["action"] === 'add-tag'){
            
            $gen_slug = strtolower( preg_replace( '/\s+/', '-', $post_data['tag-name'] ) );
           
            global $wpdb;
            
            $flag = false;
            
            $now_timestamp = current_time( 'timestamp', 0 );
            $date_create = date('m-d-Y h:i a',$now_timestamp);
            
            if( empty($post_data["clubs_open_date"]) &&  empty($post_data["clubs_close_date"]) )
            {
                $flag = true;
            }
            
            if( empty($post_data['tag-name']) && empty($gen_slug)){
                $flag = true;
            }
            
            if( ! $flag ){
                
                $wpdb->insert($wpdb->prefix.'clubs_history', 
                            array(
                                'club_id' => $term_id, 
                                'club_name' => $post_data['tag-name'], 
                                'club_slug' => $gen_slug,
                                'open_date' => $post_data['clubs_open_date'],
                                'close_date' => $post_data['clubs_close_date'],
                                'create_date' => $date_create,
                                'update_date' => ''
                            ),
                            array('%d','%s','%s','%s','%s','%s','%s')
                );
            }
        }
    }

    /**
     *  Applying the template for editing taxonomies
     * and impart the necessary attributes in orderly array
     *
     * @param $term : term data for current taxonomy
     * @param $taxonomy
     */
    public function renderEditFormsClubs( $term, $taxonomy ){
        if($term){

            $term_meta = get_term_meta($term->term_id);

            $attributes = array_map(function($term_meta){
                $arr = array();
                foreach ($term_meta as $meta ){
                    $arr = $meta;
                }
                return $arr;
            },$term_meta);


            if( ! $attributes['invitation_link'] ){
                $attributes['invitation_link'] = home_url('lwu-register/?club='.$term->slug );
                update_term_meta($term->term_id,'invitation_link',$attributes['invitation_link'] );
            }

            $managers = get_users(array('role' => 'club-manager','fields' => array('ID','display_name')));
            if(count($managers) > 0){
                foreach ($managers as $v){
                    $attributes['all_managers'][$v->ID] = $v->display_name;
                }
            }
        }
        $attributes['club_members'] = \LW\Settings::getCurrentClubMembersByManager('user_email,display_name');

        \LW\LW_Template::getTemplate( 'adm_templates','taxonomy_update_fields',$attributes);
    }

    /**
     * Update custom meta
     *
     * If they add new fields and their names
     * DO NOT begin with the keyword CLUBS followed by an UNDERSCORE
     * they will be ignored and will not be updated!
     *
     * @param $term_id
     * @param $tt_id
     */
    public function updateClubsMeta( $term_id, $tt_id ){
        
        foreach ($_POST as $k => $v){
            $exp = explode('_',$k);
            if($exp[0] == 'clubs'){
                update_term_meta($term_id,$k,$v);
            }
        }
        
        if( $_POST['invitation_link'] || $_POST['invitation_link'] !== $_POST['hid-invitation_link'] ){
            update_term_meta($term_id,'invitation_link',$_POST['invitation_link']);
        }
        
        if(!empty($_POST['clubs_manager_id'])){
            $user = get_user_by('ID',$_POST['clubs_manager_id']);
            update_user_meta( $user->ID, 'user_club', $term_id );
        }
        if( ! empty($_POST['default_club'] ) ){
           $dclub = get_terms('clubs',array('fields'=>'ids'));
            foreach ($dclub as $id){
                 delete_term_meta(absint($id),'default_club','default_club');
            }
            update_term_meta($term_id,'default_club',$_POST['default_club']);
        }elseif(empty($_POST['default_club']) && get_term_meta($term_id,'default_club',true)){
            delete_term_meta($term_id,'default_club','default_club');
        }
        
        $this->setPeriod($term_id,$_POST);
    }
    
    
    /**
     * Delete rows from clubs history
     */
    public function deleteClubPeriod(){
        check_ajax_referer( 'delete_period_secure', 'club_delete_period_nonce');
        
        global $wpdb;
        $res = 0;
        foreach ($_POST as $k => $v){
            if( is_array($v) ){
                foreach ($v as $del_row){
                    if( ! empty($del_row) ){
//                        $num_row = $wpdb->delete( 
//                                $wpdb->prefix.'clubs_history', 
//                                array( 'id' => (int)$del_row ), 
//                                array( '%d' ) );
                    $num_row =$wpdb->update( 
                        $wpdb->prefix.'clubs_history',
                        array( 
                            'create_date' => null,
                            'update_date' => date('m-d-Y h:i a' , current_time('timestamp',0))
                        ), 
                        array( 'id' => $del_row), 
                        array( 
                            '%s'
                        ), 
                        array( '%d' ) 
                    );
                        $res += $num_row;
                    }
                }
            }
        }
        wp_send_json(array('num_rows' => $res));
        die();
    }

    /**
     * Ajax set first unsaved period
     */
    public function setFirstClubPeriod(){
        check_ajax_referer( 'set_period_secure', 'club_set_period_nonce');
        
        foreach ($_POST as $key => $val){
            
            if( trim($val) === '' ){
                wp_send_json_error(array('message' => 'Missing data!'));
                die();
            }
        }
        
        $create_date = current_time('m-d-Y h:i a', 0);
        $open_date = trim($_POST["open_date"]);
        $close_date = trim($_POST["close_date"]);
        $club_name = trim($_POST['club_name']);
        $club_slug = trim($_POST['club_slug']);
        $club_id = trim($_POST["club_id"]);
        
        global $wpdb;
            
        $operation = $wpdb->insert($wpdb->prefix.'clubs_history', 
                        array(
                            'club_id' => (int)$club_id,
                            'club_name' => $club_name, 
                            'club_slug' => $club_slug,
                            'open_date' => $open_date,
                            'close_date' => $close_date,
                            'create_date' => $create_date
                        ),
                        array('%d','%s','%s','%s','%s','%s')
                    );
        if( $operation === 1){
            wp_send_json_success(array('message' => 'Success!'));
            die();
        }
        wp_send_json_error(array('message' => 'Failed!'));
        die();
    }
    
    /**
     * Save new club period
     * @global type $wpdb
     * @param type $term_id
     * @param type $post_data
     * @return boolean
     */
    private function setPeriod($term_id,$post_data){
           
            if( empty( $post_data['clubs_open_date'] ) && empty( $post_data['clubs_close_date'] ) ){
                return false;
            }

            $open_timestamp = \LW\Settings::getDateFormat('this_timestamp',array('date' => $post_data['clubs_open_date']));
            $close_timestamp = \LW\Settings::getDateFormat('this_timestamp',array('date' => $post_data['clubs_close_date']));
            $hid_date_open = \LW\Settings::getDateFormat('this_timestamp',array('date' => $post_data['hidden_date_open']));
            $hid_date_close = \LW\Settings::getDateFormat('this_timestamp',array('date' => $post_data['hidden_date_close']));

            $now_timestamp = current_time( 'timestamp', 0 );
            $date_create = date('m-d-Y h:i a',$now_timestamp);
            
            $flag = false;
            
            if( empty( $hid_date_open ) && empty( $open_timestamp ) ){
                $flag =true;
            }
            
            if( $hid_date_open === $open_timestamp ){
                $flag =true;
            }
            
            if( $open_timestamp < $hid_date_open ){
                $flag =true;
            }
            
            global $wpdb;
            
            if( ! $flag ){
                
                $wpdb->insert($wpdb->prefix.'clubs_history', 
                            array(
                                'club_id' => $term_id, 
                                'club_name' => $post_data['name'], 
                                'club_slug' => $post_data['slug'],
                                'open_date' => $post_data['clubs_open_date'],
                                'close_date' => $post_data['clubs_close_date'],
                                'create_date' => $date_create
                            ),
                            array('%d','%s','%s','%s','%s','%s')
                );
            }
        
            if( $hid_date_open === $open_timestamp && ! empty( $post_data['current_id_period'] ) && ( $hid_date_close !== $close_timestamp ) ){

                $wpdb->update( 
                        $wpdb->prefix.'clubs_history',
                        array( 
                            'close_date' => $post_data['clubs_close_date'],
                            'update_date' => $date_create
                        ), 
                        array( 'id' => $post_data['current_id_period']), 
                        array( 
                            '%s',
                            '%s'
                        ), 
                        array( '%d' ) 
                );
            }
    }
    
    
    /**
    * Activate chefs clubs options via ajax method
    */
    public function applyChefsClub(){

	    check_ajax_referer( 'chefs_secure', 'chefs_nonce_club');

	    $arg = array(
	    	'post_type' => 'product',
		    'posts_per_page' => -1,
	    );

	    $chefs_id = $_POST["club_id"];
		$chefs_percent = $_POST["chefs_prc"];
		$chefs_active = (int)$_POST["chefs_active"];

		if( empty($chefs_percent) && $chefs_active === 1){
			wp_send_json(array('message' => '<span class="chef_done error">Error: Empty percent</span>' ));
			die();
		}

	    $query = new \WP_Query($arg);

	    foreach ($query->posts as $k => $v){
	    	if($chefs_active === 1){
			    update_post_meta( $v->ID, 'chefs_club_id_'.$chefs_id, $chefs_percent);
			    $message = '<span class="chef_done">The percent has been applied to the products</span>';
		    }
		    if($chefs_active === 0){
			    delete_post_meta( $v->ID, 'chefs_club_id_'.$chefs_id);
			    $message = '<span class="chef_done">Percent has been removed from the products</span>';
		    }
	    }
	    if( isset($chefs_active) ){
		    update_term_meta($chefs_id,'chefs_active',$chefs_active);
		    update_term_meta($chefs_id,'chefs_prc',$chefs_percent);
	    }

	    wp_reset_postdata();

	    $response = array(
		    'message'	=> $message
	    );

	    wp_send_json($response);
	    die();
    }

    /**
     * Set columns title for the new meta
     *
     * @param $columns
     * @return mixed
     */
    public function addClubsGroupColumn( $columns ){
        $catch_column_name = $columns['name'];
        unset($columns['name']);

        $columns['clubs_active'] = __( 'Active', 'lummi-wild-se' );
        $columns['name'] = $catch_column_name;
        $columns['clubs_manager_id'] = __( 'Club Manager', 'lummi-wild-se' );
        $columns['clubs_discount_p'] = __( 'Discount (%)', 'lummi-wild-se' );
        $columns['clubs_phone'] = __( 'Phone', 'lummi-wild-se' );
        $columns['clubs_email'] = __( 'Email', 'lummi-wild-se' );

        $columns['clubs_close_date'] = __( 'Close date', 'lummi-wild-se' );
        $columns['clubs_ship_date'] = __( 'Shipping Date', 'lummi-wild-se' );
        $columns['clubs_arrival_date'] = __( 'Arrival Date', 'lummi-wild-se' );
        $columns['clubs_notes'] = __( 'Notes', 'lummi-wild-se' );
        $columns['packing_slip'] = __( 'Packing Slip', 'lummi-wild-se');

        return $columns;
    }
    
    /**
     * Use in smartContentProcessing method
     * @param type $term_id
     * @return int
     */
    private function isActiveInClubsColumns($term_id){
        
        $date_now = current_time('timestamp',0);
        $status = 0;
        
        if(current_user_can('administrator') && ! empty($term_id) ){
            $o_date = get_term_meta($term_id,'clubs_open_date',true);
            $c_date = get_term_meta($term_id,'clubs_close_date',true);
            
            if( ! empty($c_date) && ! empty($o_date) ){
                
                $_open = \DateTime::createFromFormat( 'm-d-Y h:i a', $o_date );
                
                $_close = \DateTime::createFromFormat( 'm-d-Y h:i a', $c_date );
                
                if( $_open !== false && $_close !== false ){
                    
                    $open_date = strtotime($_open->format('Y-m-d h:i:s'));
                    $close_date = strtotime($_close->format('Y-m-d h:i:s'));
                    
                    if ( $date_now > $close_date || $date_now < $open_date ){
                        $status = 1;
                    }
                }
            }
        } 
        
        return $status;
    }

    /**
     * Format content from custom meta fields
     *
     * @param $content
     * @param $column_name
     * @param $term_id
     * @param array $wp_arg
     * @return string
     */
    private function smartContentProcessing($content, $column_name, $term_id, $wp_arg = array()){

        if( $column_name == $wp_arg[0] ){

            if($wp_arg[1] == 'get_user_by'){
                $var = $wp_arg[1]('ID', get_term_meta( absint( $term_id ), $wp_arg[0], true ));
            }
            elseif ($wp_arg[1] == 'get_term_meta'){
                $var = $wp_arg[1]( absint( $term_id ), $wp_arg[0], true );
            }
            elseif ($wp_arg[1] == 'getPackingSlipButton'){
                $var = $this->getPackingSlipButton($term_id);
            }

            if(array_key_exists('obj',$wp_arg[2])){
                if( !empty( $var->{$wp_arg[2]['obj']} ) ){
                    return $content .=  $var->{$wp_arg[2]['obj']};
                }
            }
            if (array_key_exists('str',$wp_arg[2])){
                if( $var && !empty( $var ) ){
                    return $content .= esc_attr( $var );
                }
            }
           
            if(array_key_exists('statement',$wp_arg[2])){
                $is_closed = $this->isActiveInClubsColumns($term_id);
              
                if($var == 'on' && $is_closed !== 1){
                    return $content .= '<span style="color: forestgreen; font-weight: bold">Active</span>';
                }
                else{
                    return $content .= esc_attr('Inactive');
                }
            }
            if (array_key_exists('link',$wp_arg[2])){
                if( $var && !empty( $var ) ){
                    return $content .=  $var ;
                }
            }
        }
    }

    /**
     * Packing slip submit form for clubs taxonomy list columns
     * @param $term_id
     * @return string
     */
    public function getPackingSlipButton($term_id){
        $club = get_term( $term_id, 'clubs' );

        $filters = array(
            'post_type' => 'shop_order',
            'post_status' => array('wc-processing','wc-completed'),
            'posts_per_page' => -1,
            'meta_key' => 'clubs-slug',
            'meta_compare' => '=',
            'meta_value' => $club->slug,
        );

        $loop = new \WP_Query($filters);
        if($loop->post_count > 0){
            return '<form method="POST" target="_blank"><input type="hidden" name="admin_tag_id" value="'.$term_id.'"><input class="button button-primary" type="submit" name="packing_slip" value="'.esc_attr('Get Packing Slip').'"></form>';
        }else{
            return 'No Orders';
        }
    }

    /**
     * Add content in the columns
     *
     * @param $content
     * @param $column_name
     * @param $term_id
     * @return string
     */
    public function addClubsGroupColumnContent( $content, $column_name, $term_id ){

        //TODO Add new field
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_manager_id','get_user_by',array('obj' => 'display_name')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_discount_p','get_term_meta',array('str' => '')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_active','get_term_meta',array('statement' => 'on')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_phone','get_term_meta',array('str' => '')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_email','get_term_meta',array('str' => '')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_close_date','get_term_meta',array('str' => '')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_ship_date','get_term_meta',array('str' => '')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_arrival_date','get_term_meta',array('str' => '')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('clubs_notes','get_term_meta',array('str' => '')));
        $content .= $this->smartContentProcessing($content, $column_name, $term_id,array('packing_slip','getPackingSlipButton',array('link' => '')));

        return $content;
    }

    /**
     * Apply sort of new metadata columns
     *
     * @param $sortable
     * @return mixed
     */
    public function addClubsGroupColumnSortable( $sortable ){
        if($_GET["taxonomy"]) {
            $taxonomy = sanitize_title($_GET['taxonomy']);
            $term = get_terms($taxonomy, array('hide_empty' => false, 'fields' => 'ids'));
            for ($i = 0; $i < count($term); $i++) {
                $meta = get_metadata('term', $term[$i]);
                if (!empty($meta)) {
                    foreach ($meta as $k => $v) {
                        $exp = explode('_', $k);
                        if ($exp[0] == 'clubs') {
                            $sortable[$k] = $k;
                        }
                    }
                    break;
                }
            }
            return $sortable;
        }
    }

	/**
	 * @return array
	 */
    public function getOrdersClubInTax(){

	    $prod = array();
		    if ( current_user_can('administrator') && isset($_GET["taxonomy"]) && $_GET["taxonomy"] == 'clubs' && !empty($_GET['tag_ID'])){

			    $meta_club = get_term($_GET["tag_ID"]);
			    $meta = get_term_meta($_GET["tag_ID"]);

			    $discount = $meta["clubs_discount_p"][0];

			    if($meta["clubs_open_date"][0]){
				    $date_after = \LW\Settings::getDateFormat('this_mysql_date',array(
					    'date' => $meta["clubs_open_date"][0]
				    ));
			    }else{
				    $date_after = '';
			    }


			    $date_before = current_time( 'mysql', 0 );
			    $order_status = 'any';

			    if(isset($_POST['filter_date_in_tax'])){

				    $date_after  = ( ! empty( $_POST['date_after'] ) ) ?
					    \LW\Settings::getDateFormat('this_mysql_date',
						    array('date' => $_POST['date_after'])) : $date_after;



				    $date_before  = ( ! empty( $_POST['date_before'] ) ) ?
					    \LW\Settings::getDateFormat('this_mysql_date',
						    array('date' => $_POST['date_before'])) : $date_before;



				    $order_status = (!empty($_POST['order_status']))? $_POST['order_status'] : 'any';
			    }

			    $filters = array(
				    'post_type' => 'shop_order',
				    'post_status' => $order_status,
				    'posts_per_page' => -1,
				    'meta_key' => 'clubs-slug',
				    'meta_compare' => '=',
				    'meta_value' => $meta_club->slug,
				    'orderby' => 'date',
				    'order' => 'DESC',
				    'date_query' => array(
					    array(
						    'column' => 'post_date',
						    'after'     => $date_after,
						    'before'    => $date_before)
				    )
			    );

			    $loop = new \WP_Query($filters);
			    $i = 0;

			    $tot = 0;
			    $weight = array();
			    $total_w = 0;
			    $total_qty = 0;
			    $total_discounted = 0;
			    $shipping_total = 0;

			    while ($loop->have_posts()) {
				    $loop->the_post();
				    $productWeight = array();

				    $order = wc_get_order($loop->post->ID);
				    $order_id = $order->get_id();
				    $items = $order->get_items();
				    $date = new \DateTime($order->order_date);

				    $a=array();
				    foreach ($items as $key) {

					    $product_variation_id = $key['variation_id'];

					    if ($product_variation_id) {
						    $product = wc_get_product($key['variation_id']);

						    $weight[] = $product->get_weight();
						    $chefs_percent = get_post_meta($order_id,'order_variation_chef_prc_'.$key['variation_id'],true);
						    if( $product->get_weight() !== ''){
							    $total_w += $product->weight * $key['qty'];
						    }else{
							    $parent = wc_get_product($product->post->post_parent);
							    $total_w += $parent->get_weight() * $key['qty'];
						    }
					    } else {
					    	if(($key['product_id']) !== 0){
							    $product = wc_get_product($key['product_id']);
							    $weight[] = ($product) ? $product->get_weight() : 0;
							    $chefs_percent = get_post_meta($order_id,'order_product_chef_prc_'.$key['product_id'],true);
							    $total_w += ($product) ? $product->get_weight() * $key['qty'] : 0;
						    }
					    }
					    $total_qty += $key['qty'];

					    $tot = $product->get_price() * $key['qty'];

					    $discount_chefs = ( empty($chefs_percent) ) ? $discount : $chefs_percent + $discount;

					    $dis = $tot * $discount_chefs / 100;

					    $total_discounted += $tot - $dis;
						if($key['product_id'] !== 0){
							$products = wc_get_product($key['product_id']);
						}
					    if($key["variation_id"] !=='0') {
						    $productWeight = get_post_meta($key["variation_id"]);

						    if($productWeight["_weight"] !== NULL) {
							    $a['ORDER-' . $order_id][] = array($key["variation_id"]=>$productWeight["_weight"], 'shipWeight'=>'');
						    }else{
							    $a['ORDER-' . $order_id][] = array('empty'=>'0', 'shipWeight'=>$products->get_weight());
						    }

						    $i = $i + 1;
					    }else{
						    $a['ORDER-' . $order_id][] = array('empty'=>'0', 'shipWeight'=>$products->get_weight());
					    }

					    if ( $order->get_shipping_total() != 0){
						    $shipping_total += $order->get_shipping_total();
					    }else{
						    $shipping_total += $meta["clubs_ship_price"][0] * ($product->weight * $key['qty']);
					    }
				    }

				    $i = 0;
				    $prod['order_items'][] = array(
					    'OrderID' => $order_id,
					    'OrderDate' => $date->format('m-d-Y'),
					    'UserName'=>$order->billing_first_name .' '.$order->billing_last_name,
					    'UserPhone'=> $order->billing_phone,
					    'CurrencySymbol' => get_woocommerce_currency_symbol(),
					    'OrdersCount' => $loop->post_count,
					    'products' => $order->get_items(),
					    'weight' => $a,
					    'raw_price' => $tot,
					    'item_discount' => $dis,
					    'total_discount' => $total_discounted,
					    'post_status' => str_ireplace('wc-','',$order->post->post_status),
					    'shipping_price' => $shipping_total,
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
     * Display order table in admin taxonomy single clubs page
     */
    public function rendOrderTaxonomyClubs(){
        global $tag_ID;
        $tag_name = get_term( $tag_ID );
        $have_open = get_term_meta($tag_ID,'clubs_open_date',true);
        $have_close = get_term_meta($tag_ID,'clubs_close_date',true);
        if($tag_name->taxonomy == 'clubs' && $have_open && $have_close){
            $attributes['orders_data'] = $this->getOrdersClubInTax();
            $attributes['club_info'] = get_term_meta($tag_ID);
            $_list_order = new \LW\LW_WP_List_Orders();

            $attributes['ord'] = $this->sortOrdersForWPList();
            $_list_order->prepare_items($attributes['ord']);

            ob_start();
            $_list_order->display();
            $attributes['display'] = ob_get_clean();

	        \LW\LW_Template::getTemplate('adm_templates', 'taxonomy_custom_orders',$attributes);
        }
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
    public function sortOrdersForWPList(){
//        $maparray = new \LW\AdminPageClubs();

        $pre_order = $this->getOrdersClubInTax();
        $weight_unit = get_option('woocommerce_weight_unit');
        $output = array();

        if($pre_order){
            foreach ($pre_order as $orders) {

                foreach ($orders as $product) {
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

            foreach ($orderID as $order) {

                $output[] = array(
                    'display_name' => $order['UserName'],
                    'order_date' => $order['OrderDate'],
                    'user_phone' => $order['UserPhone'],
                    'order_id' => $order['OrderID'],
                    'order_status' => $order['PostStatus'],
                    'order_items' => $this->mapArray($ord[$order['OrderID']],$order['Weight'],$weight_unit));
            }
        }

        return $output;
    }

    /**
     * Generate PDF by Filter Orders
     */
    public function pdfGeneratorInTax(){

        if (defined( 'DOING_AJAX' ) && DOING_AJAX) {

          return;
        }

        $user_club = \LW\Settings::currentUser();

        $attributes['orders_data'] = $this->getOrdersClubInTax();
        $attributes['club_percent'] = $user_club["club_term_meta"]["clubs_discount_p"];

        if($attributes['orders_data']) {
            $date = date('m-d-Y');

            include_once(LW_PLUGIN_DIR . 'LwApp/htmltable/html_table.php');
            $pdf = new \PDF_HTML_Table('L');
            $pdf->AddPage();
            $pdf->SetTitle('Lummi');
            $pdf->SetAuthor('Lummi Wild');
            $pdf->SetFont('Arial', '', 10);

            if (isset($_POST['filter_date_in_tax'])) {
                $temp['date_after'] = $_POST['date_after'];
                $temp['date_before'] = $_POST['date_before'];
                $temp['order_status'] = $_POST['order_status'];
                if ($_POST['pdf_create']) {
                    ob_start();
                    include_once(LW_PLUGIN_DIR . 'LwApp/temp_pdf/pdftemptax.php');
                    $out = ob_get_clean();
                    $pdf->WriteHTML($out,array(38,34,34,34,34,100));
                    $pdf->Output($user_club["club_taxonomy"]->slug . '-' . $date . '.pdf', 'I');
                    exit;
                }
            }
        }
    }

    /**
     * Add styles for taxonomy clubs
     */
    public function taxonomyClubsStyles(){

        if(isset($_GET["taxonomy"]) && $_GET["taxonomy"] == 'clubs'){
            wp_enqueue_style('club_tax_styles',LW_PLUGIN_URL.'additional/css/club_tax_styles.css');
        }
    }

}