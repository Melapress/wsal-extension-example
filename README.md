# WP Activity Log Custom Extension Example

Welcome to our example plugin template for creating your very own events for WP Activity Log. Using this as an example, you will soon be building your own custom extensions with ease.

## Whats included?

Right out of the box, this repository gives you a sample WordPress plugin which you can use as a template for creating your own extensions - we have also included some handy example functions for other ways to interact with WSAL such as adding posts to the "ignored post types" list etc.

Most of your changes/custom code will occur in the main plugin file, as well as within the "wp-security-audit-log" folder. Within this folder you will find the custom-events.php, as well as a custom sensor file, all ready to be editor as you wish.

## Where do I start?

 1. Download or clone this repository.
 2. Edit the main plugin file name to reflect your new plugin. This includes your text domain, plugin name, author details and so on.
 3. Search/replace the string 'wsal_extension_core_' - try to be unique to avoid "duplicated function" errors.
 4. Search/replace the string 'My_Custom_Sensor' - this will be your main sensor name, and again be unique.

## Registering events

Your array of custom events can be added via "wp-security-audit-log/custom-alerts.php" - for details on this array see the [documentation.](https://wpactivitylog.com/support/kb/create-custom-events-wordpress-activity-log/)

## Custom Sensor

First, be sure to give your sensor a unique name to avoid conflict. The default name "WSAL_Sensors_My_Custom_Sensor" should never be used.

The file is a simple placeholder class, ready to be filled with whatever you wish so have fun!.
