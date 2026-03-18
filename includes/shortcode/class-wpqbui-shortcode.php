<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end [wpqbui] shortcode handler.
 */
class WPQBUI_Shortcode {

	public function register() {
		$settings = get_option( 'wpqbui_settings', array() );
		if ( empty( $settings['enable_shortcode'] ) ) {
			return;
		}
		add_shortcode( 'wpqbui', array( $this, 'render' ) );
	}

	/**
	 * @param array $atts  Shortcode attributes.
	 * @return string  HTML output.
	 */
	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'       => 0,
				'template' => '',
			),
			$atts,
			'wpqbui'
		);

		$id = absint( $atts['id'] );
		if ( ! $id ) {
			return '';
		}

		$repo = new WPQBUI_Query_Repository();
		$def  = $repo->find_by_id( $id );
		if ( ! $def ) {
			return '';
		}

		$previewer = new WPQBUI_Query_Previewer();
		$args      = $previewer->resolve( $def );

		$query = new WP_Query( $args );

		ob_start();

		// Allow theme to override template.
		$template = '';
		if ( ! empty( $atts['template'] ) ) {
			$template = locate_template( sanitize_text_field( $atts['template'] ) );
		}
		if ( ! $template ) {
			$template = locate_template( array( 'wpqbui/query-results.php' ) );
		}
		if ( ! $template ) {
			$template = WPQBUI_DIR . 'partials/shortcode-results.php';
		}

		if ( $query->have_posts() ) {
			include $template;
		}

		wp_reset_postdata();

		return ob_get_clean();
	}
}
