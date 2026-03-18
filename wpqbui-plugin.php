<?php
/**
 * Plugin Name: WP Query Builder UI
 * Plugin URI:  https://github.com/adilix/WPQueryBuilderUI
 * Description: Build complete WP_Query arguments through a friendly admin UI, then generate PHP code and/or a WordPress shortcode.
 * Version:     2.0.0
 * Author:      Adil OUCHRAA
 * Author URI:  https://github.com/adilix
 * Text Domain: wpqbui
 * Domain Path: /languages
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPQBUI_VERSION',    '1.0.1' );
define( 'WPQBUI_DB_VERSION', '1.0.1' );
define( 'WPQBUI_DIR',        plugin_dir_path( __FILE__ ) );
define( 'WPQBUI_URL',        plugin_dir_url( __FILE__ ) );
define( 'WPQBUI_BASENAME',   plugin_basename( __FILE__ ) );

require_once WPQBUI_DIR . 'includes/class-wpqbui-activator.php';
require_once WPQBUI_DIR . 'includes/class-wpqbui-deactivator.php';

register_activation_hook( __FILE__, array( 'WPQBUI_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WPQBUI_Deactivator', 'deactivate' ) );

/**
 * Loads all plugin includes and wires hooks via the Loader.
 */
function wpqbui_run() {
	require_once WPQBUI_DIR . 'includes/class-wpqbui-loader.php';
	require_once WPQBUI_DIR . 'includes/class-wpqbui-i18n.php';
	require_once WPQBUI_DIR . 'includes/query/class-wpqbui-query-definition.php';
	require_once WPQBUI_DIR . 'includes/query/class-wpqbui-query-sanitizer.php';
	require_once WPQBUI_DIR . 'includes/query/class-wpqbui-query-validator.php';
	require_once WPQBUI_DIR . 'includes/query/class-wpqbui-query-repository.php';
	require_once WPQBUI_DIR . 'includes/query/class-wpqbui-query-previewer.php';
	require_once WPQBUI_DIR . 'includes/codegen/class-wpqbui-codegen-formatter.php';
	require_once WPQBUI_DIR . 'includes/codegen/class-wpqbui-codegen-php.php';
	require_once WPQBUI_DIR . 'includes/codegen/class-wpqbui-codegen-shortcode.php';
	require_once WPQBUI_DIR . 'includes/shortcode/class-wpqbui-shortcode.php';
	require_once WPQBUI_DIR . 'includes/ajax/class-wpqbui-ajax-handler.php';
	require_once WPQBUI_DIR . 'includes/admin/class-wpqbui-admin.php';

	$loader = new WPQBUI_Loader();

	// i18n
	$i18n = new WPQBUI_I18n();
	$loader->add_action( 'init', $i18n, 'load_plugin_textdomain' );

	// Shortcode
	$shortcode = new WPQBUI_Shortcode();
	$loader->add_action( 'init', $shortcode, 'register' );

	// AJAX
	$ajax = new WPQBUI_Ajax_Handler();
	$ajax->register( $loader );

	// Admin
	if ( is_admin() ) {
		$admin = new WPQBUI_Admin();
		$admin->register( $loader );
	}

	$loader->run();
}

wpqbui_run();
