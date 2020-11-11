<?php

namespace LW;

class Settings
{

    public function __construct()
    {
        add_action( 'admin_init',array($this,'currentUser'),10);
        add_action( 'template_redirect', array( $this,'redirectDifferentSituations' ),1 );
        add_action( 'show_user_profile', array($this,'userProfileFields'));
        add_action( 'edit_user_profile', array($this,'userProfileFields'));
        add_action( "user_new_form", array($this,'userProfileFields') );
        add_action( 'personal_options_update', array($this,'saveUserProfileFields'));
        add_action( 'edit_user_profile_update', array($this,'saveUserProfileFields'));
        add_action( 'user_register', array($this,'saveUserProfileFields'));
        add_filter( 'woocommerce_account_menu_items', array( $this, 'removeSomeAccountMenuItems' ));
        add_filter( 'bloginfo', array($this,'bloginfoFilter'),10,2);
        add_action( 'admin_bar_menu', array($this,'easyAccessShopLinkForManagers'), 999);
        add_filter( 'manage_users_columns', array($this,'userClubTableColumnName') );
        add_filter( 'manage_users_custom_column', array($this,'userClubTableRows'), 10, 3 );
//        add_action('init',array('LW\Settings','getClubInfo'));
        add_action('wp_head', array('LW\Settings','lw_get_user'));
        add_filter( 'admin_footer_text', array($this,'removeFooterText'), 11,1 );
        add_filter( 'update_footer', array($this,'removeFooterText'), 11,1 );
        // add_filter( 'woocommerce_product_categories_widget_args', array($this , 'beforeSidebarProducsCategory'),10,1 );
        add_action('init',array(get_called_class(),'is_closed_club'),10);
        add_action('admin_init',array(get_called_class(),'is_closed_club'),11);
		add_action('admin_enqueue_scripts',array($this,'ajaxChefsClubs'),30);
	    add_action( 'wp_print_scripts', array($this,'disableAutosave') );
    }

	/**
	 * Disable auto save
	 */
	function disableAutosave() {
		wp_deregister_script('autosave');
	}

    public static function dd($var,$die = 0){
    	switch ($die){
		    case 0 :
		    	    var_dump($var);
		    	break;
		    case 1 :
			        var_dump($var); die();
		        break;
		    case 2 :
		    	    print_r($var); die();
		    	break;
		    case 3 :
		    	    echo '<pre>';
		    	    var_dump($var);
		    	    echo '</pre>';
		    	break;
	    }
    }
    
    /**
     * Format date
     * @param type $switch
     * @param type $args
     * @return boolean
     */
    public static function getDateFormat($switch = '', $args = array() )
    {
        
        $date_time = $default_format = get_option('date_format') .' '.get_option('time_format');
        $c_format = false;
        
        if( array_key_exists('date_time_format', $args ) ){
            $date_time = $args['date_time_format'];
            $c_format = true;
        }
                
        if( array_key_exists('date', $args ) ){
            if ( empty($args['date']) ){
                return false;
            }
            $date_time = ( ! $c_format ) ? $default_format : $date_time;
            $date = \DateTime::createFromFormat( $date_time, $args['date'] );
            if( $date === false ){
                return false;
            }
            
//            if( $date === false ){
//                $date = \DateTime::createFromFormat( $default_format, $args['date'] );
////                if($date === false){
////                    return false;
////                }
//                $date = $date->format($date_time);
//                $date = \DateTime::createFromFormat( $date_time, $date );
//            }
            
            $pd = date_parse_from_format( $date_time, $date->format($date_time) );
        }
        
        switch( $switch ){
            
            case 'mysql_date_now' :
                
                return current_time('mysql', 0);
                
            case 'timestamp_now' :
                
                return current_time( 'timestamp', 0 );
                               
            case 'this_timestamp' :
                
                return mktime( $pd["hour"], $pd["minute"], 0, $pd["month"], $pd["day"], $pd["year"] );
                
            case 'this_date' :
                
                return $date->format($date_time);
                
            case 'this_mysql_date' :
                
                return $date->format('Y-m-d H:i:s');
                
            default :
                
                return date( $date_time, current_time( 'timestamp', 0 ) );
        }
        
    }

    /**
    * Initial java script variables need for chefs clubs 
    */
    public function ajaxChefsClubs()
    {
	wp_localize_script('adm_script','chefs',array(
			'admin_url' => admin_url('admin-ajax.php'),
			'action_club'=> 'chefs_save_options',
			'chefs_nonce_club' => wp_create_nonce( "chefs_secure"),
			'action_product'=> 'chefs_save_product_options',
			'chefs_nonce_product' => wp_create_nonce( "chefs_secure_product"),
                        'action_set_period' => 'club_set_period_once',
                        'club_set_period_nonce' => wp_create_nonce( "set_period_secure"),
                        'action_delete_period' => 'club_delete_period',
                        'club_delete_period_nonce' => wp_create_nonce( "delete_period_secure"),
                        'history_url' => admin_url('admin.php?page=clubs-history')
	));
    }

    /**
    * Check club is active or inactive by date range and club status
    */
    public static function is_closed_club()
    {

        $user = self::currentUser();
        $date =  \LW\CommerceConfig::clubsDateCloseOpenOrder();
        if(is_user_logged_in()){
            if( $user["club_term_meta"]["clubs_active"] == 'off' || $date === 1){
                return true;
            }
        }

        return null;
    }

    /**
    * Hide sidebar with product category in inactive club
    */
    public function beforeSidebarProducsCategory($list_args)
    {

        $user = \LW\Settings::currentUser();
        $date = \LW\CommerceConfig::clubsDateCloseOpenOrder();

        if( (is_user_logged_in() && $user["club_term_meta"]["clubs_active"] == 'off') || $date === 1){
            $list_args["taxonomy"] = null;
        }
        return $list_args;
    }

    /**
     * Remove wordpress footer desc and version number
     * @param $text
     */
    public function removeFooterText($text)
    {
        unset($text);
    }

    /**
     * Add new column name in users list table
     * @param $column
     * @return mixed
     */
    public function userClubTableColumnName( $column ) {
        $column['user_clubs'] = 'User Club';
        unset($column['posts']);
        return $column;
    }

    /**
     * Display user club membership in user column
     * @param $val
     * @param $column_name
     * @param $user_id
     * @return mixed
     */
    public function userClubTableRows( $val, $column_name, $user_id ) {

        $user_club = (int)get_user_meta( $user_id,'user_club',true);

        if(is_int($user_club)){
            $the_club = get_term_by('id',$user_club,'clubs',ARRAY_A);
        }
        if(is_string($user_club)){
            $the_club = get_term_by('name',$user_club,'clubs',ARRAY_A);
        }

        switch ($column_name) {
            case 'user_clubs' :
                return $the_club["name"];
                break;
            default:
        }
        return $val;
    }


    /**
     * Register link to shop page for easy access
     *
     * @param $wp_admin_bar
     */
    public function easyAccessShopLinkForManagers($wp_admin_bar) {

            $slug = home_url('shop');
            $shop_page_id = get_option('woocommerce_shop_page_id');
            if($shop_page_id){
                $post = get_post($shop_page_id);
                $slug = $post->post_name;
                $name = $post->post_name;
            }

            $args = array(
                'id' => 'link_to_shop_page',
                'title' => 'Go To Page '.ucfirst($name),
                'href' => home_url($slug),
                'meta' => array(
                    'class' => 'link_to_shop_page',
                    'title' => 'Go To Page '.ucfirst($name)
                )
            );
            $wp_admin_bar->add_node($args);
    }
    
    /**
     * Other rules to redirect
     */
    public function redirectDifferentSituations()
    {
        if((is_page( 'lwu-register' ) || is_page( 'lwu-login' ) || is_page('lwu-lostpassword') ) && is_user_logged_in() ) {
            wp_redirect( home_url( 'lwu-account' ) );
            exit();
        }

        if(is_page('lwu-account') && !is_user_logged_in()){
            wp_redirect('lwu-login');
            exit;
        }
    }

    /**
     * Change title and description if is user club or not
     * @param $title
     * @param $show
     * @return string
     */
    public function bloginfoFilter($title,$show){
        $user = self::currentUser();
        if(is_page('lwu-register') && !current_user_can('administrator')){
            $data = get_query_var('club');
            $club = get_term_by('slug',$data,'clubs',ARRAY_A);
            $addon = ' - '.$club['name'];
            if('name' == $show && $club){
                $title = get_bloginfo('name').$addon;
            }
            if ('description' == $show) {
                $title = 'Registration Form';
            }
        }

        if($user['user_info']['ID'] && $user['club_taxonomy']->slug != 'no-membership' && !current_user_can('administrator')){
            if('name' == $show && is_user_logged_in()){
                // $title = get_bloginfo('name').' - '.$user['club_taxonomy']->name;
                $title = 'Welcome to ' . $user['club_taxonomy']->name;

            }
//            if('description' == $show){
//                $manager = get_user_meta($user['club_term_meta']["clubs_manager_id"],'nickname');
//                $title = 'Manager: '.$manager[0].' | ';
//                $title .= 'Email: '.$user['club_term_meta']["clubs_email"].' | ';
//                $title .= 'Phone: '.$user['club_term_meta']["clubs_phone"].' | ';
//                $title .= 'Discount: '.$user['club_term_meta']["clubs_discount_p"].'%';
//            }
        }else{
            $title = '';
        }

        return $title;
    }

    /**
     * Get club info by user
     * @param bool $default_info
     * @param null $default_manager
     * @return null|string
     */
//    public static function getClubInfo($default_info = false,$default_manager = null){
//        $user = self::currentUser();
//        $dsc = null;
//        $def = self::getDefaultClub();
//        if($user['user_info']['ID'] && !$user["club_term_meta"]["default_club"] && !current_user_can('administrator')){
//            $manager = get_user_meta($user['club_term_meta']["clubs_manager_id"],'nickname');
//            $dsc = 'Manager: '.$manager[0].' | ';
//            $dsc .= 'Email: '.$user['club_term_meta']["clubs_email"].' | ';
//            $dsc .= 'Phone: '.$user['club_term_meta']["clubs_phone"].' | ';
//            $dsc .= 'Discount: '.$user['club_term_meta']["clubs_discount_p"].'%';
//        }
//        elseif ($default_info === true){
//            $dsc = ($default_manager) ? 'Manager: '.$default_manager.' | ' : null;
//            $dsc .= 'Email: '.$def["clubs_email"][0].' | ';
//            $dsc .= 'Phone: '.$def["clubs_phone"][0];
//        }
//
//        return $dsc;
//
//    }

    public static function lw_get_user(){
        $user = self::currentUser();
        if($user['user_info']['ID'] && !$user["club_term_meta"]["default_club"] && !current_user_can('administrator')){
            $data['club_taxonomy'] = get_term($user['user_meta']['user_club'],'clubs');
        }
        return $data;
    }


    /**
     * Get default club
     * @return mixed|null
     */
    public static function getDefaultClub(){
        $default = null;
        $dclub = get_terms('clubs', array('fields' => 'id=>slug'));
        foreach ($dclub as $k => $slug) {
            if (get_term_meta($k, 'default_club',true)) {
                $default = get_term_meta($k);
            }
        }
        return $default;
    }

    /**
     * Remove links downloads and dashboard from account
     * @param $items
     * @return mixed
     */
    public function removeSomeAccountMenuItems( $items ) {
        unset($items["dashboard"]);
        unset($items ["downloads"]);
        return $items;
    }

    /**
     * Display custom user fields in user profile from template
     * Only for Admin
     * 
     * @param $user
     */
    public function userProfileFields( $user ) {
        $attributes['user_id'] = $user->ID;
        if(current_user_can('administrator')) {
            \LW\LW_Template::getTemplate('adm_templates', 'users_fields', $attributes);
        }
    }

    /**
     * Save new user settings from custom fields
     * @param $user_id
     * @return bool
     */
    public function saveUserProfileFields( $user_id ) {
        if ( !current_user_can( 'edit_users', $user_id ) ){
            return false;
        }
        update_user_meta( $user_id, 'user_club', $_POST['user_club'] );
        
        if( $_POST['role'] === 'club-manager' && ! empty( get_term_meta($_POST['user_club'], 'clubs_manager_id', true) ) ){
            update_term_meta($_POST['user_club'], 'clubs_manager_id', $user_id);
        }
    }

    //Users and conditions

    /**
     * @param null $arg
     * @param string $return_type : OBJECT|ARRAY_A|ARRAY_N
     * @return array|null|object
     */
    public static function getCurrentClubMembersByManager($arg = null,$return_type = 'ARRAY_A'){
        global $wpdb,$tag_ID;

        if(!$arg){
            $arg = 'ID,meta_key,meta_value';
        }

        $m = self::currentUser();

        if($tag_ID){

            $m = get_term_meta($tag_ID);

            $query = $wpdb->get_results("SELECT $arg
                                  FROM $wpdb->users AS us
                                  INNER JOIN $wpdb->usermeta  AS um
                                  ON us.id = um.user_id
                                  WHERE um.meta_key = 'user_club'
                                  AND um.meta_value = {$tag_ID}
                                  AND NOT ID = {$m["clubs_manager_id"][0]}", $return_type);
            return $query;
        }

        $query = $wpdb->get_results("SELECT $arg 
                                  FROM $wpdb->users AS us
                                  INNER JOIN $wpdb->usermeta  AS um
                                  ON us.id = um.user_id 
                                  WHERE um.meta_key = 'user_club' 
                                  AND um.meta_value = {$m['user_meta']['user_club']}
                                  AND NOT ID = {$m["user_info"]['ID']}", $return_type);
       return $query;
    }

    /**
     * Collect data for current user
     *
     * @return \WP_User
     */
    public static function currentUser(){
        $current_user = wp_get_current_user();

        if(!$current_user){
            return $current_user;
        }

        $data['user_info'] = get_object_vars($current_user->data);

        $user_meta = get_user_meta($data["user_info"]["ID"]);

        if(!$user_meta){
            return $data;
        }

        $map_user_meta = array_map(function($user_meta) {
                $arr = array();
                foreach ($user_meta as $meta ){
                    $arr = $meta;
                }
                return $arr;
            }, $user_meta);

        $data['user_meta'] = array_diff_key(array_diff($map_user_meta,array('')),array("session_tokens" => '',"user_pass" => ''));

        $data['club_taxonomy'] = get_term($data['user_meta']['user_club'],'clubs');

        if($data['club_taxonomy']->errors){
            return $data;
        }

        $club_term_meta = get_term_meta($data['club_taxonomy']->term_id);

        if(!$club_term_meta){
            return $data;
        }

        $data['club_term_meta'] = array_map(function($club_term_meta) {
            $arr = array();
            foreach ($club_term_meta as $meta ){
                $arr = $meta;
            }
            return $arr;
        }, $club_term_meta);

        return $data;
    }
}