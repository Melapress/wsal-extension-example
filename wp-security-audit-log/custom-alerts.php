<?php
/**
 * Use this file to register your custom events.
 */

$custom_alerts = array(
	__( 'PLUGINNAME', 'wp-security-audit-log' ) => array(
		__( 'PLUGINNAME Content', 'wp-security-audit-log' ) => array(

			array(
				1234,
				WSAL_LOW,
				__( 'Test event X', 'wp-security-audit-log' ),
				__( 'This is a test event', 'wp-security-audit-log' ),
				'wpforms',
				'created',
			),

		),
	),
);
