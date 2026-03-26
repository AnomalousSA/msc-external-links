<?php
/**
 * Uninstall MSC External Links.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'mscel_options' );

if ( false ) {
	$hook = 'msc-external-links_cron_event';
	$next = wp_next_scheduled( $hook );

	while ( $next ) {
		wp_unschedule_event( $next, $hook );
		$next = wp_next_scheduled( $hook );
	}
}

if ( false ) {
	global $wpdb;
	$pattern = $wpdb->esc_like( 'msc-external-links_' ) . '%';
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
			$pattern
		)
	);
}
