<?php //phpcs:ignore
/**
 * Template load for Addon License Section and Settings
 *
 * @package Bookingx/admin
 * @since      1.0.6
 */

defined( 'ABSPATH' ) || exit;
if ( ! empty( $current_submenu_active ) && 'all_license' === $current_submenu_active ) :?>
<h3>
	<?php
	// Translators: $s General Submenu Label for Business Info.
	printf( esc_html__( '%s', 'bookingx' ), esc_html( ! empty( $bkx_general_submenu_label ) ? esc_html( $bkx_general_submenu_label ) : 'General Details' ) ); //phpcs:ignore
	?>
</h3>
	<?php
	$active_plugins = get_option( 'active_plugins' );
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$addon_html = '';
	if ( ! empty( $active_plugins ) ) {
		foreach ( $active_plugins as $plugin_file ) {
			$plugin_dir  = ABSPATH . 'wp-content/plugins/' . $plugin_file;
			$plugin_data = get_plugin_data( $plugin_dir );
			$check_is_ok = bkx_check_is_active_addons( $plugin_data );
			if ( $check_is_ok == true ) {
				$addon_alias    = str_replace( '-', '_', $plugin_data['TextDomain'] );
				$addon_function = $addon_alias . '_addon_info';
				if ( function_exists( $addon_function ) ) {
					$addon_info                = call_user_func( $addon_function );
					$addon_info['text_domain'] = $plugin_data['TextDomain'];
					$addon_html               .= generate_addon_license_section_fields( $addon_info );
				}
			}
		}
	}

	?>

<form method="post" id="bkx-license-set-up">
	<table class="form-table">
		<tbody>
		<?php echo $addon_html; ?>
		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Activate License' ); ?>
			</th>
			<td>
			<?php wp_nonce_field( 'bkx_license_activation_nonce', 'bkx_license_activation_nonce' ); ?>
			<input type="submit" class="button-secondary" name="bkx_license_activation" value="<?php _e( 'Activate License' ); ?>"/>
			</td>
		</tr>
		</tbody>
	</table>
<?php endif; ?>
