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

		$form = '';
		$form .= '<form id="stardonation_form">
					<img class="sp-logo" src="'.plugin_dir_url( __FILE__ ).'../public/img/sp-logo.jpg" width="100"/>
					<span id="messages"></span>
					<div class="loader_parent"><div class="loader"></div></div>
					<div id="balance_inquire_form">
						
						<p class="intro">Donate your Star Points</p>
						<div class="donate_row_flex">
							<div class="donate_column">
								<label> Your Name :<br>
									<span class="your-name"><input type="text" name="your-name" value="" size="40" class=""></span>
								</label>
							</div>
							<div class="donate_column">
								<label> Your Email : <br>
									<span class="your-email"><input type="email" name="your-email" value="" size="40" class=""></span> 
								</label>
							</div>
							<div class="donate_column">
								<label> Your Star Points redeem number* :<br>
									<span class="redeem-number"><input type="text" name="redeem-number" value="" size="40" class=""></span>
								</label>
							</div>
						</div>
					</div>
					<div id="balance_retrieve_form">
						<p class="intro">How much do you want to Donate?</p>
						<div class="donate_row_flex">
							<div class="donate_column">
								<label>Your redeemable Star Points balance:<br>
									<span class="balance_amount"><input type="text" name="balance_amount" value="" size="40" class="" disabled></span>
								</label>
							</div>
							<div class="donate_column">
								<label>Star Points redeem amount:*<br>
									<span class="amount"><input type="text" name="amount" value="" size="40" class=""></span>
								</label>
							</div>
						</div>
					</div>
					<div id="otp_confirmation_form">
						<p class="intro">Add your OTP number and approve it.</p>
						<div class="donate_row_flex">
							<div class="donate_column">
								<label>OTP number*<br>
									<span class="otp-number"><input type="text" name="otp-number" value="" size="40" class=""></span> 
								</label>
							</div>
						</div>
					</div>	
					<input type="hidden" name="accessToken"/>
					<input type="hidden" name="step" value="1"/>
					<div class="btn-container"><input type="button" value="Back" id="stardonateback" class="btn-grad button donate-btn"><input type="button" value="Donate" id="stardonate" class="btn-grad button donate-btn"></div>
				</form>';
		
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
			'app_key',
			'App key',
			array($this, 'admin_form_setting_markup_2'),
			'starpoint-donation-setting',
			'donate_setting_section'
		);
		add_settings_field(
			'counter_name',
			'App Counter name',
			array($this, 'admin_form_setting_markup_4'),
			'starpoint-donation-setting',
			'donate_setting_section'
		);
		add_settings_field(
			'counter_password',
			'App Counter password',
			array($this, 'admin_form_setting_markup_5'),
			'starpoint-donation-setting',
			'donate_setting_section'
		);
		add_settings_field(
			'donation_name',
			'App Donation name',
			array($this, 'admin_form_setting_markup_6'),
			'starpoint-donation-setting',
			'donate_setting_section'
		);

		register_setting( 'starpoint-donation-setting', 'app_key' );
		register_setting( 'starpoint-donation-setting', 'counter_name' );
		register_setting( 'starpoint-donation-setting', 'counter_password' );
		register_setting( 'starpoint-donation-setting', 'donation_name' );
	}

	public function admin_form_setting_markup_2() {
		$app_key = get_option( 'app_key' );
		?>
		<input type="text" value="<?php echo $app_key; ?>" name="app_key"/>
		<?php
	}

	public function admin_form_setting_markup_4() {
		$counter_name = get_option( 'counter_name' );
		?>
		<input type="text" value="<?php echo $counter_name; ?>" name="counter_name"/>
		<?php
	}
	public function admin_form_setting_markup_5() {
		$counter_password = get_option( 'counter_password' );
		?>
		<input type="text" value="<?php echo $counter_password; ?>" name="counter_password"/>
		<?php
	}
	public function admin_form_setting_markup_6() {
		$donation_name = get_option( 'donation_name' );
		?>
		<input type="text" value="<?php echo $donation_name; ?>" name="donation_name"/>
		<?php
	}

	function startpoint_cpt() {
 
		// Set UI labels for Custom Post Type
			$labels = array(
				'name'                => _x( 'Donations', 'Post Type General Name', 'twentytwenty' ),
				'singular_name'       => _x( 'Donate', 'Post Type Singular Name', 'twentytwenty' ),
				'menu_name'           => __( 'Donations', 'twentytwenty' ),
				'parent_item_colon'   => __( 'Parent Donate', 'twentytwenty' ),
				'all_items'           => __( 'All Donations', 'twentytwenty' ),
				'view_item'           => __( 'View Donate', 'twentytwenty' ),
				'add_new_item'        => __( 'Add New Donate', 'twentytwenty' ),
				'add_new'             => __( 'Add New', 'twentytwenty' ),
				'edit_item'           => __( 'Edit Donate', 'twentytwenty' ),
				'update_item'         => __( 'Update Donate', 'twentytwenty' ),
				'search_items'        => __( 'Search Donate', 'twentytwenty' ),
				'not_found'           => __( 'Not Found', 'twentytwenty' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwenty' ),
			);
			 
		// Set other options for Custom Post Type
			 
			$args = array(
				'label'               => __( 'Startpoints donations', 'twentytwenty' ),
				'description'         => __( 'Startpoints donations', 'twentytwenty' ),
				'labels'              => $labels,
				// Features this CPT supports in Post Editor
				// 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
				'supports'            => array( 'title', 'custom-fields' ),
				// You can associate this CPT with a taxonomy or custom taxonomy. 
				// 'taxonomies'          => array( 'genres' ),
				/* A hierarchical CPT is like Pages and can have
				* Parent and child items. A non-hierarchical CPT
				* is like Posts.
				*/ 
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'show_in_rest' => true,
		 
			);
			 
			// Registering your Custom Post Type
			register_post_type( 'startpoints_donate', $args );
		 
		}
}
