<?php

class LummiActivation
{

    public function __construct()
    {
    }

	/**
	 * Install all
	 */
    public static function Install(){

    	self::addPagesAfterActivation();
    	self::addNewRole();
    	self::createDataHistoryClubs();

    }

    /**
     * Add pages after plugin activated
     */
    public static function addPagesAfterActivation() {

        // Information needed for creating the plugin's pages
        $defined_pages = array(
            'lwu-login' => array(
                'title' => __( 'Sign in', 'lummi-wild-se' ),
                'content' => '[lummi_login_form]'
            ),
            'lwu-register' => array(
                'title' => __( 'Register', 'lummi-wild-se' ),
                'content' => '[lummi_register_form]'
            ),
            'lwu-account' => array(
                'title' => __( 'Account', 'lummi-wild-se' ),
                'content' => '[woocommerce_my_account]'
            ),
            'lwu-lostpassword' => array(
                'title' => __( 'Lost Password', 'lummi-wild-se' ),
                'content' => '[lummi_lost_password]'
            ),
            'lwu-resetpassword' => array(
                'title' => __( 'Reset Password', 'lummi-wild-se' ),
                'content' => '[lummi_reset_password]'
            ),
            'inactive-club' => array(
	            'title' => __( 'Inactive Club', 'lummi-wild-se' ),
	            'content' => '[inactive_club_message]'
            )
        );

        foreach ( $defined_pages as $slug => $page ) {

            // Check that the page doesn't exist already
            $query = new \WP_Query( 'pagename=' . $slug );
            if ( ! $query->have_posts() ) {
                // Add the page using the data from the array above
                wp_insert_post(
                    array(
                        'post_content' => $page['content'],
                        'post_name' => $slug,
                        'post_title' => $page['title'],
                        'post_status' => 'publish',
                        'post_type'  => 'page',
                        'ping_status' => 'closed',
                        'comment_status' => 'closed',
                    )
                );
            }
        }
    }

    /**
     * Add new role after plugin activated
     */
    public static function addNewRole(){
        $define_role = array(
            'club-manager' => array(
                'display_name' =>  __( 'Club Manager' ),
                'roles' =>array(
                    'read' => true,
                    'club_manager_options' => true
                )
            ),
            'club-member' => array(
                'display_name' =>  __( 'Club Member' ),
                'roles' =>array(
                    'read' => true
                )
            )
        );

        foreach ($define_role as $role_name => $roles){
            if( ! get_role($role_name)){
                add_role( $role_name, $roles["display_name"], $roles["roles"] );
            }
        }

    }

	/**
	 * Create DB Table for club history open/close date
	 */
	public static function createDataHistoryClubs(){
		global $wpdb;

		$table_name = $wpdb->prefix.LW_PLUGIN_DB;

		$charset_collate = $wpdb->get_charset_collate();

		if(!$wpdb->get_var("SHOW TABLES LIKE $table_name")){
			$sql = "CREATE TABLE $table_name (
                    id bigint(20) NOT NULL AUTO_INCREMENT,
                    club_id int(10) NOT NULL,
                    club_name varchar(400) DEFAULT '' NOT NULL,
                    club_slug varchar(400) DEFAULT '' NOT NULL,
                    open_date varchar(150) DEFAULT '' NOT NULL,
                    close_date varchar(150) DEFAULT '' NOT NULL,
                    create_date varchar(150) DEFAULT NULL,
                    update_date varchar(150) DEFAULT '' NOT NULL,
                    PRIMARY KEY  (id)) $charset_collate";

                    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                    dbDelta( $sql );

                    add_option( 'LW_PLUGIN_DB_VERSION', LW_PLUGIN_DB_VERSION );
		}
	}



}