<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Post_Types {
	public function handle() {
		$types = get_post_types( array(), 'objects' );
		$result = array(
			array( 'slug' => 'any', 'label' => __( 'Any (all post types)', 'wpqbui' ) ),
		);
		foreach ( $types as $type ) {
			$result[] = array(
				'slug'  => $type->name,
				'label' => $type->label . ' (' . $type->name . ')',
			);
		}
		wp_send_json_success( $result );
	}
}
