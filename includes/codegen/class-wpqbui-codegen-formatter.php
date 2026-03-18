<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pretty-prints PHP arrays for code generation output.
 */
class WPQBUI_Codegen_Formatter {

	/**
	 * @param array  $array
	 * @param int    $depth       Current indent depth.
	 * @param string $indent_char Indent character(s).
	 * @return string
	 */
	public function format( array $array, $depth = 1, $indent_char = "\t" ) {
		if ( empty( $array ) ) {
			return '[]';
		}
		$is_assoc = $this->is_assoc( $array );
		$indent   = str_repeat( $indent_char, $depth );
		$outdent  = str_repeat( $indent_char, $depth - 1 );

		$parts = array();
		foreach ( $array as $key => $value ) {
			$key_str = $is_assoc ? $this->format_value( $key ) . ' => ' : '';
			if ( is_array( $value ) ) {
				$val_str = $this->format( $value, $depth + 1, $indent_char );
			} else {
				$val_str = $this->format_value( $value );
			}
			$parts[] = $indent . $key_str . $val_str;
		}

		return "[\n" . implode( ",\n", $parts ) . ",\n" . $outdent . ']';
	}

	/**
	 * Format a scalar PHP value for code output.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function format_value( $value ) {
		if ( true === $value ) {
			return 'true';
		}
		if ( false === $value ) {
			return 'false';
		}
		if ( null === $value ) {
			return 'null';
		}
		if ( is_int( $value ) ) {
			return (string) $value;
		}
		if ( is_float( $value ) ) {
			return (string) $value;
		}
		// String: check if numeric to avoid quoting numbers stored as strings.
		if ( is_numeric( $value ) && ! is_string( $value ) ) {
			return (string) $value;
		}
		return "'" . addslashes( $value ) . "'";
	}

	private function is_assoc( array $array ) {
		if ( array() === $array ) {
			return false;
		}
		return array_keys( $array ) !== range( 0, count( $array ) - 1 );
	}
}
