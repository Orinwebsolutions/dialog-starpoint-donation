<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/Orinwebsolutions
 * @since      1.0.0
 *
 * @package    Dialog_Starpoint_Donation
 * @subpackage Dialog_Starpoint_Donation/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Dialog_Starpoint_Donation
 * @subpackage Dialog_Starpoint_Donation/public
 * @author     Amila Priyankara <amilapriyankara16@gmail.com>
 */
class Dialog_Starpoint_Donation_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dialog-starpoint-donation-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dialog-starpoint-donation-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'starAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

	}

	public function startpoint_ajax()
	{

		$counter_name = get_option( 'counter_name' );
		$counter_password = get_option( 'counter_password' );
		$donation_name = get_option( 'donation_name' );

		$data = array();
		$return = array();
		$mobile_number = '';
		parse_str($_POST['data'], $data);

		if(strlen($data['redeem-number']) > 9){
			$mobile_number = substr($data['redeem-number'],-9);
		}else if(strlen($data['redeem-number']) < 9){
			wp_send_json(['msg' => 'Your Star Points redeem number is incorrect, Please correct it and try again','type' => 'error',]);
		}else{
			$mobile_number = $data['redeem-number'];
		}
		
		if($data['step'] == 1){

			$authCode = $this->request_auth('POST', "grant_type=client_credentials", []);

			$customer = array(
				"counterAlias" => $counter_name,
				"counterAuth" => $counter_password,
				"subscriberType" => "MOBILE",
				"subscriberValue" => $mobile_number
			);
			
			$balance = $this->retrieveBalance($customer, $authCode['access_token']);
			 
			if($balance['status'] != 0){
			// if($balance['status'] == -99){
				$return = array(
					'msg' => $balance['errorDesc'],
					'type' => 'error',
				);
			}else{
				$return = array(
					'auth'  => $authCode['access_token'],
					'currentBalance' => $balance['currentBalance'],
					'redeemableBalance' => $balance['redeemableBalance'],
					'type' => 'auth&bal',
					'ResponseObj' => $balance
				);
			}
		}

		if($data['accessToken'] && $data['step'] == 2){



			if($data['accessToken']){
				$customer = array(
					"counterAlias" => $counter_name,
					"counterAuth" => $counter_password,
					"subscriberType" => "MOBILE",
					"subscriberValue" => $mobile_number,
					"amount" => $data['amount'],
					"accessMode" => "POS"
				);
			}	
			
			$sentOTP = $this->sendAuthToCustomer($customer, $data['accessToken']);

			if(isset($sentOTP) && $sentOTP['pinSend']){
				$return = array(
					'pinSend' => $sentOTP['pinSend'],
					'type' => 'pinSend',
				);
			}else{
				$return = array(
					'msg' => $sentOTP['errorMessage'],
					'type' => 'error',
					'ResponseObj' => $sentOTP
				);
			}
		}

		if($data['accessToken'] && $data['step'] == 3){

			$customer = array(
				"counterAlias" => $counter_name,
				"counterAuth" => $counter_password,
				"billNumber" => "'".rand()."'",
				"noOfPoints" => $data['amount'],
				"billValue" => $data['amount'],
				"subscriberType" => "MOBILE",
				"subscriberValue" => $mobile_number,
				"subscriberAuth" => $data['otp-number'],
				"accessMode" => "POS"
			);

			$transfer = $this->burnWithAuth($customer, $data['accessToken']);

			//ToDo prepare return value
			if(isset($transfer) && $transfer['transactionReferance']){
				$return = array(
					'transactionReferance' => $transfer['transactionReferance'],
					'type' => 'burnstarpoint',
				);
				$this->storeCustomerDonation($customer, $data, $transfer);
			}else{
				$return = array(
					'msg' => $transfer['errorDesc'],
					'type' => 'error',
					'ResponseObj' => $transfer
				);
			}
		}
		wp_send_json($return);

	}

	function retrieveBalance($customer, $token)
	{
		$body = json_encode($customer);

		$response = $this->request_new( 'POST', 'https://extmife.dialog.lk/extapi/api_software_0000420171115', $body, [], $token );

		if ( $response ) {
			return $response;
		}

		return false;
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
				return json_decode($request['body'], true);
			}
		}

		return false;
	}

	function storeCustomerDonation($customer, $submittedData, $transfer){
		$customername = $submittedData['your-name'];
		$email = $submittedData['your-email'];
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
			add_post_meta($post_id, 'email', $email);
			add_post_meta($post_id, 'Transaction ID', $transactionReferance);
		}
	}

}
