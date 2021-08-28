<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/Orinwebsolutions
 * @since      1.0.0
 *
 * @package    Dialog_Starpoint_Donation
 * @subpackage Dialog_Starpoint_Donation/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dialog_Starpoint_Donation
 * @subpackage Dialog_Starpoint_Donation/admin
 * @author     Amila Priyankara <amilapriyankara16@gmail.com>
 */
class Dialog_Starpoint_Donation_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dialog_Starpoint_Donation_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dialog_Starpoint_Donation_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dialog-starpoint-donation-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dialog_Starpoint_Donation_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dialog_Starpoint_Donation_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dialog-starpoint-donation-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function register_shortcodes(){
		add_shortcode('starpoint_form',array($this, 'starpoint_form'));
	}
	public function starpoint_form()
	{
		$cf7formid = get_option( 'cf7_form_id' );
		// Things that you want to do. 
		$form = 'form';
		if($cf7formid){
			$form .= do_shortcode( '[contact-form-7 id='.$cf7formid.' title="Starpoint Donation form"]' ); 
		}
		
		// Output needs to be return
		return $form;
	}

	public function admin_menu() {
		add_menu_page(
			'Starpoint donation',
			'Starpoint donation',
			'manage_options',
			$this->plugin_name,
			array($this, 'form_settings'),
			'dashicons-hammer',100);
	}

	public function form_settings() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/dialog-starpoint-donation-admin-display.php' );
	}

	function settings_init() {
	
		add_settings_section(
			'donate_setting_section',
			'',
			'',
			'starpoint-donation-setting'
		);
	
		add_settings_field(
			'cf7_form_id',
			'Donation settings',
			array($this, 'admin_form_setting_markup'),
			'starpoint-donation-setting',
			'donate_setting_section'
		);
		add_settings_field(
			'donation_app',
			'App setting',
			array($this, 'admin_form_setting_markup_2'),
			'starpoint-donation-setting',
			'donate_setting_section'
		);

		register_setting( 'starpoint-donation-setting', 'cf7_form_id' );
		register_setting( 'starpoint-donation-setting', 'donation_app' );
	}

	public function admin_form_setting_markup() {
		$cf7formid = get_option( 'cf7_form_id' );
		?>
		<select id="cf7_form_id" name="cf7_form_id">
			<option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option> 
			<?php 
			$pages = get_posts(array(
				'post_type'     => 'wpcf7_contact_form',
				'numberposts'   => -1
			));
			foreach ( $pages as $page ) {
				$option = '<option value="' . $page->ID . '" '.selected( $cf7formid, $page->ID ).'>';
				$option .= $page->post_title;
				$option .= '</option>';
				echo $option;
			}
			?>
		</select>
		<?php
	}
	public function admin_form_setting_markup_2() {
		// $donation_app = get_option( 'donation_app' );
		?>
		<!-- <select id="donation_app" name="donation_app">
			<option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option>  -->
			<?php 
			// $pages = get_posts(array(
			// 	'post_type'     => 'wpcf7_contact_form',
			// 	'numberposts'   => -1
			// ));
			// foreach ( $pages as $page ) {
			// 	$option = '<option value="' . $page->ID . '" '.selected( $donation_app, $page->ID ).'>';
			// 	$option .= $page->post_title;
			// 	$option .= '</option>';
			// 	echo $option;
			// }
			?>
		<!-- </select> -->
		<?php
	}
}
