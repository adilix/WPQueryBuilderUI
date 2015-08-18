<?php
/*
Plugin Name: WP Query Builder UI
Plugin URI: https://github.com/adilix/WPQueryBuilderUI
Description: WPQueryBuilderUi is a Wordpress plugin which allows developers to create a WP_Query object using a friendly user interface, then generate PHP code and a Wordpress shortcode (Credit to ShibaShake for the plugin architecture: http://shibashake.com/)
Version: 0.1.0
Author: Adil OUCHRAA & Noureddine ABABAR 
Author URI: https://github.com/adilix
*/

/*  Copyright 2014 WPQueryBuilderUi

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define( 'WPQBUI_VERSION', '0.1.0' );
define( 'WPQBUI_RELEASE_DATE', date_i18n( 'F j, Y', '1411212894' ) );
define( 'WPQBUI_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPQBUI_URL', plugin_dir_url( __FILE__ ) );


if (!class_exists("WPQueryBuilderUI")) :

class WPQueryBuilderUI {
	var $settings, $options_page;
	
	function __construct() {	

		if (is_admin()) {
			// Load example settings page
			if (!class_exists("WPQBUI_Settings"))
				require(WPQBUI_DIR . 'wpqbui-settings.php');
			$this->settings = new WPQBUI_Settings();	
		}
		
		add_action('init', array($this,'init') );
		add_action('admin_init', array($this,'admin_init') );
		add_action('admin_menu', array($this,'admin_menu') );
		
		register_activation_hook( __FILE__, array($this,'activate') );
		register_deactivation_hook( __FILE__, array($this,'deactivate') );
	}

	/*
		Propagates pfunction to all blogs within our multisite setup.
		If not multisite, then we just run pfunction for our single blog.
	*/
	function network_propagate($pfunction, $networkwide) {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function 
			// for each blog id
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					call_user_func($pfunction, $networkwide);
				}
				switch_to_blog($old_blog);
				return;
			}	
		} 
		call_user_func($pfunction, $networkwide);
	}

	function activate($networkwide) {
		$this->network_propagate(array($this, '_activate'), $networkwide);
	}

	function deactivate($networkwide) {
		$this->network_propagate(array($this, '_deactivate'), $networkwide);
	}

	/*
		Enter our plugin activation code here.
	*/
	function _activate() {}

	/*
		Enter our plugin deactivation code here.
	*/
	function _deactivate() {}
	

	/*
		Load language translation files (if any) for our plugin.
	*/
	function init() {
		load_plugin_textdomain( 'wpqbui', WPQBUI_DIR . 'lang', basename( dirname( __FILE__ ) ) . '/lang' );
	}

	function admin_init() {
	}

	function admin_menu() {

		if ( ! current_user_can('update_plugins') )
				return;
		add_menu_page( 	__('WP Query Builder UI', 'wpqbui'), 
										__('WPQBUI', 'wpqbui'), 
										'administrator', 
										'wpqbui-plugin', 
										array($this,'render_generator'), 
										plugins_url( 'wpqbui/assets/img/wpqbui.png' ), 
										81 
									);
		add_action("admin_print_scripts-$page", array($this->settings, 'js_includes'));

	}

	function render_generator() {

		$title = __('WP Query Builder UI - Generator', 'wpqbui');
		?>
		<div class="wrap">   
			<h2><?php echo esc_html( $title ); ?></h2>
		</div>
		<form method="post" action="options.php">
			<div class="metabox-holder">
				<div class="postbox-container" style="width: 99%;">
				</div>
			</div>
			<p>
				<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Generate'); ?>" />
			</p>
			<div class="code">
				
			</div>
		</form>

		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function ($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
		});
		//]]>
		</script>
		<?php 
	}
	

} // end class
endif;

// Initialize plugin object.
global $wpqbui;
if (class_exists("WPQueryBuilderUI") && !$wpqbui) {
    $wpqbui = new WPQueryBuilderUI();	
}	
?>