<?php
/**
 * Custom Sensors for PLUGINNAME
 *
 * Class file for alert manager.
 *
 * @since   1.0.0
 * @package Wsal
 */

/**
 * Custom sensor class to process WPForms events.
 *
 * @since 1.0.0
 */
class WSAL_Sensors_PLUGINNAMESensor extends WSAL_AbstractSensor {

	/**
	 * Holds a cached value if the checked alert has recently fired.
	 *
	 * @var null|array
	 */
	private $cached_alert_checks = null;

	/**
	 * Hook events related to sensor.
	 *
	 * @since 1.0.0
	 */
	public function HookEvents() {

	}

	/**
	 * Get Post Data.
	 *
	 * Collect old post data before post update event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id - Post ID.
	 */
	public function get_before_post_edit_data( $post_id ) {
		$post_id = absint( $post_id ); // Making sure that the post id is integer.
		$post    = get_post( $post_id ); // Get post.

		// If post exists.
		if ( ! empty( $post ) && $post instanceof WP_Post ) {
			$this->_old_post = $post;
		}
	}

}
