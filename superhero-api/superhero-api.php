<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.linkedin.com/in/miguel-mariano-developer/
 * @since             1.0.0
 * @package           Superhero_Api
 *
 * @wordpress-plugin
 * Plugin Name:       SuperHero API
 * Plugin URI:        https://www.linkedin.com/in/miguel-mariano-developer/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Miguel Eduardo Mariano Lopez
 * Author URI:        https://www.linkedin.com/in/miguel-mariano-developer/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       superhero-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SUPERHERO_API_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-superhero-api-activator.php
 */
function activate_superhero_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-superhero-api-activator.php';
	Superhero_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-superhero-api-deactivator.php
 */
function deactivate_superhero_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-superhero-api-deactivator.php';
	Superhero_Api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_superhero_api' );
register_deactivation_hook( __FILE__, 'deactivate_superhero_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-superhero-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_superhero_api() {

	$plugin = new Superhero_Api();
	$plugin->run();

    require_once plugin_dir_path( __FILE__ ) . 'admin_functions.php';

    Admin_Functions::init();
}
run_superhero_api();