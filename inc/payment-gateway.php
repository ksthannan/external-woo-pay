<?php
if ( ! class_exists( 'WC_Gateway_On_Processing' ) ) {
    class WC_Gateway_On_Processing extends WC_Payment_Gateway {

       
        public function __construct() {
            
            $this->id                 = 'exwoopay_on_processing';
            $this->method_title       = 'On Processing';
            $this->method_description = 'Make an order and proceed to payment';

            $this->title              = 'On Processing';
            $this->description        = 'Make an order and proceed to payment';

            $this->supports = array(
                'products',
            );

            $this->init_form_fields();
            $this->init_settings();

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => 'Enable/Disable',
                    'type'    => 'checkbox',
                    'label'   => 'Enable On Processing Gateway',
                    'default' => 'yes',
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'On Processing',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Allow orders to be processed without payment.',
                ),
            );
        }

        public function process_payment( $order_id ) {
            $order = wc_get_order( $order_id );
            // $order->payment_complete();
            $order->update_status('pending');
            $order_total = $order->get_total();
            $order_subtotal = $order->get_subtotal();
            $order_currency = $order->get_currency();

            $External_Woo_Pay = new External_Woo_Pay();
            $payment_store_link = $External_Woo_Pay -> payment_site_link;

            // $payment_store_link = $External_Woo_Pay -> get_option('payment_site_link');
            $checkout_url = wc_get_checkout_url();
            $billing_info = array(
                'first_name'    => $order->get_billing_first_name(),
                'last_name'     => $order->get_billing_last_name(),
                'company'       => $order->get_billing_company(),
                'address_1'     => $order->get_billing_address_1(),
                'address_2'     => $order->get_billing_address_2(),
                'city'          => $order->get_billing_city(),
                'state'         => $order->get_billing_state(),
                'postcode'      => $order->get_billing_postcode(),
                'country'       => $order->get_billing_country(),
                'email'         => $order->get_billing_email(),
                'phone'         => $order->get_billing_phone(),
            );

            $order_data = array(
                'customer' => $billing_info,
                'session_token' => $External_Woo_Pay -> generate_token(16),
                'product_order_id' =>  $order_id,
                'product_site_link_back' =>  $checkout_url,
                'order_total' =>  $order_total,
                'order_subtotal' =>  $order_subtotal,
                'order_currency' =>  $order_currency
            );




            $response = wp_remote_post($payment_store_link . '/wp-json/external-woo-pay/v1/create_payment_url/', array(
                'body'    => json_encode($order_data),
                'headers' => array(
                    'token' => $External_Woo_Pay -> input_token
                ),
            ));
                
            if(is_wp_error( $response )){
                $redirect = '';
            }else{

                $response = wp_remote_retrieve_body($response);
                $data = json_decode($response, true);
                $redirect = $data['payment_link'];
                $_SESSION['exwoopay_session_key'] = $order_data['session_token'];
            }

            $checkout_link = wc_get_checkout_url() . '/?pay_frame=' . urlencode($redirect);

            return array(
                'result'   => 'success',
                'redirect' => $checkout_link
                // 'redirect' => $this->get_return_url( $order ),
            );
        }
    }
}


