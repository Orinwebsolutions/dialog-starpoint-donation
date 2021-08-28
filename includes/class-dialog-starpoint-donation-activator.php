<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/Orinwebsolutions
 * @since      1.0.0
 *
 * @package    Dialog_Starpoint_Donation
 * @subpackage Dialog_Starpoint_Donation/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dialog_Starpoint_Donation
 * @subpackage Dialog_Starpoint_Donation/includes
 * @author     Amila Priyankara <amilapriyankara16@gmail.com>
 */
class Dialog_Starpoint_Donation_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')){
			die('Plugin NOT activated, because CF7 plugin is not activated in your site, Please activate CF7 plugin!!');
		}
		if(!class_exists("SOAPClient")){ //Plugin validate soapclient enable or not
			die('Plugin NOT activated, because SOAPClient is not activated your hosting !!');
		}
	}

}
