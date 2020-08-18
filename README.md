To keep things easier to manage and have a central repo with the core code for all our extensions, we use a master repository which contains the “core” files (these are parts of extensions which don't change) such as the PluginInstaller class, filters for adding custom sensors and so on.

We use this setup so any updates to the “core” can be simply fetched into all the extensions and merge any changes with ease.

# How does it work?

To clarify what this means for the file structure of an extension, let’s take a look at the repo's structure. We have the “wsal-extension-core.php” file, which is where core functions that are shared across all extensions is kept. The main plugin file “wsal-extension” which includes the core file from within the main file and your all set.

# Creating a new extension

Create a new repository for your new extension. The naming format for new repos is “wsal-pluginname”. So for example if we are making an extension for wpforms, the repo should be called “wsal-wpforms”
Clone the master repository or download it and extract its content into your new repository. To clone you can use the following command

```bash
git clone https://github.com/WPWhiteSecurity/wsal-extension-template.git
```

Add the master repository as upstream to new repository with the below command:
```bash
git remote add upstream https://github.com/WPWhiteSecurity/wsal-extension-template.git
```

From this point onwards, you can now go ahead and develop your new extension as normal. However, should you wish to pull any changes made on the master repository into your extension, run the following from your “child” branch.
```bash
git pull upstream master --allow-unrelated-histories
```

# The main extension plugin file
When working on a new extension, you must ensure you include the core files in your main plugin file - see below for an example.

```php
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
 * @package Wsal
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
	REQUIRED. Here we include and fire up the main core class. This will be needed regardless so be sure to leave line 37-39 in tact.
*/
require_once plugin_dir_path( __FILE__ ) . 'core/class-extension-core.php';
$wsal_extension = \WPWhiteSecurity\ActivityLog\Extensions\Core\WSAL_Extension_Core::get_instance();
$wsal_extension->init();
```
