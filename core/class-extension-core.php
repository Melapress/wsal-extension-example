<?php

namespace WPWhiteSecurity\ActivityLog\Extensions\Common;

use \WPWhiteSecurity\ActivityLog\Extensions\Common\PluginInstaller as PluginInstaller;

if ( ! class_exists( '\WPWhiteSecurity\ActivityLog\Extensions\Common\Core' ) ) {

	class Core {

		/**
		 * Extension text-domain.
		 *
		 * @var string
		 */
		private $extension_text_domain;

		/**
		 * Extension custom alert path.
		 *
		 * @var string
		 */
		private $custom_alert_path;

		/**
		 * Extension sensor path.
		 *
		 * @var string
		 */
		private $custom_sensor_path;

		public function __construct( $core_settings = '' ) {
			if ( ! empty( $core_settings ) && isset( $core_settings['text_domain'] ) ) {
				$this->extension_text_domain = $core_settings['text_domain'];
			}
			if ( ! empty( $core_settings ) && isset( $core_settings['custom_alert_path'] ) ) {
				$this->custom_alert_path = $core_settings['custom_alert_path'];
			}
			if ( ! empty( $core_settings ) && isset( $core_settings['custom_sensor_path'] ) ) {
				$this->custom_sensor_path = $core_settings['custom_sensor_path'];
			}
			$this->add_actions();
		}

		/**
		 * Add actions.
		 */
		public function add_actions() {
			add_action( 'admin_init', array( $this, 'init_install_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_ajax_dismiss_notice', array( $this, 'dismiss_notice' ) );
			/**
			 * Hook into WSAL's action that runs before sensors get loaded.
			 */
			add_action( 'wsal_before_sensor_load', array( $this, 'add_custom_sensors_and_events_dirs' ) );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		}

		/**
		 * Load plugin text domain.
		 */
		public function load_plugin_textdomain() {
			$language_path      = basename( dirname( dirname( __FILE__ ) ) );
			$core_language_path = basename( dirname( __FILE__ ) );
			$core_language_path = $language_path . '/' . $core_language_path . '/languages';
			load_plugin_textdomain( 'wsal-extension-core', false, $core_language_path );
			if ( isset( $this->extension_text_domain ) && ! empty( $this->extension_text_domain ) ) {
				load_plugin_textdomain( $this->extension_text_domain, false, $language_path . '/languages' );
			}
		}

		/**
		 * Display admin notice if WSAL is not installed.
		 */
		function install_notice() {
			$plugin_installer = new PluginInstaller();
			$screen           = get_current_screen();

			// First lets check if WSAL is installed, but not active.
			if ( $plugin_installer->is_plugin_installed( 'wp-security-audit-log/wp-security-audit-log.php' ) && ! is_plugin_active( 'wp-security-audit-log/wp-security-audit-log.php' ) ) : ?>
                <div class="notice notice-success is-dismissible wsal-installer-notice">
					<?php
					printf(
						'<p>%1$s &nbsp;&nbsp;<button class="activate-addon button button-primary" data-plugin-slug="wp-security-audit-log/wp-security-audit-log.php" data-plugin-download-url="%2$s" data-plugins-network="%4$s" data-nonce="%3$s">%5$s</button><span class="spinner" style="display: none; visibility: visible; float: none; margin: 0 0 0 8px;"></span></p>',
						esc_html__( 'WP Activity Log is installed but not active.', 'wsal-extension-core' ),
						esc_url( 'https://downloads.wordpress.org/plugin/wp-security-audit-log.latest-stable.zip' ),
						esc_attr( wp_create_nonce( 'wsal-install-addon' ) ),
						( is_a( $screen, '\WP_Screen' ) && isset( $screen->id ) && 'plugins-network' === $screen->id ) ? true : false, // confirms if we are on a network or not.
						esc_html__( 'Activate WP Activity Log.', 'wp-security-audit-log' )
					);
					?>
                </div>
			<?php elseif ( ! class_exists( 'WpSecurityAuditLog' ) ) : ?>
                <div class="notice notice-success is-dismissible wsal-installer-notice">
					<?php
					printf(
						'<p>%1$s &nbsp;&nbsp;<button class="install-wsal button button-primary" data-plugin-slug="wp-security-audit-log/wp-security-audit-log.php" data-plugin-download-url="%2$s" data-plugins-network="%4$s" data-nonce="%3$s">%5$s</button><span class="spinner" style="display: none; visibility: visible; float: none; margin: 0 0 0 8px;"></span></p>',
						esc_html__( 'This extension requires the WP Activity Log plugin to work.', 'wsal-extension-core' ),
						esc_url( 'https://downloads.wordpress.org/plugin/wp-security-audit-log.latest-stable.zip' ),
						esc_attr( wp_create_nonce( 'wsal-install-addon' ) ),
						( is_a( $screen, '\WP_Screen' ) && isset( $screen->id ) && 'plugins-network' === $screen->id ) ? true : false, // confirms if we are on a network or not.
						esc_html__( 'Install WP Activity Log.', 'wsal-extension-core' )
					);
					?>
                </div>
			<?php
			endif;
		}

		function init_install_notice() {
			// Check if main plugin is installed.
			if ( ! class_exists( 'WpSecurityAuditLog' ) && ! class_exists( 'WSAL_AlertManager' ) ) {
				// Check if the notice was already dismissed by the user.
				if ( get_option( 'wsal_core_notice_dismissed' ) != true ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- this may be truthy and not explicitly bool
					if ( ! class_exists( 'PluginInstaller' ) ) {
						require_once 'class-plugin-installer.php';
					}
					$plugin_installer = new PluginInstaller();
					if ( is_multisite() && is_network_admin() ) {
						add_action( 'admin_notices', array( $this, 'install_notice' ) );
						add_action( 'network_admin_notices', array( $this, 'install_notice' ), 10, 1 );
					} elseif ( ! is_multisite() ) {
						add_action( 'admin_notices', array( $this, 'install_notice' ) );
					}
				}
			} else {
				// Reset the notice if the class is not found.
				delete_option( 'wsal_core_notice_dismissed' );
			}
		}

		/**
		 * Load our js file to handle ajax.
		 */
		function enqueue_scripts() {
			wp_enqueue_script(
				'wsal-core-scripts',
				plugins_url( 'assets/js/scripts.js', __FILE__ ),
				array( 'jquery' ),
				'1.0',
				true
			);

			$script_data = array(
				'ajaxURL'           => admin_url( 'admin-ajax.php' ),
				'installing'        => esc_html__( 'Installing, please wait', 'wsal-extension-core' ),
				'already_installed' => esc_html__( 'Already installed', 'wsal-extension-core' ),
				'installed'         => esc_html__( 'Extension installed', 'wsal-extension-coreg' ),
				'activated'         => esc_html__( 'Extension activated', 'wsal-extension-core' ),
				'failed'            => esc_html__( 'Install failed', 'wsal-extension-core' ),
			);

			// Send ajax url to JS file.
			wp_localize_script( 'wsal-core-scripts', 'WSALCoreData', $script_data );
		}


		/**
		 * Update option if user clicks dismiss.
		 */
		function dismiss_notice() {
			update_option( 'wsal_core_notice_dismissed', true );
		}

		/**
		 * Used to hook into the `wsal_before_sensor_load` action to add some filters
		 * for including custom sensor and event directories.
		 */
		function add_custom_sensors_and_events_dirs( $sensor ) {
			add_filter( 'wsal_custom_sensors_classes_dirs', array( $this, 'add_custom_sensors_path' ) );
			add_filter( 'wsal_custom_alerts_dirs', array( $this, 'add_custom_events_path' ) );
			return $sensor;
		}

		/**
		 * Adds a new path to the sensors directory array which is checked for when the
		 * plugin loads the sensors.
		 *
		 * @param array $paths An array containing paths on the filesystem.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		function add_custom_sensors_path( $paths = array() ) {
			$paths   = ( is_array( $paths ) ) ? $paths : array();
			$paths[] = $this->custom_sensor_path;

			return $paths;
		}

		/**
		 * Adds a new path to the custom events directory array which is checked for
		 * when the plugin loads all of the events.
		 *
		 * @param array $paths An array containing paths on the filesystem.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		function add_custom_events_path( $paths ) {
			$paths   = ( is_array( $paths ) ) ? $paths : array();
			$paths[] = $this->custom_alert_path;

			return $paths;
		}

	}
}
