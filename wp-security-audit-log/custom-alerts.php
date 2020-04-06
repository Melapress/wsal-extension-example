<?php

$custom_alerts = array(
	__( 'PLUGINNAME', 'wp-security-audit-log' ) => array(
		__( 'PLUGINNAME Content', 'wp-security-audit-log' ) => array(

			array(
				10000,
				WSAL_LOW,
				__( 'Test event', 'wp-security-audit-log' ),
				__( 'This is a test event', 'wp-security-audit-log' ),
				'wpforms',
				'created',
			),

		),
	),
);
