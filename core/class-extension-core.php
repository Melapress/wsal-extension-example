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

		/**
		 * Extension plugin name.
		 *
		 * @var string
		 */
		private $extension_plugin_name = null;

		/**
		 * Extension plugin file path.
		 *
		 * @var string
		 */
		private $extension_main_file_path;

		/**
		 * Was the user been notified with message already
		 *
		 * @since 1.1.0
		 *
		 * @var boolean
		 */
		private static $adminNoticeAlreadyShown = false;

		public function __construct( $extension_main_file_path = '', $text_domain = '' ) {
			// Backward compatibilty to avoid site crashes when updating extensions.
			if ( is_array( $extension_main_file_path ) ) {
				$this->extension_text_domain = ( isset( $extension_main_file_path['text_domain'] ) ) ? $extension_main_file_path['text_domain'] : '';
				$this->custom_alert_path     = ( isset( $extension_main_file_path['custom_alert_path'] ) ) ? $extension_main_file_path['custom_alert_path'] : '';
				$this->custom_sensor_path    = ( isset( $extension_main_file_path['custom_sensor_path'] ) ) ? $extension_main_file_path['custom_sensor_path'] : '';
				$this->extension_plugin_name = '';
			}
			// If we dont have array, then continue with the as normal.
			else {
				$this->extension_text_domain  = $text_domain;
				$this->custom_alert_path      = trailingslashit( dirname( $extension_main_file_path ) ) . 'wp-security-audit-log';
				$this->custom_sensor_path     = trailingslashit( trailingslashit( dirname( $extension_main_file_path ) ) . 'wp-security-audit-log' . DIRECTORY_SEPARATOR . 'custom-sensors' );
			}

			$this->extension_main_file_path = $extension_main_file_path;

			$this->add_actions();
		}

		/**
		 * Add actions.
		 */
		public function add_actions() {
			add_action( 'admin_init', array( $this, 'init_install_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_ajax_dismiss_notice', array( $this, 'dismiss_notice' ) );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_filter( 'wsal_custom_alerts_dirs', array( $this, 'add_custom_events_path' ) );
			add_filter( 'wsal_custom_sensors_classes_dirs', array( $this, 'add_custom_sensors_path' ) );
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

			$freeInstalled = false;
			$premiumInstalled = false;
			$freeActivated = false;
			$premiumActivated = false;

			/* Starting checks */
			/* Is there free version installed */
			if ( $plugin_installer->is_plugin_installed( 'wp-security-audit-log/wp-security-audit-log.php' )) {
				$freeInstalled = true;
			}
			/* Is there premium version installed */
			if ( $plugin_installer->is_plugin_installed( 'wp-security-audit-log-premium/wp-security-audit-log.php' )) {
				$premiumInstalled = true;
			}
			/* End checks */

			if ( $freeInstalled || $premiumInstalled ) {
				/* We have plugin installed */
				/* Is free version activated */
				if (is_plugin_active( 'wp-security-audit-log/wp-security-audit-log.php' )) {
					$freeActivated = true;
				}
				/* Is premium version activated */
				if (is_plugin_active( 'wp-security-audit-log-premium/wp-security-audit-log.php' )) {
					$premiumActivated = true;
				}

				if ( $freeActivated || $premiumActivated ) {
					/* There is installed and activated plugin - bounce */
					return;
				} else {

					if ( ! self::$adminNoticeAlreadyShown ) {
					/* Notify the user that the activity log is not active */
					?>
					<div class="notice notice-success is-dismissible wsal-installer-notice">
						<?php
						printf(
							'<p>%1$s &nbsp;&nbsp;<button class="activate-addon button button-primary" data-plugin-slug="wp-security-audit-log%6$s/wp-security-audit-log.php" data-plugin-download-url="%2$s" data-plugins-network="%4$s" data-nonce="%3$s">%5$s</button><span class="spinner" style="display: none; visibility: visible; float: none; margin: 0 0 0 8px;"></span></p>',
							sprintf(
								esc_html__( 'The %s extension requires the WP Activity Log plugin to work, which is already installed on your website.', 'wsal-extension-core' ),
								$this->getExtentionPluginName()
							),
							esc_url( 'https://downloads.wordpress.org/plugin/wp-security-audit-log.latest-stable.zip' ),
							esc_attr( wp_create_nonce( 'wsal-install-addon' ) ),
							( is_a( $screen, '\WP_Screen' ) && isset( $screen->id ) && 'plugins-network' === $screen->id ) ? true : false, // confirms if we are on a network or not.
							esc_html__( 'Activate WP Activity Log.', 'wp-security-audit-log' ),
							(($premiumInstalled)?'-premium':'')
						);
						?>
					</div>
					<?php
						self::$adminNoticeAlreadyShown = true;
					}
				}
			} elseif ( ! class_exists( 'WpSecurityAuditLog' ) ) {
				if ( ! self::$adminNoticeAlreadyShown ) {
					/* Notify the user that the activity log is not installed */
				?>
                <div class="notice notice-success is-dismissible wsal-installer-notice">
					<?php
					printf(
						'<p>%1$s &nbsp;&nbsp;<button class="install-wsal button button-primary" data-plugin-slug="wp-security-audit-log/wp-security-audit-log.php" data-plugin-download-url="%2$s" data-plugins-network="%4$s" data-nonce="%3$s">%5$s</button><span class="spinner" style="display: none; visibility: visible; float: none; margin: 0 0 0 8px;"></span></p>',
						sprintf(
							esc_html__( 'The %s extension requires the WP Activity Log plugin to work.', 'wsal-extension-core' ),
							$this->getExtentionPluginName()
                        ),
						esc_url( 'https://downloads.wordpress.org/plugin/wp-security-audit-log.latest-stable.zip' ),
						esc_attr( wp_create_nonce( 'wsal-install-addon' ) ),
						( is_a( $screen, '\WP_Screen' ) && isset( $screen->id ) && 'plugins-network' === $screen->id ) ? true : false, // confirms if we are on a network or not.
						esc_html__( 'Install WP Activity Log.', 'wsal-extension-core' )
					);
					?>
                </div>
			<?php
					self::$adminNoticeAlreadyShown = true;
				}
			};
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

		/**
		 * Getter for plugin name
		 *
		 * @since 1.1.0
		 *
		 * @return string
		 */
		public function getExtentionPluginName() {
			if ( null === $this->extension_plugin_name ) {
				$this->extension_plugin_name = '';

				if ( ! is_array( $this->extension_main_file_path ) ) {
					if ( is_admin() ) {
						if ( ! \function_exists( 'get_plugin_data' ) ) {
							require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						}
						$plugin_data                 = \get_plugin_data( $this->extension_main_file_path );
						$this->extension_plugin_name = $plugin_data['Name'];
					}
				}
			}

			return $this->extension_plugin_name;
		}
	}
}
