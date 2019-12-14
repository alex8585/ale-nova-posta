<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Ale_Nova_Posta
 * @subpackage Ale_Nova_Posta/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ale_Nova_Posta
 * @subpackage Ale_Nova_Posta/includes
 * @author     Your Name <email@example.com>
 */
class Ale_Nova_Posta_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public  static function activate() {
        $sqlCities = "CREATE TABLE IF NOT EXISTS " . ALE_NOVA_POSTA_CITIES_TABLE . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			CityID mediumint(9) NOT NULL,
			Description varchar(255) NOT NULL,
			DescriptionRu varchar(255) NOT NULL,
			Ref varchar(255) NOT NULL,
			SettlementTypeDescription varchar(255) NOT NULL,
			SettlementTypeDescriptionRu varchar(255) NOT NULL,
			UNIQUE KEY id (id)
			
		)ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sqlCities);

	}

	
}
