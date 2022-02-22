<?php
/**
 * Our list of events.
 *
 * @package wsal
 */

// phpcs:disable WordPress.WP.I18n.UnorderedPlaceholdersText 
// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment

$custom_alerts = array(
	esc_html__( 'PLUGINNAME', 'wp-security-audit-log' ) => array(
		esc_html__( 'PLUGINNAME Content', 'wp-security-audit-log' ) => array(

			array(
				1234,
				WSAL_LOW,
				esc_html__( 'Test event X', 'wp-security-audit-log' ),
				esc_html__( 'This is a test event', 'wp-security-audit-log' ),
				'wpforms',
				'created',
			),

		),
	),
);
