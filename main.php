<?php
/*
Plugin Name: snapCX - WooCommerce Address Validation
Plugin URI: https://wordpress.org/plugins/woo-address-validation/
Description: Do real time address validation (USA and rest of world) at checkout page. Customers will get visual messages on same page on address suggestions or errors. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="https://snapcx.io/pricing?solution=avs">Sign up for an snapCX subscription plan</a> to get an API key, and 3) Go to your Plugin configuration page (inside woocommerce menu), and save your API key.
 * Version: 1.3.3
 * Requires at least: 4.0
 * Tested up to: 4.9.2
 * WC requires at least: 3.2.0
 * WC tested up to: 3.4.4
 * Author: snapCX, uberCX
 * Author URI: https://snapcx.io/addressValidation
 * Developer: snapCX Team
 * Developer URI: https://snapcx.io/addressValidation
 * Text Domain: snapCX
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define ( 'UBERCX_ADDR_PLUGIN_PATH' , plugin_dir_path( __FILE__ ) );
define('UBERCX_ADDR_DOMAIN', 'ubercx-addr-val');
global $AVSVersion;
$AVSVersion = '1.2.5';

define('UBERCX_ADDR_BASENAME', plugin_basename( __FILE__ ));

include_once(UBERCX_ADDR_PLUGIN_PATH . 'inc/UberCXAccountVerifier.php');
include_once(UBERCX_ADDR_PLUGIN_PATH . 'inc/UberCXAddrVal.php');
include_once(UBERCX_ADDR_PLUGIN_PATH . 'inc/UberCXApiClient.php');

/**
 * Loads the right js & css assets
*/
function load_ubercx_addr_scripts($hook){
	//load up the javascript
	wp_enqueue_script('jquery');
	wp_enqueue_script('ubercx-js', plugins_url( '/js/ubercx.js', __FILE__ ), 'jquery');
	wp_enqueue_style( 'ubercx-addr-css',  plugin_dir_url( __FILE__ ). 'css/style.css' );

	if(isset($hook) && $hook === 'woocommerce_page_wc-settings') {
	    if(isset($_GET['tab']) && wp_strip_all_tags($_GET['tab']) === 'settings_tab_snapcx_avs') {
            wp_enqueue_script( 'ubercx-addr-settings-js',  plugins_url( '/js/settings.js', __FILE__ ), 'jquery' );
        }
    }
}

add_action('admin_enqueue_scripts', 'load_ubercx_addr_scripts');
add_action('wp_enqueue_scripts', 'load_ubercx_addr_scripts');

//Code For Activation 
register_activation_hook( __FILE__, array( 'UBERCXAddrVal', 'ubercx_plugin_activate' ) );
//Code For Deactivation 
register_deactivation_hook( __FILE__, array( 'UBERCXAddrVal', 'ubercx_plugin_deactivate' ) );
try {
    $ubercxAddrVal = new UberCXAddrVal();
} catch (Exception $e) {
    echo "<h1>Caught Exception: </h1>";
    echo "</p>" . "File:" . $e->getFile() . " on line: " . $e->getLine() . "</p>";
    echo '<p>' . $e->getCode() . ' ' . $e->getMessage() . '</p>';
};

add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );
?>
