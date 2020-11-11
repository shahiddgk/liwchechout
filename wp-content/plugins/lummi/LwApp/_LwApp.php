<?php

namespace LW;

require_once ('Loader.php');

class LwApp
{
    public $user = null;

    public function __construct()
    {
        \LW\Loader::registerNamespace('LW', dirname(__FILE__) . DIRECTORY_SEPARATOR);
        \LW\Loader::registerAutoLoad();
        add_action( 'admin_init', array($this,'restrictAdmin'), 1 );
        add_action('after_setup_theme', array($this,'removeAdminBar'));
        add_action('admin_enqueue_scripts',array($this,'adminEnqueueScript'));
        add_action('wp_enqueue_scripts',array($this,'clientEnqueueScript'));
    }
   
    public function run()
    {
        $settings = new \LW\Settings();

        $clubs = new \LW\ClubsTaxonomies();

        $forms = new \LW\Forms();

        $woo = new \LW\CommerceConfig();

        $admin_club_page = new \LW\AdminPageClubs();

        $clubs->applyTaxonomies();

        $forms->applySettingsForms();

        $woo->applyConfig();

        $admin_club_page->doAdminClubPages();
    }

    public function restrictAdmin() {

        $user = \LW\Settings::currentUser();

        if ( !current_user_can( 'manage_options' ) && !current_user_can('club_manager_options')  && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
            wp_redirect( home_url('lwu-account') );
            exit;
        }

        if(!current_user_can('administrator') && is_admin() && !$user['user_meta']['user_club'] ){
            wp_redirect(home_url('shop'));
            exit;
        }
    }

    public function removeAdminBar(){
        if(current_user_can('club_manager_options') ){
            show_admin_bar(true);
        }
    }

    /**
     * Registration for some needed script
     */
    public function adminEnqueueScript(){
        wp_enqueue_script('adm_script', LW_PLUGIN_URL.'additional/js/script.js', array('jquery'), '', true);
        wp_enqueue_script('multi_select', LW_PLUGIN_URL.'additional/js/multi-select.js', array('jquery'), '', true);
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker','',array('jquery'),'',true);
        wp_enqueue_script('jquery-ui-slider','',array('jquery'),'',true);
        wp_enqueue_style('admin_style',LW_PLUGIN_URL.'additional/css/lummi_admin_style.css');
        wp_enqueue_style('jquery-ui-min',LW_PLUGIN_URL.'additional/css/jquery-ui.min.css');
        wp_enqueue_style('jquery-ui-structure-min',LW_PLUGIN_URL.'additional/css/jquery-ui.structure.min.css');
        wp_enqueue_style('jquery-ui-theme-min',LW_PLUGIN_URL.'additional/css/jquery-ui.theme.min.css');
        wp_enqueue_style('date-time-picker-css',LW_PLUGIN_URL.'additional/css/date-time-picker.css');
        wp_enqueue_script('date-time-picker',LW_PLUGIN_URL.'additional/js/date-time-picker.js',array('jquery'),'',true);
    }

    public function clientEnqueueScript(){

        $club = \LW\Settings::currentUser();
        $date_status = \LW\CommerceConfig::clubsDateCloseOpenOrder();

        //Display inactive message in shop page
        if((is_user_logged_in() && $club["club_term_meta"]["clubs_active"] == 'off') || (is_user_logged_in() && $date_status === 1)) {
            $idpage = get_the_ID();
            $shop_page_id = get_option('woocommerce_shop_page_id');
            wp_enqueue_script('inactive_script',LW_PLUGIN_URL.'additional/js/inactive_script.js',array('jquery'),'',true);
            wp_localize_script('inactive_script','lwu',array(
                'message' => $club["club_term_meta"]['clubs_inactive_massage'],
                'id_page' => $idpage,
                'id_shop' => $shop_page_id,
                'status' => $date_status
            ));
        }
        
        // Display wellcome message in shop page
        if(is_user_logged_in() && $club["club_term_meta"]["clubs_active"] == 'on' && $date_status === 0){
            $idpage = get_the_ID();
            $shop_page_id = is_shop();
            wp_enqueue_script('front_script',LW_PLUGIN_URL.'additional/js/front_script.js',array('jquery'),'',true);
            wp_localize_script('front_script','log',array(
                'message' => $club["club_term_meta"]["clubs_after_login_massage"],
                'id_page' => $idpage,
                'id_shop' => $shop_page_id,
                'status' => $date_status
            ));
        }
    }

}