<?php

if ( ! class_exists( 'WSAL_Extension_Core' ) ) {
	class WSAL_Extension_Core {

		/**
		 * Instance wrapper.
		 *
		 * @var object
		 */
		private static $instance = null;

		/**
		 * Return plugin instance.
		 */
		public static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {

		}

		/**
		 * Fire up classes.
		 */
		public function init() {
			$this->add_actions();
		}

		public function add_actions() {
			add_action( 'plugins_loaded', array( $this, 'wsal_extension_core_init_actions' ) );
			add_action( 'admin_init', array( $this, 'wsal_extension_core_init_install_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'wsal_extension_core_scripts' ) );
			add_action( 'wp_ajax_wsal_extension_core_dismiss_notice', array( $this, 'wsal_extension_core_dismiss_notice' ) );
			/**
			* Hook into WSAL's action that runs before sensors get loaded.
			*/
			add_action( 'wsal_before_sensor_load', array( $this, 'wsal_extension_core_mu_plugin_add_custom_sensors_and_events_dirs' ) );
			add_action( 'plugins_loaded', array( $this,  'wsal_extension_core_load_plugin_textdomain' ) );
		}

		/**
		 * Load plugin text domain.
		 */
		public function wsal_extension_core_load_plugin_textdomain() {
			load_plugin_textdomain( 'wp-security-audit-log', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Check if plugin is being installed via a multisite child site, if so, show notice.
		 */
		function wsal_extension_core_init_actions() {
			if ( is_multisite() && function_exists( 'is_super_admin' ) && is_super_admin() && ! is_network_admin() ) {
				if ( ! function_exists( 'is_plugin_active' ) ) {
					include_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
			}
		}

		/**
		 * Display admin notice if WSAL is not installed.
		 */
		function wsal_extension_core_install_notice() {
			$plugin_installer = new WSALExtension_PluginInstallerAction();
			$screen           = get_current_screen();

			// First lets check if WSAL is installed, but not active.
			if ( $plugin_installer->is_plugin_installed( 'wp-security-audit-log/wp-security-audit-log.php' ) && ! is_plugin_active( 'wp-security-audit-log/wp-security-audit-log.php' ) ) : ?>
				<div class="notice notice-success is-dismissible wsal-addons-template-notice">
					<?php
						printf(
							'<p>%1$s &nbsp;&nbsp;<button class="activate-addon button button-primary" data-plugin-slug="wp-security-audit-log/wp-security-audit-log.php" data-plugin-download-url="%2$s" data-plugins-network="%4$s" data-nonce="%3$s">%5$s</button><span class="spinner" style="display: none; visibility: visible; float: none; margin: 0 0 0 8px;"></span></p>',
							esc_html__( 'WP Activity Log is installed but not active.', 'wp-security-audit-log' ),
							esc_url( 'https://downloads.wordpress.org/plugin/wp-security-audit-log.latest-stable.zip' ),
							esc_attr( wp_create_nonce( 'wsal-install-addon' ) ),
							( is_a( $screen, '\WP_Screen' ) && isset( $screen->id ) && 'plugins-network' === $screen->id ) ? true : false, // confirms if we are on a network or not.
							esc_html__( 'Activate WP Activity Log.', 'wp-security-audit-log' )
						);
					?>
				</div>
			<?php elseif ( ! class_exists( 'WpSecurityAuditLog' ) ) : ?>
				<div class="notice notice-success is-dismissible wsal-addons-template-notice">
					<?php
						printf(
							'<p>%1$s &nbsp;&nbsp;<button class="install-wsal button button-primary" data-plugin-slug="wp-security-audit-log/wp-security-audit-log.php" data-plugin-download-url="%2$s" data-plugins-network="%4$s" data-nonce="%3$s">%5$s</button><span class="spinner" style="display: none; visibility: visible; float: none; margin: 0 0 0 8px;"></span></p>',
							esc_html__( 'This extension requires the WP Security Audit Log plugin to work.', 'wp-security-audit-log' ),
							esc_url( 'https://downloads.wordpress.org/plugin/wp-security-audit-log.latest-stable.zip' ),
							esc_attr( wp_create_nonce( 'wsal-install-addon' ) ),
							( is_a( $screen, '\WP_Screen' ) && isset( $screen->id ) && 'plugins-network' === $screen->id ) ? true : false, // confirms if we are on a network or not.
							esc_html__( 'Install WP Activity Log.', 'wp-security-audit-log' )
						);
					?>
				</div>
			<?php
			endif;
		}

		function wsal_extension_core_init_install_notice() {
			// Check if main plugin is installed.
			if ( ! class_exists( 'WpSecurityAuditLog' ) && ! class_exists( 'WSAL_AlertManager' ) ) {
				// Check if the notice was already dismissed by the user.
				if ( get_option( 'wsal_forms_notice_dismissed' ) != true ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- this may be truthy and not explicitly bool
					if ( ! class_exists( 'WSALExtension_PluginInstallerAction' ) ) {
						require_once 'wp-security-audit-log/classes/PluginInstallerAction.php';
					}
					$plugin_installer = new WSALExtension_PluginInstallerAction();
					if ( is_multisite() && is_network_admin() ) {
						add_action( 'admin_notices', array( $this, 'wsal_extension_core_install_notice' ) );
						add_action( 'network_admin_notices', array( $this, 'wsal_extension_core_install_notice' ), 10, 1 );
					} elseif ( ! is_multisite() ) {
						add_action( 'admin_notices', array( $this, 'wsal_extension_core_install_notice' ) );
					}

				}
			} else {
				// Reset the notice if the class is not found.
				delete_option( 'wsal_forms_notice_dismissed' );
			}
		}

		/**
		 * Load our js file to handle ajax.
		 */
		function wsal_extension_core_scripts() {
			wp_enqueue_script(
				'wsal-addons-template-scripts',
				plugins_url( 'assets/js/scripts.js', __FILE__ ),
				array( 'jquery' ),
				'1.0',
				true
			);

			$script_data = array(
				'ajaxURL'           => admin_url( 'admin-ajax.php' ),
				'installing'        => esc_html__( 'Installing, please wait', 'wp-security-audit-log' ),
				'already_installed' => esc_html__( 'Already installed', 'wp-security-audit-log' ),
				'installed'         => esc_html__( 'Extension installed', 'wp-security-audit-log' ),
				'activated'         => esc_html__( 'Extension activated', 'wp-security-audit-log' ),
				'failed'            => esc_html__( 'Install failed', 'wp-security-audit-log' ),
			);

			// Send ajax url to JS file.
			wp_localize_script( 'wsal-addons-template-scripts', 'WSALWPFormsData', $script_data );
		}


		/**
		 * Update option if user clicks dismiss.
		 */
		function wsal_extension_core_dismiss_notice() {
			update_option( 'wsal_forms_notice_dismissed', true );
		}

		/**
		 * Used to hook into the `wsal_before_sensor_load` action to add some filters
		 * for including custom sensor and event directories.
		 *
		 * @method wsal_mu_plugin_add_custom_sensors_and_events_dirs
		 */
		function wsal_extension_core_mu_plugin_add_custom_sensors_and_events_dirs( $sensor ) {
			add_filter( 'wsal_custom_sensors_classes_dirs', array( $this, 'wsal_extension_core_mu_plugin_custom_sensors_path' ) );
			add_filter( 'wsal_custom_alerts_dirs', array( $this, 'wsal_extension_core_mu_plugin_add_custom_events_path' ) );
			return $sensor;
		}

		/**
		 * Adds a new path to the sensors directory array which is checked for when the
		 * plugin loads the sensors.
		 *
		 * @method wsal_mu_plugin_custom_sensors_path
		 * @since  1.0.0
		 * @param  array $paths An array containing paths on the filesystem.
		 * @return array
		 */
		function wsal_extension_core_mu_plugin_custom_sensors_path( $paths = array() ) {
			$paths   = ( is_array( $paths ) ) ? $paths : array();
			$paths[] = trailingslashit( trailingslashit( dirname( __FILE__ ) ) . 'wp-security-audit-log' . DIRECTORY_SEPARATOR . 'custom-sensors' );
			return $paths;
		}

		/**
		 * Adds a new path to the custom events directory array which is checked for
		 * when the plugin loads all of the events.
		 *
		 * @method wsal_mu_plugin_add_custom_events_path
		 * @since  1.0.0
		 * @param  array $paths An array containing paths on the filesystem.
		 * @return array
		 */
		function wsal_extension_core_mu_plugin_add_custom_events_path( $paths ) {
			$paths   = ( is_array( $paths ) ) ? $paths : array();
			$paths[] = trailingslashit( trailingslashit( dirname( __FILE__ ) ) . 'wp-security-audit-log' );
			return $paths;
		}

	}
}
