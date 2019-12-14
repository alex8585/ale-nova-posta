<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ale_Nova_Posta
 * @subpackage Ale_Nova_Posta/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Ale_Nova_Posta
 * @subpackage Ale_Nova_Posta/includes
 * @author     Your Name <email@example.com>
 */
class Ale_Nova_Posta_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'update_cities_from_np_hook' );
	}

}
