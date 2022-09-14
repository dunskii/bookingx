<?php //phpcs:ignore
/**
 * Template load for Addon License Section and Settings
 *
 * @package Bookingx/admin
 * @since      1.0.14
 */

defined( 'ABSPATH' ) || exit;
if ( ! empty( $current_submenu_active ) && 'all_license' === $current_submenu_active ) :?>
<h3>Manage BookingX Addons </h3>
	<?php
	$active_plugins = get_option( 'active_plugins' );
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$addon_html     = '';
	$is_addon_found = 0;
	if ( ! empty( $active_plugins ) ) {
		foreach ( $active_plugins as $plugin_file ) {
			$plugin_dir  = ABSPATH . 'wp-content/plugins/' . $plugin_file;
			$plugin_data = get_plugin_data( $plugin_dir );
			$check_is_ok = bkx_check_is_active_addons( $plugin_data );
			if ( true === $check_is_ok ) {
				$addon_alias    = str_replace( '-', '_', $plugin_data['TextDomain'] );
				$addon_function = $addon_alias . '_addon_info';
				if ( function_exists( $addon_function ) ) {
					$addon_info                = call_user_func( $addon_function );
					$addon_info['text_domain'] = $plugin_data['TextDomain'];
					$addon_html               .= generate_addon_license_section_fields( $addon_info );
				}
				$is_addon_found++;
			}
		}
	}
	?>
<form method="post" id="bkx-license-set-up">
	<table class="form-table">
		<tbody>
		<?php
		if ( isset( $is_addon_found ) && $is_addon_found > 0 ) {
			echo $addon_html;
			?>
			<tr valign="top">
				<th scope="row" valign="top">
					 &nbsp;
				</th>
				<td>
					<?php wp_nonce_field( 'bkx_license_activation_nonce', 'bkx_license_activation_nonce' ); ?>
					<input type="submit" class="button-secondary" name="bkx_license_activation" value="<?php _e( 'Activate All Licenses' ); ?>"/>
				</td>
			</tr>
			<?php
		} else {
			?>
			<tr valign="top">
				<th scope="row" valign="top">
					<?php
					  // Translators: $s General Submenu Label for Business Info.
			        echo sprintf( __( 'You don\'t have any Booking X add-ons installed or activated. Visit the <a href="%s">Booking X Add-on store</a> to see what is available.', 'bookingx' ), esc_url('https://booking-x.com/add-ons/') ); //phpcs:ignore
					?>
				</th>
			</tr>
		<?php } ?>
		</tbody>
	</table>
<?php endif; ?>
