<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generates a PHP code string for a given set of WP_Query args.
 */
class WPQBUI_Codegen_Php {

	/** @var WPQBUI_Codegen_Formatter */
	private $formatter;

	public function __construct() {
		$this->formatter = new WPQBUI_Codegen_Formatter();
	}

	/**
	 * @param array $args     Resolved WP_Query args.
	 * @param array $options  { 'var_name' => string, 'include_loop' => bool, 'indent' => string }
	 * @return string  PHP source code.
	 */
	public function generate( array $args, array $options = array() ) {
		$var_name     = sanitize_title( $options['var_name'] ?? 'query' );
		$var_name     = $var_name ?: 'query';
		$include_loop = (bool) ( $options['include_loop'] ?? false );
		$indent       = $options['indent'] ?? "\t";

		$args_str = $this->formatter->format( $args, 1, $indent );

		$lines   = array();
		$lines[] = '<?php';
		$lines[] = '';
		$lines[] = '$args = ' . $args_str . ';';
		$lines[] = '';
		$lines[] = '$' . $var_name . ' = new WP_Query( $args );';

		if ( $include_loop ) {
			$lines[] = '';
			$lines[] = 'if ( $' . $var_name . '->have_posts() ) {';
			$lines[] = $indent . 'while ( $' . $var_name . '->have_posts() ) {';
			$lines[] = $indent . $indent . '$' . $var_name . '->the_post();';
			$lines[] = $indent . $indent . '// Your template code here.';
			$lines[] = $indent . $indent . 'the_title( \'<h2>\', \'</h2>\' );';
			$lines[] = $indent . '}';
			$lines[] = '}';
			$lines[] = '';
			$lines[] = 'wp_reset_postdata();';
		}

		return implode( "\n", $lines );
	}
}
