# WP Activity Log Extension Core

To keep things easier to manage and have a central repo with the core code for all our extensions, we use a master repository which contains the “core” files (these are parts of extensions which don't change) such as the PluginInstaller class, filters for adding custom sensors and so on.

We use this setup so any updates to the “core” can be simply fetched into all the extensions and merge any changes with ease.

## [](https://github.com/WPWhiteSecurity/wsal-extension-template#how-does-it-work)How does it work?

With this repository, we hold only a small amount of code which is typically used in all extensions. The core handles loading the text-domain, notices, plugin installer and functions needed to load custom sensors and events and thats it.

By simply copying the content of this repository into your new extension as a base, you can then create your plugins main file (see example below). The main thing to remember is to include the core class from within your new extension - using the function provided below, you can simply call the "core", passing your plugins text-domain on for loading if you wish.

## [](https://github.com/WPWhiteSecurity/wsal-extension-template#creating-a-new-extension)Creating a new extension

Create a new repository for your new extension. The naming format for new repos is “wsal-pluginname”. So for example if we are making an extension for wpforms, the repo should be called “wsal-wpforms”. Clone the master repository or download it and extract its content into your new repository. To clone you can use the following command

    git clone https://github.com/WPWhiteSecurity/wsal-extension-template.git

Add the master repository as upstream to new repository with the below command:

    git remote add upstream https://github.com/WPWhiteSecurity/wsal-extension-template.git

From this point onwards, you can now go ahead and develop your new extension as normal. However, should you wish to pull any changes made on the master repository into your extension, run the following from your “child” branch.

    git pull upstream master --allow-unrelated-histories
Note - be sure to ignore any changes to the readme.md file if you have made any customizations to this when merging into your custom extension's repository.

## [](https://github.com/WPWhiteSecurity/wsal-extension-template#the-main-extension-plugin-file)The main extension plugin file

When working on a new extension, you must ensure you include the core files in your main plugin file - see below for an example. Be sure to update the information and text-domain etc accordingly.

    <?php
    /**
     * Plugin Name: WP Activity Log Extension for (Plugin name)
     * Plugin URI: https://wpactivitylog.com/extensions/
     * Description: A WP Activity Log plugin extension
     * Text Domain: wp-security-audit-log
     * Author: WP White Security
     * Author URI: http://www.wpwhitesecurity.com/
     * Version: 1.0.0
     * License: GPL2
     * Network: true
     *
     * @package WsalExtensionCore
     * @subpackage Wsal Custom Events Loader
     */

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
     REQUIRED. Here we include and fire up the main core class. This is crucial so leave intact.
    */
    require_once plugin_dir_path( __FILE__ ) . 'core/class-extension-core.php';
    $wsal_extension = new WPWhiteSecurity\ActivityLog\Extensions\Common\Core( __FILE__, 'wsal-yoast' );

### Allowing sensors to load on front-end.

1.  First we need to add a new checkbox to the Enable/Disable Events screen (ToggleAlerts) - to do this use the wsal_togglealerts_append_content_to_toggle filter to append your custom checkbox to whatever event ID you wish.

````
/**
 * Append some extra content below an event in the ToggleAlerts view.
 */
function append_content_to_toggle( $alert_id ) {

  if ( 9999 === $alert_id ) {
    $frontend_events     = WSAL_Settings::get_frontend_events();
    $enable_for_visitors = ( isset( $frontend_events['new_event'] ) && $frontend_events['new_event'] ) ? true : false;
    ?>
    <tr>
      <td></td>
      <td>
        <input name="frontend-events[new_event]" type="checkbox" id="frontend-events[new_event]" value="1" <?php checked( $enable_for_visitors ); ?> />
      </td>
      <td colspan="2"><?php esc_html_e( 'Keep a log of this event on the front end?', 'wsal-gravity-forms' ); ?></td>
    </tr>
    <?php
  }
}
````


2.  Add filter wsal_load_on_frontend to check the front-end on/off settings array to see if the plugin needs to load.
````
function wsal_gravityforms_allow_sensor_on_frontend( $default, $frontend_events ) {
  $should_load = ( $default || ! empty( $frontend_events['gravityforms'] ) ) ? true : false;;
  return $should_load;
}
````

3.  Use 'wsal_load_public_sensors' to add our sensor to the array of public sensors

````
function wsal_gravityforms_extension_load_public_sensors( $value ) {
  $value[] = 'Gravity_Forms';
  return $value;
}
````
