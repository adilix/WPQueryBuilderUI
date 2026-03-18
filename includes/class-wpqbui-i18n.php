<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_I18n {
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'wpqbui',
			false,
			dirname( WPQBUI_BASENAME ) . '/languages'
		);
	}
}
