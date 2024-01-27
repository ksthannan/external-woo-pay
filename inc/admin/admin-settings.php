<?php
/**
 * Admin Settings
 */
class External_Woo_Pay_Admin_Settings extends External_Woo_Pay{

    /**
     * Constructor 
     */
    public function __construct() {

        parent::__construct();

        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

    }

    /**
     * Register admin settings page
     */
    public function register_settings() {
        register_setting( EXTERNAL_WOO_PAY_OPT_GROUP, EXTERNAL_WOO_PAY_OPT_NAME );
    }

    /**
     * Admin menu items
     */
    public function add_menu_item() {
        add_menu_page(
            __( 'External Woo Pay', 'exwoopay' ),
            __( 'External Woo Pay', 'exwoopay' ),
            'manage_options',
            'exwoopay',
            array( $this, 'exwoopay_render_options_page' ),
            'dashicons-visibility'
        );

    }

    /**
     * Admin settings options 
     */
    public function exwoopay_render_options_page(){
        require( __DIR__ . '/admin-options.php' );
    }

}

new External_Woo_Pay_Admin_Settings();
