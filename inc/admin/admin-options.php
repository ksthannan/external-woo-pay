<?php 

defined( 'ABSPATH' ) or die; 

// echo '<pre>';
// $order = wc_get_order(67);
// $order_key = $order->get_order_key();

// var_dump($order_key);
// echo '<br>';
// // $_SESSION = array();

// $debug = $_SESSION;
// var_dump($debug);
// echo '</pre>';
?>
<div class="wrap wrap-exwoopay-content">
	<h1><?php _e( 'External Woo Pay Settings', 'exwoopay' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_errors(); ?>
		<?php settings_fields( EXTERNAL_WOO_PAY_OPT_GROUP ); ?>
		<?php do_settings_sections( EXTERNAL_WOO_PAY_OPT_GROUP ); ?>
		
		<div class="payment_site_settings <?php if($this -> is_payment_site) echo 'exwoopay_active';?>">
			<h3><?php _e( 'Payment Site Settings', 'exwoopay' );	?> </h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e( 'Use this website for payment gateway', 'exwoopay' );?></th>
						<td>
							<label for="payment_site"> 
								<input type="checkbox" name="<?php esc_attr_e( EXTERNAL_WOO_PAY_OPT_NAME ); ?>[payment_site]" value="1" id="payment_site" <?php checked('1', $this -> is_payment_site, true);?>>
								<?php _e( 'Enable', 'exwoopay' );?>
							</label>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Website Token', 'exwoopay' );?></th>
						<td><input class="regular-text" type="text" name="<?php esc_attr_e( EXTERNAL_WOO_PAY_OPT_NAME ); ?>[self_token]" value="<?php esc_attr_e( $this -> self_token ); ?>" readonly>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Product Site Domain (A)', 'exwoopay' );?></th>
						<td><input class="regular-text" type="text" name="<?php esc_attr_e( EXTERNAL_WOO_PAY_OPT_NAME ); ?>[product_site_link]" placeholder="exampleA.com" value="<?php esc_attr_e( $this -> product_site_link ); ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="product_site_settings <?php if($this -> is_product_site) echo 'exwoopay_active';?>">
			<h3><?php _e( 'Product Site Settings', 'exwoopay' );	?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e( 'Use this website for products', 'exwoopay' );?></th>
						<td>
							<label for="product_site"> 
								<input type="checkbox" name="<?php esc_attr_e( EXTERNAL_WOO_PAY_OPT_NAME ); ?>[product_site]" value="1" id="product_site" <?php checked('1', $this -> is_product_site, true);?>>
								<?php _e( 'Enable', 'exwoopay' );?>
							</label>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Payment Site\'s Token (B)', 'exwoopay' );?></th>
						<td><input class="regular-text" type="text" name="<?php esc_attr_e( EXTERNAL_WOO_PAY_OPT_NAME ); ?>[input_token]" value="<?php esc_attr_e( $this -> input_token ); ?>">
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Payment Site Domain (B)', 'exwoopay' );?></th>
						<td><input class="regular-text" type="text" name="<?php esc_attr_e( EXTERNAL_WOO_PAY_OPT_NAME ); ?>[payment_site_link]" placeholder="exampleB.com" value="<?php esc_attr_e( $this -> payment_site_link ); ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php submit_button(); ?>
	</form>
</div>