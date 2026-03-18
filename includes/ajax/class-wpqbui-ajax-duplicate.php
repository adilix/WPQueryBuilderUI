<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Duplicate {
	public function handle() {
		// phpcs:ignore WordPress.Security.NonceVerification
		$id   = absint( $_POST['query_id'] ?? 0 );
		if ( ! $id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid query ID.', 'wpqbui' ) ) );
		}
		$repo   = new WPQBUI_Query_Repository();
		$new_id = $repo->duplicate( $id );
		if ( ! $new_id ) {
			wp_send_json_error( array( 'message' => __( 'Could not duplicate the query.', 'wpqbui' ) ) );
		}
		wp_send_json_success( array(
			'new_id'  => $new_id,
			'message' => __( 'Query duplicated successfully.', 'wpqbui' ),
		) );
	}
}
