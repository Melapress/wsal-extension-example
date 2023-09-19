<?php
/**
 * Plugin Name: WP Activity Log Extension for (Plugin name)
 * Plugin URI: https://wpactivitylog.com/extensions/
 * Description: A WP Activity Log plugin extension
 * Text Domain: my-custom-textdomain
 * Author: WP White Security
 * Author URI: http://www.wpwhitesecurity.com/
 * Version: 1.0.0
 * License: GPL2
 * Network: true
 *
 * @package Wsal
 * @subpackage Wsal Custom Events Loader
 */

use WSAL\Helpers\Classes_Helper;

/*
	Copyright(c) 2020  WP White Security  (email : info@wpwhitesecurity.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 REQUIRED. Here we include and fire up the main core class. This will be needed regardless so be sure to leave line 37-39 in tact.
*/
require_once plugin_dir_path( __FILE__ ) . 'core/class-extension-core.php';
$wsal_extension = new WPWhiteSecurity\ActivityLog\Extensions\Common\Core( __FILE__, 'wsal-example-extension' );

/*
	From here, you may now place your custom code. Examples of the functions
	needed in a typical extension are provided as a basis, however you MUST
	rename the functions (make them unique) to avoid duplication conflicts.

	Each function provide is complete with internal workings, simply uncomment
	and edit as you wish.
*/

/**
 * Register a custom event object within WSAL.
 *
 * @param array $objects array of objects current registered within WSAL.
 */
function wsal_extension_core_add_custom_event_objects( $objects ) {
	// $new_objects = array(
	// 'my_custom_obj' => esc_html__( 'My Object Label (Typically the name of the plugin your creating an event for)', 'wp-security-audit-log' ),
	// );
	//
	// combine the two arrays.
	// $objects = array_merge( $objects, $new_objects );
	//
	return $objects;
}

/*
	Filter in our custom functions into WSAL.
 */
add_filter( 'wsal_event_objects', 'wsal_extension_core_add_custom_event_objects', 10, 2 );

add_action(
	'wsal_sensors_manager_add',
	/**
	* Adds sensors classes to the Class Helper
	*
	* @return void
	*
	* @since latest
	*/
	function () {
		require_once __DIR__ . '/wp-security-audit-log/sensors/class-my-custom-sensor.php';

		Classes_Helper::add_to_class_map(
			array(
				'WSAL\\Plugin_Sensors\\My_Custom_Sensor' => __DIR__ . '/wp-security-audit-log/sensors/class-my-custom-sensor.php',
			)
		);
	}
);

add_action(
	'wsal_custom_alerts_register',
	/**
	* Adds sensors classes to the Class Helper
	*
	* @return void
	*
	* @since latest
	*/
	function () {
		require_once __DIR__ . '/wp-security-audit-log/class-my-custom-alerts.php';

		Classes_Helper::add_to_class_map(
			array(
				'WSAL\\Custom_Alerts\\My_Custom_Alerts' => __DIR__ . '/wp-security-audit-log/class-my-custom-alerts.php',
			)
		);
	}
);
