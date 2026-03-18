<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers all wp_ajax_wpqbui_* actions.
 */
class WPQBUI_Ajax_Handler {

	public function register( WPQBUI_Loader $loader ) {
		$actions = array(
			'wpqbui_get_post_types',
			'wpqbui_get_taxonomies',
			'wpqbui_get_terms',
			'wpqbui_get_authors',
			'wpqbui_get_meta_keys',
			'wpqbui_get_posts',
			'wpqbui_preview_query',
			'wpqbui_duplicate_query',
			'wpqbui_delete_query',
		);
		foreach ( $actions as $action ) {
			$loader->add_action( 'wp_ajax_' . $action, $this, 'dispatch', 10, 0 );
		}
	}

	public function dispatch() {
		// Identify the action.
		$action = isset( $_POST['action'] ) ? sanitize_key( $_POST['action'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		// Verify nonce.
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! wp_verify_nonce( $nonce, 'wpqbui_ajax' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'wpqbui' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'wpqbui' ) ), 403 );
		}

		$class_map = array(
			'wpqbui_get_post_types' => 'WPQBUI_Ajax_Post_Types',
			'wpqbui_get_taxonomies' => 'WPQBUI_Ajax_Taxonomies',
			'wpqbui_get_terms'      => 'WPQBUI_Ajax_Terms',
			'wpqbui_get_authors'    => 'WPQBUI_Ajax_Authors',
			'wpqbui_get_meta_keys'  => 'WPQBUI_Ajax_Meta_Keys',
			'wpqbui_get_posts'      => 'WPQBUI_Ajax_Posts',
			'wpqbui_preview_query'  => 'WPQBUI_Ajax_Preview',
			'wpqbui_duplicate_query' => 'WPQBUI_Ajax_Duplicate',
			'wpqbui_delete_query'   => 'WPQBUI_Ajax_Delete',
		);

		if ( ! isset( $class_map[ $action ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Unknown action.', 'wpqbui' ) ), 400 );
		}

		// Lazy-load the handler.
		$class_name = $class_map[ $action ];
		$file       = WPQBUI_DIR . 'includes/ajax/class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
		if ( ! file_exists( $file ) ) {
			wp_send_json_error( array( 'message' => __( 'Handler not found.', 'wpqbui' ) ), 500 );
		}
		require_once $file;

		( new $class_name() )->handle();
	}
}
