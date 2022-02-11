<?php
/**
 * Plugin Name: NFT Gallery
 * Plugin URI: https://wpopensea.com/
 * Description: The easiest way to add NFTs from OpenSea to your WordPress site! Powered by OpenSea API.
 * Author: Hendra Setiawan
 * Author URI: https://hendra.skybee.io/
 * Version: 1.0.0
 * Text Domain: wpopensea
 * Written by: Hendra Setiawan - https://hendra.blog/about/
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

$mode = 'live'; // dev or live

if($mode == 'dev') { $version = rand(100,999); } else { $version = '1.0.0'; }

define('WPOPENSEA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WPOPENSEA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPOPENSEA_VERSION', $version);

require_once( WPOPENSEA_PLUGIN_PATH . 'admin/functions.php' );
require_once( WPOPENSEA_PLUGIN_PATH . 'inc/shortcodes.php' );

function wpopensea_assets() {
    wp_register_style( 'flexbox', plugin_dir_url( __FILE__ ) . 'assets/css/flexboxgrid.min.css', false, WPOPENSEA_VERSION );
    wp_register_style( 'wpopensea', plugin_dir_url( __FILE__ ) . 'assets/css/frontend.css', false, WPOPENSEA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'wpopensea_assets' );