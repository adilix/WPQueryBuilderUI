<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Posts {
	public function handle() {
		// phpcs:ignore WordPress.Security.NonceVerification
		$post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : 'any';
		// phpcs:ignore WordPress.Security.NonceVerification
		$search    = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification
		$paged     = max( 1, absint( $_POST['paged'] ?? 1 ) );

		$q = new WP_Query( array(
			'post_type'              => $post_type ?: 'any',
			's'                      => $search,
			'posts_per_page'         => 30,
			'paged'                  => $paged,
			'post_status'            => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );

		$result = array();
		foreach ( $q->posts as $post ) {
			$result[] = array(
				'ID'         => $post->ID,
				'post_title' => $post->post_title ?: '(no title)',
				'post_type'  => $post->post_type,
			);
		}
		wp_send_json_success( array(
			'posts'       => $result,
			'total_pages' => (int) $q->max_num_pages,
			'paged'       => $paged,
		) );
	}
}
