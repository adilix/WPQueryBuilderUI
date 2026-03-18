<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table = $wpdb->prefix . 'wpqbui_queries';
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );

delete_option( 'wpqbui_db_version' );
delete_option( 'wpqbui_settings' );
