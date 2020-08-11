To keep things easier to manage and have a central repo with the core code for all our extensions, we use a master repository which contains the “core” files (these are parts of extensions which don't change) such as the PluginInstaller class, filters for adding custom sensors and so on.

We use this setup so any updates to the “core” can be simply fetched into all the extensions and merge any changes with ease.

# How does it work?

To clarify what this means for the file structure of an extension, let’s take a look at the repo's structure. We have the “wsal-extension-core.php” file, which is where core functions that are shared across all extensions is kept. The main plugin file “wsal-extension” which includes the core file from within the main file and your all set.

# Creating a new extension

Create a new repository for your new extension. The naming format for new repos is “wsal-pluginname”. So for example if we are making an extension for wpforms, the repo should be called“wsal-wpforms”
Clone the master repository into your new repository by using the below command:

```bash
git clone https://github.com/WPWhiteSecurity/wsal-extension-template.git
```

Add the master repository as upstream to new repository with the below command:
```bash
git remote add upstream https://github.com/WPWhiteSecurity/wsal-extension-template.git
```
From this point onwards, you can now go ahead and develop your new extension as normal. However, should you wish to pull any changes made on the master repository into your extension, run the following from your “child” branch.
```bash
git pull upstream master
```

# The main extension plugin file
When working on a new extension, at the top of your main plugin file register the “core” files (which is now a class) using the following code:

```bash
require_once plugin_dir_path( __FILE__ ) . 'wsal-extension-core.php';
$wsal_extension = WSAL_Extension::get_instance();
$wsal_extension->init();
```
