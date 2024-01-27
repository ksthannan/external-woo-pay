<?php
/*
Plugin Name: External Woo Pay
Description: Pay by site B for the cart items of site A
Version:     1.0.0
Author:      WPDevGo
Author URI:  https://wpdevgo.com/
Text Domain: exwoopay
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die;

/**
 * Define required constants
 */
define( 'EXTERNAL_WOO_PAY_VER', '1.0.0' );
define( 'EXTERNAL_WOO_PAY_FILE', __FILE__ );
define( 'EXTERNAL_WOO_PAY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EXTERNAL_WOO_PAY_OPT_GROUP', 'ewp_admin_settings_group' );
define( 'EXTERNAL_WOO_PAY_OPT_NAME', 'ewp_admin_settings' );


if ( ! class_exists( 'External_Woo_Pay' ) ) {

	require( __DIR__ . '/inc/functions.php' );

	class External_Woo_Pay {

		use External_Woo_Pay_Functions;

		public $is_payment_site;
		public $is_product_site;
		public $product_site_link;
		public $payment_site_link;
	    public $self_token;
	    public $input_token;
	    public $site_a_link;
		public $options;

		public static function get_instance() {
			if ( self::$instance == null ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		private static $instance = null;

		/**
		 * Construct initiates
		 */
		public function __construct() {

			$this->options = null;

			$this->is_product_site = $this->get_option('product_site');
			$this->is_payment_site = $this->get_option('payment_site');
			$this->self_token = $this->update_token();
			$this->input_token = $this->get_option('input_token');
			$this->product_site_link = $this->get_option('product_site_link');
			$this->payment_site_link = $this->get_option('payment_site_link');

			// Actions
			add_action( 'init', array( $this, 'initialize_features' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_wp_assets' ) );
	
			// Actions
			add_action( 'init', array($this, 'start_custom_session') );
			add_action('plugins_loaded', array($this, 'plugins_loaded_hooked'));
			add_action('init', array($this, 'do_custom_functions_init'));
			
	
			if($this -> is_payment_site){
				add_action( 'woocommerce_get_checkout_order_received_url', array($this, 'thank_you_redirect_back') );
			}
			if($this -> is_product_site){
				add_filter( 'woocommerce_payment_gateways', array($this, 'add_on_processing_gateway') );
				add_filter('woocommerce_order_button_text', array($this, 'exwoopay_change_place_order_button_text'));
				add_action('body_class', array($this, 'add_custom_body_class_for_product_site'));
				add_action('wp_footer', array($this, 'exwoopay_modal_iframe'), 99);
			}
		    
		}

		/**
		 * Initialize features
		 */
		public function initialize_features() {
			load_plugin_textdomain( 'exwoopay', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Enqueue admin assets
		 */
		public function enqueue_admin_assets( ) {
			wp_enqueue_style( 'exwoopay-admin-style', plugins_url( 'assets/css/admin-style.css', __FILE__ ), array(), EXTERNAL_WOO_PAY_VER, 'all' );
			wp_enqueue_script( 'exwoopay-admin-script', plugins_url( 'assets/js/admin-script.js', __FILE__ ), array( 'jquery' ), EXTERNAL_WOO_PAY_VER, true );
		}

		/**
		 * Enqueue wp assets
		 */
		public function enqueue_wp_assets( ) {
			wp_enqueue_style( 'exwoopay-style', plugins_url( 'assets/css/exwoopay-style.css', __FILE__ ), array(), EXTERNAL_WOO_PAY_VER, 'all' );
			wp_enqueue_script( 'exwoopay-script', plugins_url( 'assets/js/exwoopay-script.js', __FILE__ ), array( 'jquery' ), EXTERNAL_WOO_PAY_VER, true );
		}

		public function plugins_loaded_hooked(){
			if($this -> is_product_site){
				require( __DIR__ . '/inc/payment-gateway.php' );
			}
		}
		
	}
	

	/**
	 * Instantiate class
	 */
	External_Woo_Pay::get_instance();

	require( __DIR__ . '/inc/admin/admin-settings.php' );
	require( __DIR__ . '/inc/restendpoints/rest_endpoints.php' );
	// require( __DIR__ . '/inc/productsite/functions-product-site.php' );
	// require( __DIR__ . '/inc/paymentsite/functions-payment-site.php' );

}


add_action('wp_footer', 'exwoopay_modal_iframessss', 99);
function exwoopay_modal_iframessss(){
      
    if(isset($_GET['pay_frame'])):
        $iframe = $_GET['pay_frame'];
    ?>
        <div class="exwoopay_modal_wrap exwoopay_active" id="exwoopay_modal">
            <div class="exwoopay_modal_container">
                <div class="exwoopay_modal_content">
                    <iframe class="exwoopay_payment_url" src="<?php echo $iframe;?>" frameborder="0"></iframe>
                </div>
            </div>
        </div>
		<script>
			setInterval(function(){
				var frame = document.querySelector('#exwoopay_modal .exwoopay_payment_url');
				var url = new URL(frame.contentWindow.location.href);
				var params = new URLSearchParams(url.search);
				var check_key = params.get("payment_done"); 
				if(check_key){
					window.location.href = frame.contentWindow.location.href;
					console.log('Redirecting... to ' + frame.contentWindow.location.href);
				}
			}, 1000);
		</script>
    <?php 
    endif;
}