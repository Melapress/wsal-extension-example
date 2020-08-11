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
