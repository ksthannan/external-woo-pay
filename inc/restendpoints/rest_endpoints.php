<?php
class External_Woo_Pay_Rest_Endpoints extends External_Woo_Pay{

	/**
	 * Construct initiates
	 */
	public function __construct() {

		parent::__construct();
		
		// if($this -> is_product_site){
		// 	add_action( 'rest_api_init', array($this, 'register_product_rest_endpoints'));
		// }

		if($this -> is_payment_site){
			add_action( 'rest_api_init', array($this, 'register_payment_rest_endpoints'));
		}
		
	}

	/**
	 * Register rest endpoints for product site
	 */
	// public function register_product_rest_endpoints(){
	// 	register_rest_route( 'external-woo-pay/v1', '/retrieve_product_data/', array(
	// 		'methods' => 'POST',
	// 		'callback' => array($this, 'exwoopay_retrieve_product_data'),
	// 		'permission_callback' => '__return_true',
	// 		));
	// }

	// public function exwoopay_retrieve_product_data(){
	// 	$data = getallheaders();
	// 	$request_body = file_get_contents("php://input");
	// 	$data = json_decode($request_body, true);
	// 	$rest_data = $this->get_option($data['session_token']);
	// 	return rest_ensure_response($rest_data);
	// }


	/**
	 * Register rest endpoints for exwoopay_payment_url
	 */
	public function register_payment_rest_endpoints(){
		register_rest_route( 'external-woo-pay/v1', '/create_payment_url/', array(
			'methods' => 'POST',
			'callback' => array($this, 'exwoopay_create_payment_url'),
			));

		register_rest_route( 'external-woo-pay/v1', '/order_payment_status/', array(
			'methods' => 'POST',
			'callback' => array($this, 'exwoopay_order_payment_status'),
			));
		register_rest_route( 'external-woo-pay/v1', '/clear_exwoopay_data/', array(
			'methods' => 'POST',
			'callback' => array($this, 'exwoopay_clear_exwoopay_data'),
			));

	}

	/**
	 * Calback of create_payment_url
	 */
	public function exwoopay_clear_exwoopay_data(){
		if($headers['token'] == $this->self_token){
			$headers = getallheaders();
			$request_body = file_get_contents("php://input");
			$data = json_decode($request_body, true);
			$External_Woo_Pay = new External_Woo_Pay();
			$External_Woo_Pay->delete_option($data['exwoopay_session_key']);
			return rest_ensure_response(array(
				'delete_info' => 'success'
			));
		}
	}
	/**
	 * Calback of create_payment_url
	 */
	public function exwoopay_create_payment_url(){
		$headers = getallheaders();
		$request_body = file_get_contents("php://input");
		$data = json_decode($request_body, true);
		$External_Woo_Pay = new External_Woo_Pay();

		$url = $External_Woo_Pay->product_site_link;
		$parsed_url = parse_url($url);
		$referrer = isset($parsed_url['host']) ? $parsed_url['host'] : '';


		if($headers['token'] == $this->self_token){
			$custom_price = intval($data['order_total']);
			$order = wc_create_order();
			$order->set_total($custom_price);
			$order_id = $order->get_id();
			$payment_url = $order->get_checkout_payment_url() . '&external_payment=true';
	
			$billing_info = $data['customer'];
			
			$order->set_billing_first_name($billing_info['first_name']);
			$order->set_billing_last_name($billing_info['last_name'] . ' (#'.$data['product_order_id'].' ' . $referrer . ')');
			$order->set_billing_company($billing_info['company']);
			$order->set_billing_address_1($billing_info['address_1']);
			$order->set_billing_address_2($billing_info['address_2']);
			$order->set_billing_city($billing_info['city']);
			$order->set_billing_state($billing_info['state']);
			$order->set_billing_postcode($billing_info['postcode']);
			$order->set_billing_country($billing_info['country']);
			$order->set_billing_email($billing_info['email']);
	
			$order->save();


			// Disable all WooCommerce email notifications for orders
			add_filter('woocommerce_email_enabled_new_order', '__return_false');
			add_filter('woocommerce_email_enabled_cancelled_order', '__return_false');
			add_filter('woocommerce_email_enabled_failed_order', '__return_false');
			add_filter('woocommerce_email_enabled_customer_on_hold_order', '__return_false');
			add_filter('woocommerce_email_enabled_customer_processing_order', '__return_false');
			add_filter('woocommerce_email_enabled_customer_completed_order', '__return_false');
			add_filter('woocommerce_email_enabled_customer_invoice', '__return_false');
			add_filter('woocommerce_email_enabled_customer_note', '__return_false');
			add_filter('woocommerce_email_enabled_customer_reset_password', '__return_false');
			add_filter('woocommerce_email_enabled_customer_new_account', '__return_false');

			// Optionally, you can also disable the admin email notifications
			add_filter('woocommerce_email_enabled_new_order_admin', '__return_false');
			add_filter('woocommerce_email_enabled_customer_processing_order_admin', '__return_false');
			add_filter('woocommerce_email_enabled_customer_completed_order_admin', '__return_false');

			// Optionally, you can disable the failed order retry notifications
			add_filter('woocommerce_email_enabled_failed_order_retry', '__return_false');

	
			$response = array(
				'session_token' => $data['session_token'],
				'payment_link' => $payment_url,
				'product_site_link_back' => $data['product_site_link_back'],
				'product_order_id' => $data['product_order_id'],
				'order_id' => $order_id
			);
	
			// $_SESSION['exwoopay_data'] = $response;

			$External_Woo_Pay->update_option( 'exwoopay_' . $data['session_token'], $response);
	
			return rest_ensure_response($response);
		}else{
			$response = array(
				'action' => 'failed',
				'error' => 'Token Error'
			);
			return rest_ensure_response($response);
		}

	}

	/**
	 * Callback for exwoopay_order_payment_status
	 */
	public function exwoopay_order_payment_status(){
		$headers = getallheaders();
		$request_body = file_get_contents("php://input");
		$data = json_decode($request_body, true);

		if($headers['token'] == $this->self_token){

			$External_Woo_Pay = new External_Woo_Pay();
			$order_saved_info = $External_Woo_Pay->get_option('exwoopay_' . $data['exwoopay_session_key']);

			$order_id = $data['order_id'];
			$order = wc_get_order( intval($order_id) );
			$payment_status = $order->get_status();

			if($order_saved_info['order_id'] == $data['order_id']){
				if($order){

					$response = array(
						'payment_status' => $payment_status,
						'order_saved_info' => $order_saved_info
					);
					return rest_ensure_response($response);
	
				}else{
					$response = array(
						'action' => 'failed',
						'error' => 'Order Error'
					);
					return rest_ensure_response($response);
				}
			}else{
				$response = array(
					'action' => 'failed',
					'error' => 'Order Validation Error'
				);
				return rest_ensure_response($response);
			}
			

		}else{
			$response = array(
				'action' => 'failed',
				'error' => 'Token Error'
			);
			return rest_ensure_response($response);
		}

	}

}

new External_Woo_Pay_Rest_Endpoints();