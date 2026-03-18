<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Taxonomies {
	public function handle() {
		$post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( 'any' === $post_type || '' === $post_type ) {
			$taxes = get_taxonomies( array(), 'objects' );
		} else {
			$taxes = get_object_taxonomies( $post_type, 'objects' );
		}

		$result = array();
		foreach ( $taxes as $tax ) {
			$result[] = array(
				'slug'  => $tax->name,
				'label' => $tax->label . ' (' . $tax->name . ')',
			);
		}
		wp_send_json_success( $result );
	}
}
