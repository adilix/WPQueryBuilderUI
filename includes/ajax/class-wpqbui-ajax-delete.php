<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Delete {
	public function handle() {
		// phpcs:ignore WordPress.Security.NonceVerification
		$id = absint( $_POST['query_id'] ?? 0 );
		if ( ! $id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid query ID.', 'wpqbui' ) ) );
		}
		$repo   = new WPQBUI_Query_Repository();
		$result = $repo->delete( $id );
		if ( ! $result ) {
			wp_send_json_error( array( 'message' => __( 'Could not delete the query.', 'wpqbui' ) ) );
		}
		wp_send_json_success( array( 'message' => __( 'Query deleted.', 'wpqbui' ) ) );
	}
}
