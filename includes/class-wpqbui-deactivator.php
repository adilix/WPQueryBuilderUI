<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Deactivator {
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
