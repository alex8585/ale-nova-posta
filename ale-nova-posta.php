<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Ale_Nova_Posta
 *
 * @wordpress-plugin
 * Plugin Name:       Ale Nova Posta
 * Plugin URI:        http://example.com/
 * Description:       Nova Posta Api in checkout
 * Version:           1.0.0
 * Author:            Alex
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ale-nova-posta
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
define( 'ALE_NOVA_POSTA_VERSION', '1.0.0' );
define ('ALE_NOVA_POSTA_URL', plugin_dir_url( __FILE__ ));
define ('ALE_NOVA_POSTA_PATH', plugin_dir_path( __FILE__ ));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ale-nova-posta-activator.php
 */
function activate_ale_nova_posta() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ale-nova-posta-activator.php';
	Ale_Nova_Posta_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ale-nova-posta-deactivator.php
 */
function deactivate_ale_nova_posta() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ale-nova-posta-deactivator.php';
	Ale_Nova_Posta_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ale_nova_posta' );
register_deactivation_hook( __FILE__, 'deactivate_ale_nova_posta' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ale-nova-posta.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ale_nova_posta() {

	$plugin = new Ale_Nova_Posta();
	$plugin->run();

}
run_ale_nova_posta();
