<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!empty($current_submenu_active) && $current_submenu_active == 'bkx_gateway_paypal_express') :?>
<!---Start Paypal settings settings-->
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  "{$bkx_general_submenu_label}" ); ?> </h3>
<a href="https://developer.paypal.com/docs/classic/api/apiCredentials/" target="_blank">Click here</a> to create your paypal account and get API Credentials.
<form name="form_api" id="id_form_api" method="post">
<table cellspacing="0" class="widefat" style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="api_flag" value="1">
        <input type="hidden" name="bkx_setting_form_init" value="1">
		<tr class="active">
			<th scope="row"><label for="Paypal Mode">Paypal Mode</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="radio" name="paypalmode" id="id_modesandbox" value="sandbox" <?php if(bkx_crud_option_multisite('bkx_api_paypal_paypalmode')=="sandbox") echo "checked"; ?>>Sandbox </br>
					<input type="radio" name="paypalmode" id="id_modelive" value="live" <?php if(bkx_crud_option_multisite('bkx_api_paypal_paypalmode')=="live") echo "checked"; ?>> Live
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="API username">API username</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="username" id="id_username" value="<?php echo bkx_crud_option_multisite('bkx_api_paypal_username'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="API password">API password</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="password" id="id_password" value="<?php echo bkx_crud_option_multisite('bkx_api_paypal_password'); ?>">
				</div>
			</td>
		</tr>
		<tr class="active">
			<th scope="row"><label for="API signature">API signature</label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="signature" id="id_signature" value="<?php echo bkx_crud_option_multisite('bkx_api_paypal_signature'); ?>">
				</div>
			</td>
		</tr>
		
		<tr class="active">
			<td class="plugin-title" colspan="2" style="border-style: none;padding: 10px 200px;">
				
			</td>
		</tr>
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_api" id="id_save_api" value="Save Changes" /></p>
</form>
<!---End Paypal settings settings-->
<?php endif; ?>

<?php if(!empty($current_submenu_active) && $current_submenu_active == 'tax_settings') :?>
<!-- Tax Setting -->
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
<form name="form_tax_option" id="id_form_tax_option" method="post">
<table cellspacing="0" class="widefat " style="margin-top:20px;">
	<tbody>
		<input type="hidden" name="tax_option_flag" value="1">
        <input type="hidden" name="bkx_setting_form_init" value="1">
		<tr class="active">
			<th scope="row"><label for="Tax Rate">Tax rate in ( % ) </label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="number" min="0" name="bkx_tax_rate" id="id_bkx_tax_rate" value="<?php echo bkx_crud_option_multisite('bkx_tax_rate'); ?>">
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Tax name">Tax name </label></th>
			<td class="plugin-description">
				<div class="plugin-description">
					<input type="text" name="bkx_tax_name" id="id_bkx_tax_name" value="<?php echo bkx_crud_option_multisite('bkx_tax_name'); ?>">
				</div>
			</td>
		</tr>

		<tr class="active">
			<th scope="row"><label for="Prices entered with tax"> Prices entered with tax </label></th>
			<td class="plugin-description">
				<div class="plugin-description">
						<input type="radio" name="bkx_prices_include_tax" id="id_bkx_prices_include_tax_yes" value="1" <?php if( $bkx_prices_include_tax == 1 ) echo "checked"; ?>> Yes, I will enter prices inclusive of tax  </br>
						<input type="radio" name="bkx_prices_include_tax" id="id_bkx_prices_include_tax_no" value="0" <?php if( $bkx_prices_include_tax == 0 ) echo "checked"; ?>> No, I will enter prices exclusive of tax
				</div>
			</td>
		</tr>
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_tax" id="id_save_tax" value="Save Changes" /></p>
</form>
<!-- End Tax Setting -->
<?php endif; ?>
<?php if(!empty($current_submenu_active) && $current_submenu_active == 'currency') :?>
<h3> <?php printf( esc_html__( '%1$s', 'bookingx' ),  $bkx_general_submenu_label ); ?> </h3>
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
									$currency_option = bkx_crud_option_multisite('currency_option');
									if($currency_option == $code) $cur_selected = "selected"; else $cur_selected = '';
									echo '<option value="'.$code.'" '.$cur_selected.' >'.$name . ' (' . get_bookingx_currency_symbol( $code ) . ')'.'</option>';
								}
							?>
						</select>
						</div>
					</td>
				</tr>
	</tbody>
</table>
<p class="submit"><input type="submit" onclick="" class='button-primary' name="save_currency_option" id="id_save_currency_option" value="Save Changes" /></p>
</form>
<?php endif; ?>