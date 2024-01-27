<?php
/**
 * Payment site's features
 */
class External_Woo_Pay_Payment_Site extends External_Woo_Pay{

    /**
     * Construct initiates
     */
    public function __construct() {

        // parent::__construct();
        
        // if($this -> is_payment_site){
        //     add_action( 'init', array( $this, 'catch_cart_data_in_payment_site' ) );
        // }
        
    }

    /**
     * Catch cart data in payment site 
     */
    // public function catch_cart_data_in_payment_site(){
        
    //     if($this -> self_token && $this -> product_site_link){
            
    //         if ( isset($_REQUEST['checkout_request'])) {
                
    //             $token = $_REQUEST['checkout_request'];
                
    //             $data = array(
    //                 'session_token' => $token
    //             );
    //             $response = wp_remote_post($this -> product_site_link . '/wp-json/external-woo-pay/v1/retrieve_product_data/', array(
    //                 'body'    => json_encode($data),
    //                 'headers' => array(
    //                     'Content-Type' => 'application/json',
    //                     'token' => $this -> self_token,
    //                 ),
    //                 'method' => 'POST',
    //             )); 
    //             if(is_wp_error( $response )){
    //                 update_option('el_debugging', $response->get_error_message());
    //             }else{

    //                 $response = wp_remote_retrieve_body($response);

    //                 $data = json_decode($response['body'], true);
                    
    //                 $_SESSION['exwoopay_data'] = $data;






    //                 $checkout_url = wc_get_checkout_url();

    //                 update_option('el_debugging', $data);

    //                 if($this -> product_site_link) wp_redirect($checkout_url);
    //                 exit;
                    
    //             }
                
    //         }
    //     }
    // }

}

// new External_Woo_Pay_Payment_Site();

