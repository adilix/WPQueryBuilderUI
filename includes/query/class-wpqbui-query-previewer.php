<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves a query definition into final WP_Query args without running the query.
 */
class WPQBUI_Query_Previewer {

	/**
	 * @param WPQBUI_Query_Definition $def
	 * @return array  Resolved args ready for WP_Query.
	 */
	public function resolve( WPQBUI_Query_Definition $def ) {
		$args = $def->to_args_array();

		// Apply WordPress defaults where they are meaningful.
		if ( ! isset( $args['post_type'] ) ) {
			$args['post_type'] = 'post';
		}
		if ( ! isset( $args['post_status'] ) ) {
			$args['post_status'] = 'publish';
		}
		if ( ! isset( $args['posts_per_page'] ) ) {
			$args['posts_per_page'] = (int) get_option( 'posts_per_page', 10 );
		}
		if ( ! isset( $args['orderby'] ) ) {
			$args['orderby'] = 'date';
		}
		if ( ! isset( $args['order'] ) ) {
			$args['order'] = 'DESC';
		}

		return $args;
	}
}
