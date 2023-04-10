<?php
/**
 * Custom Alerts for My custom sensor.
 *
 * @since   latest
 *
 * @package wsal
 * @subpackage wsal-my-custom-alerts
 */

declare(strict_types=1);

namespace WSAL\Custom_Alerts;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\WSAL\Custom_Alerts\My_Custom_Alerts' ) ) {
	/**
	 * Custom sensor for Yoast plugin.
	 *
	 * @since latest
	 */
	class My_Custom_Alerts {

		/**
		 * Returns the structure of the alerts for extension.
		 *
		 * @return array
		 *
		 * @since latest
		 */
		public static function get_custom_alerts(): array {
			return array(
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
		}
	}
}
