<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generates a [wpqbui] shortcode string for a saved query.
 */
class WPQBUI_Codegen_Shortcode {

	/**
	 * @param int   $query_id
	 * @param array $options  { 'extra_attrs' => array }
	 * @return string  Shortcode string, e.g. [wpqbui id="3"]
	 */
	public function generate( $query_id, array $options = array() ) {
		$attrs = array( 'id' => (int) $query_id );
		if ( ! empty( $options['extra_attrs'] ) && is_array( $options['extra_attrs'] ) ) {
			foreach ( $options['extra_attrs'] as $k => $v ) {
				$attrs[ sanitize_key( $k ) ] = esc_attr( $v );
			}
		}
		$attr_str = '';
		foreach ( $attrs as $k => $v ) {
			$attr_str .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
		return '[wpqbui' . $attr_str . ']';
	}
}
