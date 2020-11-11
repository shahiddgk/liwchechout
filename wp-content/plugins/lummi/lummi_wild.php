<?php
/**
* Plugin name: Lummi Wild
* Plugin URI: http://www.intuitowebsites.com
* Description: Customize WooCommerce for Lummi Wild
* Version: 1.0.0
* Author: Rob Haskell, Intuito Websites
* License: private
* Text Domain: lummi-wild-se
*/

if (!defined( 'ABSPATH'))
{
    exit;
}

define('LW_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('LW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LW_PLUGIN_DB','clubs_history');
define('LW_PLUGIN_DB_VERSION','1.0');

require_once('LwApp/LwApp.php');
include_once ('LwApp/LummiActivation.php');

register_activation_hook( __FILE__ ,array('LummiActivation', 'Install'));

$lummi_wild = new \LW\LwApp();

$lummi_wild->run();