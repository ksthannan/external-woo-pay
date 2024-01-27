<?php
/**
 * Custom Functions 
 */
trait External_Woo_Pay_Functions{

    /**
     * Sesstion start 
     */
    public function start_custom_session() {
        if ( ! session_id() ) {
            session_start();
        }

        // WC()->session->set_customer_session_cookie(true);
    }

    /**
     * Order button text change 
     */
    public function exwoopay_change_place_order_button_text($button_text) {
        return __('Proceed to Payment', 'exwoopay');
    }

    /**
     * Iframe modal payment box
     */
     public function exwoopay_modal_iframe(){
      
        if(isset($_GET['pay_frame'])):
            $iframe = $_GET['pay_frame'];
        ?>
            <!-- <div class="exwoopay_modal_wrap exwoopay_active" id="exwoopay_modal">
                <div class="exwoopay_modal_container">
                    <div class="exwoopay_modal_content">
                        <iframe class="exwoopay_payment_url" src="<?php echo $iframe;?>" frameborder="0"></iframe>
                    </div>
                </div>
            </div> -->
        <?php 
        endif;
     }

    /**
     * get option functions for settings field
     */
    public function get_option( $option_name, $default = '' ) {
        if ( is_null( $this->options ) ) $this->options = ( array ) get_option( EXTERNAL_WOO_PAY_OPT_NAME, array() );
        if ( isset( $this->options[$option_name] ) ) return $this->options[$option_name];
        return $default;
    }

    /**
     * Update functions for settings field
     */
    public function update_option( $option_name, $default = '' ) {
        if ( is_null( $this->options ) ) $this->options = ( array ) get_option( EXTERNAL_WOO_PAY_OPT_NAME, array() );
        $this->options[$option_name] = $default;
        update_option( EXTERNAL_WOO_PAY_OPT_NAME, $this->options);
    }

    /**
     * Update functions for settings field
     */
    public function delete_option( $option_name) {
        if ( is_null( $this->options ) ) $this->options = ( array ) get_option( EXTERNAL_WOO_PAY_OPT_NAME, array() );
        unset($this->options['exwoopay_' . $option_name]);
        update_option( EXTERNAL_WOO_PAY_OPT_NAME, $this->options);
    }

    /**
     * Generate Token 
     */
    public function generate_token($digit = 8){
        $randomBytes = random_bytes($digit);
        $hexToken = bin2hex($randomBytes);
        return $hexToken;
    }

    public function update_token(){
        $self_token = $this->get_option('self_token');
        if(empty($self_token)){
            $self_token = $this->generate_token(20);
            $this->update_option('self_token', $self_token);
        }
        return $self_token;
    }

    /**
     * Change woocommerce checkout URL on cart page
     */
    public function custom_checkout_url($url) {
        $new_checkout_url = site_url('/?external_checkout_request=checkoutb');
        if( $this -> is_product_site ) return $new_checkout_url;
    }

    /**
     * Register Custom Payment Gateway On Processing
     */
    public function add_on_processing_gateway( $methods ) {
        $methods[] = 'WC_Gateway_On_Processing';
        return $methods;
    }

    /**
     * DO custom functions init
     */
    public function do_custom_functions_init(){
        if(isset($_REQUEST['exwoopay_id'])){

            if(isset($_SESSION['exwoopay_session_key'])){

                $session_key = $_SESSION['exwoopay_session_key'];

                $exwoopay_id = $_REQUEST['exwoopay_id'];
                
                $response = wp_remote_post($this->payment_site_link . '/wp-json/external-woo-pay/v1/order_payment_status/', array(
                    'body'    => json_encode(array(
                        'order_id' => $exwoopay_id,
                        'exwoopay_session_key' => $session_key,
                    )),
                    'headers' => array(
                        'token' => $this -> input_token
                    )
                ));

                if(is_wp_error( $response )){

                }else{

                    $response = wp_remote_retrieve_body($response);
                    $data = json_decode($response, true);

                    $checkout_url = wc_get_checkout_url();
                    $order_key = '';
                    if(isset($data['order_saved_info']) ){
                        $order = wc_get_order( $data['order_saved_info']['product_order_id'] );
                        $order->set_status($data['payment_status']);
                        $order_key = $order->get_order_key();
                        $order->save();
                    }

                    $order_url = $checkout_url . '/order-received/' . $data['order_saved_info']['product_order_id'];

                    wp_remote_post($this->payment_site_link . '/wp-json/external-woo-pay/v1/clear_exwoopay_data/', array(
                        'body'    => json_encode(array(
                            'exwoopay_session_key' => $session_key,
                        )),
                        'headers' => array(
                            'token' => $this -> input_token
                        )
                    ));


                    wp_redirect($order_url . '?key=' . $order_key . '&payment_done=true');
                    exit;

                }
            }
            

        }

        if(isset($_REQUEST['external_payment']) && $this->is_payment_site){
            add_filter('body_class', array($this, 'add_custom_body_class_for_payment_page'));
        }

    }

    /**
     * body class to payment site
     */
    public function add_custom_body_class_for_payment_page($classes) {
        $classes[] = 'exwoopay_payment';
        return $classes;
    }
    /**
     * body class to product site
     */
    public function add_custom_body_class_for_product_site($classes) {
        $classes[] = 'exwoopay_product';
        return $classes;
    }

    /**
     * Thank you page redirect back to product site
     */
    public function thank_you_redirect_back($link){
        $session_key = $_SESSION['exwoopay_session_key'];
        $order_info = $this->get_option('exwoopay_' . $session_key);
        if(isset($order_info) ){
            $thank_you_url = $order_info['product_site_link_back'] . '/order-received/' . $order_info['product_order_id'] . '/?exwoopay_id=' . $order_info['order_id'];
            return $thank_you_url;
        }
        
        return $link;
    }

}
