<?php
/*
Plugin Name:  WP Nathan
Description:  Small description
Plugin URI:   ""
Author:       Nathan Rodrigues
Version:      1.0
Text Domain:  wp-nathan
Domain Path:  /languages
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/



// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

define ( 'WPNAT_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define ( 'WPNAT_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
define ( 'WPNAT_TETXDOMAIN', 'wp-nathan' );
define ( 'WPNAT_PLUGIN_NAME', 'WP Nathan' );


if ( ! class_exists( 'wpnat_core_functions' ) ) {

	class wpnat_core_functions {
		public function __construct() {
			add_action( 'admin_menu', [ $this, 'wpnat_add_toplevel_menu' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'wpnat_ajax_admin_enqueue_scripts' ] );
			add_action( 'wp_ajax_admin_hook', [ $this, 'wpnat_ajax_admin_handler' ] );
		}

		function wpnat_ajax_admin_enqueue_scripts( $hook ) {
			// check if our page
			if ( 'toplevel_page_wpnat-settings' !== $hook ) return;
	
			// define script url
			$script_url = plugins_url( 'admin/js/ajax-admin.js', __FILE__ );
		
			// enqueue script
			wp_enqueue_script( 'ajax-admin', $script_url, '', true );	
		
			// create nonce
			$nonce = wp_create_nonce( 'ajax_admin' );
		
			// define script
			$script = array( 'nonce' => $nonce );
		
			// localize script
			wp_localize_script( 'ajax-admin', 'ajax_admin', $script );
		
		}

		// process ajax request
		public function wpnat_ajax_admin_handler() {

			// check nonce
			//check_ajax_referer( 'ajax_admin', 'nonce' );

			// check user
			if ( ! current_user_can( 'manage_options' ) ) return;

			// define the url
			$selected_theme = isset( $_POST['selected_theme'] ) ? sanitize_text_field( $_POST['selected_theme'] ) : false;

			update_option( 'selected_theme', $selected_theme );

			return true;
			
			// end processing
			wp_die();

		}
		
	

		// add top-level administrative menu
		public function wpnat_add_toplevel_menu() {
			
			/* 
				add_menu_page(
					string   $page_title, 
					string   $menu_title, 
					string   $capability, 
					string   $menu_slug, 
					callable $function = '', 
					string   $icon_url = '', 
					int      $position = null 
				)
			*/
			
			$capability = 'manage_options';
			$slug = 'wpnat-settings';
	
			add_menu_page(
				__( WPNAT_PLUGIN_NAME, WPNAT_TETXDOMAIN ),
				__( WPNAT_PLUGIN_NAME, WPNAT_TETXDOMAIN ),
				$capability,
				$slug,
				[ $this, 'wpnat_display_settings_page' ],
				"",
			);
			
		}

		public function wpnat_display_settings_page() {
			ob_start();
			?>
			<div class="wrap">
				<h1><?php echo WPNAT_PLUGIN_NAME ?></h1>
				<form class="ajax-form" method="post">
					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row">
									<label for="default_comments_theme">Comment theme</label>
								</th>
								<td>
								<select name="default_comments_theme" id="default_comments_theme">
									<option value="regular" <?php selected( get_option( 'selected_theme' ), 'regular' ); ?>>Regular theme</option>
									<option value="modern" <?php selected( get_option( 'selected_theme' ), 'modern' ); ?>>Modern theme</option>
									<option value="classic" <?php selected( get_option( 'selected_theme' ), 'classic' ); ?>>Classic theme</option>
								</select>
								</td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
					</p>
				</form>
			</div>
			<?php
			ob_get_contents();
		}

	}

	new wpnat_core_functions();

}