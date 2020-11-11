<?php 

/*
Plugin name: Intuito Cycle2
Plugin URI: http://www.intuitowebsites.com
Description: Loads the power of jQuery Cycle2 for use in a theme 
Version: 1.0
Author: Rob Haskell,
License: private
*/

add_action( 'admin_menu', 'intuito_plugin_menu_cycle2' );

function intuito_plugin_menu_cycle2() {
	add_menu_page ( //http://codex.wordpress.org/Function_Reference/add_menu_page
		'Intuito Cycle2', //page title
		'jQuery Cycle2', //menu title
		'manage_options', //capability
		'intuito-jquery-cycle2', //menu slug
		'intuito_cycle2_config', //function that calls the page
		'', //icon url
		22 //position in the menu
	);
} 


// Functions to call up menu item pages
function intuito_cycle2_config() {
	include('php/intuito_cycle2.php');
}

include('php/shortcodes.php');

add_action( 'wp_enqueue_scripts', 'intuito_cycle2_scripts' );
function intuito_cycle2_scripts() {
	wp_enqueue_script(
		'cycle2', // handle
		plugins_url() . '/intuito-cycle2/scripts/jquery.cycle2.min.js', // relative path
		array( 'jquery' ) //dependencies. jquery is loaded automatically
	);

	wp_enqueue_style(
	  'cycle2_css',
	  plugins_url() . '/intuito-cycle2/css/style.css'
	);

}



?>