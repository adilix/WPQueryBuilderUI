<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPQBUI_Activator {
	public static function activate() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table          = $wpdb->prefix . 'wpqbui_queries';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id          bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name        varchar(200)        NOT NULL DEFAULT '',
			slug        varchar(200)        NOT NULL DEFAULT '',
			description text,
			query_args  longtext            NOT NULL,
			created_at  datetime            NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at  datetime            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			created_by  bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (id),
			UNIQUE KEY slug (slug)
		) {$charset_collate};";

		dbDelta( $sql );

		add_option( 'wpqbui_db_version', WPQBUI_DB_VERSION );
	}
}
