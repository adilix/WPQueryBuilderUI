<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Ajax_Preview {
	public function handle() {
		// phpcs:ignore WordPress.Security.NonceVerification
		$raw = isset( $_POST['query_args'] ) ? wp_unslash( $_POST['query_args'] ) : array();
		if ( is_string( $raw ) ) {
			$raw = json_decode( $raw, true ) ?: array();
		}

		$sanitizer  = new WPQBUI_Query_Sanitizer();
		$clean_args = $sanitizer->sanitize( (array) $raw );

		$validator   = new WPQBUI_Query_Validator();
		$validations = $validator->validate( $clean_args );

		$def        = new WPQBUI_Query_Definition();
		$def->query_args = $clean_args;

		$previewer    = new WPQBUI_Query_Previewer();
		$resolved     = $previewer->resolve( $def );

		$php_gen  = new WPQBUI_Codegen_Php();
		// phpcs:ignore WordPress.Security.NonceVerification
		$include_loop = ! empty( $_POST['include_loop'] );
		$php_code = $php_gen->generate( $resolved, array( 'include_loop' => $include_loop ) );

		$shortcode = '';
		// phpcs:ignore WordPress.Security.NonceVerification
		$query_id = absint( $_POST['query_id'] ?? 0 );
		if ( $query_id ) {
			$sc_gen    = new WPQBUI_Codegen_Shortcode();
			$shortcode = $sc_gen->generate( $query_id );
		}

		wp_send_json_success( array(
			'resolved_args'      => $resolved,
			'php_code'           => $php_code,
			'shortcode'          => $shortcode,
			'validation_results' => $validations,
		) );
	}
}
