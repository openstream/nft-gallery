<?php
/**
* WPOpenSea - Admin Functions
*
* In this file,
* you will find all functions related to the plugin settings in WP-Admin area.
*
* @author 	Hendra Setiawan
* @version 	1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpopensea_admin_js($hook) {
	if( 'toplevel_page_wpopensea' != $hook ) {
		return;
	}
wp_enqueue_script('wpopensea_admin_js_file', plugin_dir_url(__FILE__) . 'js/admin.js');
}
add_action('admin_enqueue_scripts', 'wpopensea_admin_js');

add_action( 'admin_menu', 'wpopensea_admin_menu' );
function wpopensea_admin_menu() {
	add_menu_page(__('NFT Gallery','wpopensea'), __('NFT Gallery','wpopensea'), 'manage_options', 'wpopensea', 'wpopensea_toplevel_page', 'dashicons-grid-view', 16 );
}

function wpopensea_register_settings() {
    register_setting('wpopensea-settings-group', 'wpopensea-api');
    register_setting('wpopensea-settings-group', 'wpopensea-type');
	register_setting('wpopensea-settings-group', 'wpopensea-limit');
	register_setting('wpopensea-settings-group', 'wpopensea-id');
}
add_action('admin_init', 'wpopensea_register_settings');

function wpopensea_toplevel_page() {
	// Permission check
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// Get the default API key
	if( get_option('wpopensea-api') ) {
		$wpopenseaAPI = get_option('wpopensea-api'); }
	else {
		$wpopenseaAPI = 'b61c8a54123d4dcb9acc1b9c26a01cd1'; }

	// Get the default type
	if( get_option('wpopensea-type') ) {
		$wpopenseaType = get_option('wpopensea-type'); }
	else {
		$wpopenseaType = 'collection'; }

	// Get the default limit
	if( get_option('wpopensea-limit') ) {
		$wpopenseaLimit = get_option('wpopensea-limit'); }
	else {
		$wpopenseaLimit = 18; }	

	// Get collection slug or wallet address
	$wpopenseaID = get_option('wpopensea-id');
?>
<div class="wpwrap">
	<div class="card" style="border-radius: 10px;">	
	<h1 style="padding-top: 15px; text-align: center;"><?php _e('NFT Gallery','wpopensea'); ?></h1>
		<div class="form-wrap">
			<form method="post" action="options.php">
				<?php settings_fields('wpopensea-settings-group'); ?>
				<?php do_settings_sections('wpopensea-settings-group'); ?>
				<div class="form-field wpopensea-wrapper">
					<label for="wpopensea-api" style="font-weight: bold;">OpenSea API Key</label>
					<input type="text" style="width: 100%;" value="<?php esc_html_e( $wpopenseaAPI, 'wpopensea' ); ?>" name="wpopensea-api">
					<p>In order to get your own API Key, you can <a href="https://docs.opensea.io/reference/request-an-api-key" target="_blank">Request an API key</a> here.</p>

					<label for="wpopensea-type" style="font-weight: bold;">Type</label>
					<select name="wpopensea-type" class="ostype">
						<option value="collection" <?php if($wpopenseaType == 'collection') echo 'selected'; ?>>Collection</option>
						<option value="owner" <?php if($wpopenseaType == 'owner') echo 'selected'; ?>>Owner</option>
					</select>
					<p>Choose which type of NFTs that you would like to show, either from a collection or a single wallet address.</p>

					<label for="wpopensea-id" style="font-weight: bold;" class="osid">Wallet Address</label>
					<input type="text" name="wpopensea-id" style="width: 100%;" value="<?php esc_html_e( $wpopenseaID, 'wpopensea' ); ?>" required="">
					<p class="osidcaption">Please specify your wallet address.</p>

					<label for="wpopensea-limit" style="font-weight: bold;">Limit</label>
					<input type="number" name="wpopensea-limit" style="width: 60px;" value="<?php esc_html_e( $wpopenseaLimit, 'wpopensea' ); ?>">
					<p>Specify the number of NFTs to show.</p>				
				</div>

				<hr />
				<h3>Shortcode</h3>
				<?php
				if($wpopenseaAPI){
					echo '<p>Copy and paste this shortcode directly into any post or page.</p>';
					echo '<textarea style="width: 100%;" readonly="readonly">[wpopensea]</textarea>';
				} else {
					echo '<p style="color: red;">Problem detected! Please set your OpenSea API.</p>';
				}
				?>
				<?php submit_button('Save Settings'); ?>
			</form>		
			<p style="text-align: center; border-top: 1px solid #eee; padding-top: 15px;">NFT Gallery. Made with <span class="dashicons dashicons-heart"></span> by <a href="https://hendra.skybee.io/" target="_blank">Hendra</a>.</p>
		</div>
	</div>
</div>
<?php
}