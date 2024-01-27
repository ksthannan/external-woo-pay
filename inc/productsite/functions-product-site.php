<?php
/**
 * Product site's features
 */
class External_Woo_Pay_Product_Site extends External_Woo_Pay{

    /**
     * Construct initiates
     */
    public function __construct() {

        // parent::__construct();
        
        // $this -> is_product_site = $this -> get_option( 'product_site' );

        // if($this -> is_product_site){
        //     add_action( 'init', array( $this, 'prepare_cart_data_in_product_site' ) );
        // }
        
    }

    /**
     * Prepare cart data in poduct site
     */
    public function prepare_cart_data_in_product_site(){

        // if($this -> input_token && $this -> payment_site_link){
            
        //     if (isset($_REQUEST['external_checkout_request'])) {
                
        //         $cart = WC()->cart;
        //         $cart_items = $cart->get_cart();
        //         $cart_subtotal = $cart->get_subtotal();
        //         $cart_total  = $cart->get_total('absolute');
        //         $currency_name = get_woocommerce_currency();
        //         $currency_symbol = get_woocommerce_currency_symbol();

        //         $session_token = $this->generate_token(16);
                
        //         $cart_data = array(
        //             // 'items' => $cart_items,
        //             'session_token' => $session_token,
        //             'subtotal' => $cart_subtotal,
        //             'total' => $cart_total,
        //             'currency_name' => $currency_name,
        //             'currency_symbol' => $currency_symbol
        //         );

        //         $_SESSION['exwoopay_data'] = $cart_data;
        //         $this -> update_option($session_token, $cart_data);

        //         $redirect = $this -> payment_site_link . '?checkout_request=' . $session_token;
        //         wp_redirect( $redirect );
        //         exit();

        //         /**
        //          * Check payment site's permission
        //          */
        //         // $response = wp_remote_post($this -> payment_site_link . '/wp-json/external-woo-pay/v1/retrieve_product_data/', array(
        //         //     'body'    => json_encode($cart_data),
        //         //     'headers' => array(
        //         //         'token' => $this -> input_token
        //         //     ),
        //         // ));
                    
        //         // if(is_wp_error( $response )){
        //         //     update_option('el_debugging', $response->get_error_message());
        //         // }else{
        //         //     update_option('el_debugging', $response);
        //         //     $redirect = $this -> payment_site_link . '?checkout_request=' . $session_token;
        //         //     wp_redirect( $redirect );
        //         //     exit();

        //         // }
                
        //     }
        // }
    }
}

// new External_Woo_Pay_Product_Site();