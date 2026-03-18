<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Authors {
	public function handle() {
		// phpcs:ignore WordPress.Security.NonceVerification
		$search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';

		$users = get_users( array(
			'search'         => $search ? '*' . $search . '*' : '',
			'search_columns' => array( 'user_login', 'display_name', 'user_email' ),
			'number'         => 50,
			'orderby'        => 'display_name',
			'order'          => 'ASC',
		) );

		$result = array();
		foreach ( $users as $user ) {
			$result[] = array(
				'ID'           => $user->ID,
				'display_name' => $user->display_name . ' (' . $user->user_login . ')',
				'user_login'   => $user->user_login,
			);
		}
		wp_send_json_success( $result );
	}
}
