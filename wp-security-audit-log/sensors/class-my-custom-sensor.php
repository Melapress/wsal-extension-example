<?php
/**
 * Sensor: My custom sensor
 *
 * My custom sensor file.
 *
 * @package Wsal
 * @since latest
 */

declare(strict_types=1);

namespace WSAL\Plugin_Sensors;

use WSAL\Controllers\Alert_Manager;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\WSAL\Plugin_Sensors\My_Custom_Sensor' ) ) {
	/**
	 * Custom Sensors for PLUGINNAME plugin.
	 *
	 * Class file for alert manager.
	 *
	 * @since   1.0.0
	 * @package wsal
	 * @subpackage wsal-PLUGINNAME
	 */
	class My_Custom_Sensor {

		/**
		 * Listening to events using hooks.
		 * Here you can code your own custom sensors for triggering your custom events.
		 */
		public static function init() {
			// Begin adding your own custom hook and functions here.
		}
	}
}
