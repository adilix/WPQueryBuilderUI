<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Terms {
	public function handle() {
		// phpcs:ignore WordPress.Security.NonceVerification
		$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification
		$search   = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';

		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid taxonomy.', 'wpqbui' ) ) );
		}

		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'search'     => $search,
			'number'     => 100,
			'hide_empty' => false,
		) );

		if ( is_wp_error( $terms ) ) {
			wp_send_json_error( array( 'message' => $terms->get_error_message() ) );
		}

		$result = array();
		foreach ( $terms as $term ) {
			$result[] = array(
				'term_id' => $term->term_id,
				'name'    => $term->name . ' (ID: ' . $term->term_id . ')',
				'slug'    => $term->slug,
			);
		}
		wp_send_json_success( $result );
	}
}
