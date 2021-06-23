<?php //phpcs:ignore
/**
 * Template load for Payment Gateway Section and Settings
 *
 * @package Bookingx/admin
 * @since      1.0.6
 */

defined( 'ABSPATH' ) || exit;
if ( ! empty( $current_submenu_active ) && 'bkx_gateway_paypal_express' === $current_submenu_active ) :?>
	<!---Start Paypal settings settings-->
	<h3>
	<?php
	// Translators: $s Payment Gateway Label.
		printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $bkx_general_submenu_label ) ); //phpcs:ignore
	?>
	</h3>
	<a href="https://developer.paypal.com/docs/classic/api/apiCredentials/" target="_blank">Click
		here</a> to create your paypal account and get API Credentials.
	<form name="form_api" id="id_form_api" method="post">
		<table cellspacing="0" class="widefat bkx-admin-paypal-settings" style="margin-top:20px;">
			<tbody>
			<input type="hidden" name="api_flag" value="1">
			<input type="hidden" name="bkx_setting_form_init" value="1">
			<tr class="active">
				<th scope="row"><label for="Paypal Mode">Enable / Disable</label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="paypal_status" id="id_paypal_status_on" value="1"
							<?php
							if ( 1 === bkx_crud_option_multisite( 'bkx_gateway_paypal_express_status' ) ||
							     '1' === bkx_crud_option_multisite( 'bkx_gateway_paypal_express_status' )) {
								echo 'checked';
							}
							?>
						>Enable
						<input type="radio" name="paypal_status" id="id_paypal_status_off" value="0"
							<?php
							if ( 0 === bkx_crud_option_multisite( 'bkx_gateway_paypal_express_status' ) ||
							     '0' === bkx_crud_option_multisite( 'bkx_gateway_paypal_express_status' )) {
								echo 'checked';
							}
							?>
						>
						Disable
					</div>
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="Paypal Mode">PayPal Mode</label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="paypalmode" id="id_modesandbox" value="sandbox"
							<?php
							if ( 'sandbox' === bkx_crud_option_multisite( 'bkx_api_paypal_paypalmode' ) ) {
								echo 'checked';
							}
							?>
						>Sandbox
						<input type="radio" name="paypalmode" id="id_modelive" value="live"
							<?php
							if ( 'live' === bkx_crud_option_multisite( 'bkx_api_paypal_paypalmode' ) ) {
								echo 'checked';
							}
							?>
						>
						Live
					</div>
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="API username">API Username</label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="text" name="username" id="id_username" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_api_paypal_username' ) ); ?>">
					</div>
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="API password">API Password</label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="text" name="password" id="id_password" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_api_paypal_password' ) ); ?>">
					</div>
				</td>
			</tr>
			<tr class="active">
				<th scope="row"><label for="API signature">API Signature</label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="text" name="signature" id="id_signature" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_api_paypal_signature' ) ); ?>">
					</div>
				</td>
			</tr>

			<tr class="active">
				<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">

				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_api" id="id_save_api" value="Save Changes"/></p>
	</form>
	<!---End Paypal settings settings-->
<?php endif; ?>

<?php if ( ! empty( $current_submenu_active ) && 'tax_settings' === $current_submenu_active ) : ?>
	<!-- Tax Setting -->
	<h3>
	<?php
	// Translators: $s Tax Setting.
		printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $bkx_general_submenu_label ) ); //phpcs:ignore
	?>
	</h3>
	<form name="form_tax_option" id="id_form_tax_option" method="post">
		<table cellspacing="0" class="widefat " style="margin-top:20px;">
			<tbody>
			<input type="hidden" name="tax_option_flag" value="1">
			<input type="hidden" name="bkx_setting_form_init" value="1">
			<tr class="active">
				<th scope="row"><label for="Tax Rate">Tax rate in ( % ) </label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="number" min="0" name="bkx_tax_rate" id="id_bkx_tax_rate" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_tax_rate' ) ); ?>">
					</div>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="Tax name">Tax name </label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="text" name="bkx_tax_name" id="id_bkx_tax_name" value="<?php echo esc_attr( bkx_crud_option_multisite( 'bkx_tax_name' ) ); ?>">
					</div>
				</td>
			</tr>

			<tr class="active">
				<th scope="row"><label for="Prices entered with tax"> Prices entered with tax </label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<input type="radio" name="bkx_prices_include_tax" id="id_bkx_prices_include_tax_yes" value="1"
							<?php
							$bkx_prices_include_tax = bkx_crud_option_multisite( 'bkx_prices_include_tax' );
							$bkx_prices_include_tax = ( $bkx_prices_include_tax ) ? $bkx_prices_include_tax : 0;
							if ( 1 === $bkx_prices_include_tax ) {
								echo 'checked';
							}
							?>
						> Yes, I will enter
						prices inclusive of tax  </br>
						<input type="radio" name="bkx_prices_include_tax" id="id_bkx_prices_include_tax_no" value="0"
							<?php
							if ( 0 === $bkx_prices_include_tax ) {
								echo 'checked';
							}
							?>
						> No, I will enter
						prices exclusive of tax
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_tax" id="id_save_tax" value="Save Changes"/></p>
	</form>
	<!-- End Tax Setting -->
<?php endif; ?>
<?php if ( ! empty( $current_submenu_active ) && 'currency' === $current_submenu_active ) : ?>
	<h3> 
	<?php
	// Translators: $s Currency Label.
		printf( esc_html__( '%1$s', 'bookingx' ), esc_html( $bkx_general_submenu_label ) ); //phpcs:ignore
	?>
	</h3>
	<form name="form_payment_option" id="id_form_payment_option" method="post">
		<table cellspacing="0" class="widefat" style="margin-top:20px;">
			<tbody>
			<input type="hidden" name="payment_option_flag" value="1">
			<input type="hidden" name="bkx_setting_form_init" value="1">
			<tr class="active">
				<th scope="row"><label for="Notification"> Currency </label></th>
				<td class="plugin-description">
					<div class="plugin-description">
						<select name="currency_option">
							<?php
							$currency_code_options = get_bookingx_currencies();
							foreach ( $currency_code_options as $code => $name ) {
								$currency_option = bkx_crud_option_multisite( 'currency_option' );
								if ( $code === $currency_option ) {
									$cur_selected = 'selected';
								} else {
									$cur_selected = '';
								}
         echo '<option value="' . esc_attr( $code ) . '"  ' . esc_attr( $cur_selected ) . ' >' . esc_html( $name ) . ' (' . esc_html( get_bookingx_currency_symbol( $code ) ) . ')' . '</option>'; //phpcs:ignore
							}
							?>
						</select>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_currency_option" id="id_save_currency_option" value="Save Changes"/></p>
	</form>
<?php endif; ?>
