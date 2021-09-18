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
		$cf7formid2 = get_option( 'cf7_form_2_id' );

		$form = '';
		if($cf7formid && !isset($_SESSION['error'])){
			$form .= '<div id="donate_init">'; 
			$form .= '<img class="sp-logo" src="'.plugin_dir_url( __FILE__ ).'../public/img/sp-logo.jpg" width="100"/>'; 
			$form .= do_shortcode( '[contact-form-7 id='.$cf7formid.' title="Starpoint Donation form"]' ); 
			$form .= '</div>'; 
		}
		if($cf7formid2 && !isset($_SESSION['error'])){
			$form .= '<div id="donate_otp" style="display:none;">'; 
			$form .= do_shortcode( '[contact-form-7 id='.$cf7formid2.' title="Starpoint Donation form"]' ); 
			$form .= '<span id="reminder" style="display:none;">If you still not recieve OTP, kindly refresh your page.</div>'; 
			$form .= '</div>'; 
		}
		if(session_id() && isset($_SESSION['error']) ){
			$form .= '<span class="error">'.$_SESSION['error'].'</span>';
			unset($_SESSION['amount']);
			session_destroy();
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
			'Donation main form',
			array($this, 'admin_form_setting_markup'),
			'starpoint-donation-setting',
			'donate_setting_section'
		);
		add_settings_field(
			'cf7_form_2_id',
			'Donation OTP form',
			array($this, 'admin_form_setting_markup_1'),
			'starpoint-donation-setting',
			'donate_setting_section'
		);
		add_settings_field(
			'app_env',
			'App environment',
			array($this, 'admin_form_setting_markup_3'),
			'starpoint-donation-setting',
			'donate_setting_section'
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

		register_setting( 'starpoint-donation-setting', 'cf7_form_id' );
		register_setting( 'starpoint-donation-setting', 'cf7_form_2_id' );
		register_setting( 'starpoint-donation-setting', 'app_env' );
		register_setting( 'starpoint-donation-setting', 'app_key' );
		register_setting( 'starpoint-donation-setting', 'counter_name' );
		register_setting( 'starpoint-donation-setting', 'counter_password' );
		register_setting( 'starpoint-donation-setting', 'donation_name' );
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
	public function admin_form_setting_markup_1() {
		$cf7formid = get_option( 'cf7_form_2_id' );
		?>
		<select id="cf7_form_2_id" name="cf7_form_2_id">
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
		$app_key = get_option( 'app_key' );
		?>
		<input type="text" value="<?php echo $app_key; ?>" name="app_key"/>
		<?php
	}
	public function admin_form_setting_markup_3() {
		$app_env = get_option( 'app_env' );
		?>
		<select name="app_env" id="app_env">
			<option value="sandbox">Sandbox</option>
			<option value="live">Live</option>
		</select>
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

	public function session_init()
	{
		if (!session_id()){
			session_start();
		}
	}

	##https://wordpress.stackexchange.com/questions/379325/saving-contact-form-7-data-into-custom-table
	##https://stackoverflow.com/questions/35676608/what-contact-form-7-action-should-i-hook-into-for-seed-submitted-data-after-vali?rq=1
	public function cf7_form_submit($instance, $result)
	{
		$counter_name = get_option( 'counter_name' );
		$counter_password = get_option( 'counter_password' );
		$donation_name = get_option( 'donation_name' );
		$transfer = '';
		
		
		if(get_option( 'cf7_form_id' ) == $instance->id){
			$submission = WPCF7_Submission::get_instance();

			$data = $submission->get_posted_data();


			$_SESSION['authres'] = $this->request_auth('POST', "grant_type=client_credentials", []);;
			$_SESSION['redeemnumber'] = ltrim(str_replace(' ', '', $data['redeem-number']),"0");
			$_SESSION['amount'] = $data['amount'];
			$_SESSION['customer_name'] = $data['your-name'];

			if($_SESSION['authres']){
				$customer = array(
					"counterAlias" => $counter_name,
					"counterAuth" => $counter_password,
					"subscriberType" => "MOBILE",
					"subscriberValue" => $_SESSION['redeemnumber'],
					"amount" => $_SESSION['amount'],
					"accessMode" => "POS"
				);
				
				$sentOTP = $this->sendAuthToCustomer($customer, $_SESSION['authres']['access_token']);

				if(isset($sentOTP) && $sentOTP['pinSend']){
					return true;
				}else{
					$_SESSION['error'] = 'You donation have error, Please try again later';
				}
			}
			// return true;
		}

		if(get_option( 'cf7_form_2_id' ) == $instance->id){
			$submission = WPCF7_Submission::get_instance();

			$data = $submission->get_posted_data();

			if($_SESSION['authres']){

				$customer = array(
					"counterAlias" => $counter_name,
					"counterAuth" => $counter_password,
					"billNumber" => "",
					"noOfPoints" => $_SESSION['amount'],
					"billValue" => $_SESSION['amount'],
					"subscriberType" => "MOBILE",
					"subscriberValue" => $_SESSION['redeemnumber'],
					"subscriberAuth" => $data['otp-number'],
					"accessMode" => "POS"
				);

				$transfer = $this->burnWithAuth($customer, $_SESSION['authres']['access_token']);
				
				/***
				 * Array
				 * (
				 * 	[totalPoints] => 94
				 *  [transactionReferance] => 1867049229
				 *  [errorDesc] => 
				 *  [status] => 0
				 * )
				 */
				if($transfer){
					$this->storeCustomerDonation($customer, $transfer);
				}
				unset($_SESSION['authres']);
				unset($_SESSION['redeemnumber']);
				unset($_SESSION['amount']);
				unset($_SESSION['customer_name']);
				session_destroy();
				return true;
			}
		}
	}

	function sendAuthToCustomer($customer, $token)
	{
		$body = json_encode($customer);

		$response = $this->request_new( 'POST', 'https://extmife.dialog.lk/extapi/api_software_0000920171115', $body, [], $token );

		if ( $response ) {
			return $response;
		}

		return false;
	}
	function burnWithAuth($customer, $token)
	{
		$body = json_encode($customer);

		$response = $this->request_new( 'POST', 'https://extmife.dialog.lk/extapi/api_software_0000220171115', $body, [], $token );

		if ( $response ) {
			return $response;
		}

		return false;
	}

	function request_auth( $method, $body, $headers = [] ) {

		$app_key = get_option( 'app_key' );
		$counter_name = get_option( 'counter_name' );
		if ( empty( $app_key ) || empty( $counter_name ) ) {
			return false;
		}

		$requesturl = 'https://extmife.dialog.lk/extapi/api_admin_00001';

		$headers['Authorization'] = 'Basic '.$app_key;

		$params = array(
			'method'  => $method,
			'headers' => $headers,
		);

		if ( ! empty( $body ) && is_string( $body ) ) {
			$params['body'] = $body;
		} else if ( ! empty( $body ) && is_array( $body ) ) {
			$requesturl .= '?' . http_build_query( $body );
		}

		$request     = wp_remote_request( esc_url_raw( $requesturl ), $params );
		$status_code = wp_remote_retrieve_response_code( $request );
		
		if ( is_wp_error( $request ) ) {

			$messages = $request->get_error_messages();

			if ( is_array( $messages ) && ! empty( $messages ) ) {
				return $messages;
			}

		} else {
			if ( isset( $request['body'] ) && $status_code == 200 ) {
				return json_decode($request['body'], true);
			} else {
				return false;
			}
		}

		return false;
	}

	function request_new( $method, $endpoint, $body = '', $headers = [], $token) {

		$requesturl = $endpoint;

		$headers['Authorization'] = 'Bearer ' . $token;
		$headers['Content-Type']  = 'application/json';

		$params = array(
			'method'  => $method,
			'headers' => $headers,
		);

		$params['body'] = $body;

		$request     = wp_remote_request( esc_url_raw( $requesturl ), $params );
		$status_code = wp_remote_retrieve_response_code( $request );
		

		if ( is_wp_error( $request ) ) {

			$messages = $request->get_error_messages();

			if ( is_array( $messages ) && ! empty( $messages ) ) {
				return $messages;
			}

		} else {
			if ( isset( $request['body'] ) && $status_code == 200 ) {
				return json_decode($request['body'], true);
			} else {
				return false;
			}
		}

		return false;
	}

	function storeCustomerDonation($customer, $transfer){
		$customername = $_SESSION['customer_name'];
		$points = $customer['noOfPoints'];
		$mobilenumber = $customer['subscriberValue'];
		$transactionReferance = $transfer['transactionReferance'];


		$post_id = wp_insert_post(array (
			'post_type' => 'startpoints_donate',
			'post_title' => $customername.' donations',
			'post_status' => 'publish',
			'comment_status' => 'closed',   // if you prefer
			'ping_status' => 'closed',      // if you prefer
		));

		if ($post_id) {
			// insert post meta
			add_post_meta($post_id, 'Points donate', $points);
			add_post_meta($post_id, 'Mobile', $mobilenumber);
			add_post_meta($post_id, 'Transaction ID', $transactionReferance);
		}
	}

	// function startpoint_donations() {
 
	// 	register_post_type( 'startpoints_donate',
	// 		array(
	// 			'labels' => array(
	// 				'name' => __( 'Startpoints donations' ),
	// 				'singular_name' => __( 'Startpoints donate' )
	// 			),
	// 			'public' => true,
	// 			'has_archive' => true,
	// 			'rewrite' => array('slug' => 'startpoints_donate'),
	// 			'show_in_rest' => true,
	 
	// 		)
	// 	);
	// }

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
