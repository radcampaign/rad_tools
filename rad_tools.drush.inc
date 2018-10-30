<?php
/**
 * @file Some helpful drush commands to help with development
 */

/**
 * Adds our commands
 * @return array
 */
function rad_tools_drush_command() {
  $items = [
    'mod-reinstall' => [
      'description' => 'Reinstalls a module - helpful for reloading our module configuration',
      'aliases' => ['mri'],
      'arguments' => [
        'name' => 'The module name.',
      ],
    ],
    'mod-config-clear' => [
      'description' => 'Removes module config',
      'aliases' => ['mcc'],
      'arguments' => [
        'name' => 'The module name.',
      ]
    ],
    'mod-config-install' => [
      'description' => 'Installs module config',
      'aliases' => ['mci'],
      'arguments' => [
        'name' => 'The module name.',
      ]
    ],
    'mod-config-rebuild' => [
      'description' => 'Reinstalls the config for a specific module',
      'aliases' => ['mcr'],
      'arguments' => [
        'name' => 'The module name.',
      ]
    ],
  ];

  return $items;
}

/**
 * checks if a module exists
 * @param  string $name  The name of the module to check in pm-list
 * @return boolean
 */
function rad_tools_find_module($name = '') {
  if (empty($name)) {
    return false;
  }

  $modules = drush_invoke_process('@site', 'pm-list', [], ['fields' => 'name', 'type' => 'module'], false);
  return in_array($name, array_keys($modules['object'] ?? []));
}

/**
 * Handles our mri drush command
 * @param  string $name  the name of the module to reinstall. If no name is passed, it will check the current directory name to see if it is a module
 * @return void
 */
function drush_rad_tools_mod_reinstall($name = '') {
  // if the name is empty, there's nothing we can do
  if (empty($name)) {
    drush_print('Module name required!');
    return false;
  }

  // make sure the module exists
  if (rad_tools_find_module($name)) {
    drush_invoke_process('@site', 'pmu', [$name]);
    drush_invoke_process('@site', 'cr');
    drush_invoke_process('@site', 'en', [$name]);
  }
  else {
    drush_print(sprintf('The module %s was not found. exiting ...', $name));
  }
}

/**
 * Runs the uninstallation of module config
 * @param  string $name  the name of the module
 * @return void
 */
function drush_rad_tools_mod_config_clear($name = '') {
  if (empty($name)) {
    drush_print('Module name required!');
    return false;
  }

  drush_print(dt('Uninstalling config for module: ' . $name));
  \Drupal::service('config.manager')
        ->uninstall('module', $name);
}

/**
 * Runns the installation of module config
 * @param  string $name  the name of the module
 * @return void
 */
function drush_rad_tools_mod_config_install($name = '') {
  // first we need to remove all configuration associated with the module
  drush_print(dt('Installing config for module: ' . $name));
  // next we'll go and reinstall the configuration associated with the module
  $config_installer = \Drupal::service('config.installer');

  // Check the validity of the default configuration. This will throw
  // exceptions if the configuration is not valid.
  $config_installer->checkConfigurationToInstall('module', $name);

  // reinstalls the configuration
  $config_installer->installDefaultConfig('module',$name);
}

/**
 * Reinstalls the configuration for a module
 * @see  https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Extension%21ModuleInstaller.php/class/ModuleInstaller/8.2.x
 * @param  string $name the name of the module
 * @return void
 */
function drush_rad_tools_mod_config_rebuild($name = '') {
  // if the name is empty, there's nothing we can do
  if (empty($name)) {
    drush_print('Module name required!');
    return false;
  }
  // runs clear and install
  drush_rad_tools_mod_config_clear($name);
  drush_rad_tools_mod_config_install($name);
}
