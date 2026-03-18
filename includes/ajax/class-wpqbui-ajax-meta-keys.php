<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Meta_Keys {
	public function handle() {
		global $wpdb;
		// phpcs:ignore WordPress.Security.NonceVerification
		$post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification
		$search    = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';

		if ( $post_type && 'any' !== $post_type ) {
			$sql    = $wpdb->prepare(
				"SELECT DISTINCT pm.meta_key
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE p.post_type = %s
				AND pm.meta_key LIKE %s
				ORDER BY pm.meta_key
				LIMIT 100",
				$post_type,
				'%' . $wpdb->esc_like( $search ) . '%'
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT DISTINCT meta_key
				FROM {$wpdb->postmeta}
				WHERE meta_key LIKE %s
				ORDER BY meta_key
				LIMIT 100",
				'%' . $wpdb->esc_like( $search ) . '%'
			);
		}

		$keys = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		// Filter out internal meta keys (those starting with _).
		$keys = array_values( array_filter( $keys, function( $k ) {
			return 0 !== strpos( $k, '_' );
		} ) );

		wp_send_json_success( $keys );
	}
}
