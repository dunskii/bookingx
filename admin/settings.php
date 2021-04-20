<?php
/**
 * Page Load for BookingX Settings
 *
 * @package Bookingx/admin
 * @since      1.0.3
 */

defined( 'ABSPATH' ) || exit;
$screen = get_current_screen();
wp_enqueue_style( 'bkx-seat-sol-style', BKX_PLUGIN_DIR_URL . 'public/css/sol.css' );
wp_enqueue_script( 'iris' );
wp_enqueue_script( 'thickbox' );
wp_enqueue_style( 'thickbox' );
wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_style( 'jquery-ui-style' );
wp_enqueue_script( 'jquery-ui-autocomplete' );
$temp_option = bkx_crud_option_multisite( 'bkx_siteuser_canedit_seat' );
$temp_css    = bkx_crud_option_multisite( 'bkx_siteclient_canedit_css' ); ?>
<div class="wrap booking-x-setting">
	<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br></div>
	<h2>Booking X Settings</h2>
	<?php
	/*
	*  Print the error or success messages if any while processing.
	*/
	if ( isset( $_REQUEST['bkx_success'] ) && '' !== $_REQUEST['bkx_success'] ) { //phpcs:ignore
        $bkx_success = sanitize_text_field( $_REQUEST['bkx_success'] ); //phpcs:ignore
		echo '<div class="updated"><p><strong>' . esc_html( bkx_admin_success_message( $bkx_success ) ) . '</strong></p></div>';
	}
	$country               = bkx_get_wp_country();
	$bkx_setting_tabs      = bkx_setting_tabs();
	$bkx_setting_tabs_html = '';
	if ( ! empty( $bkx_setting_tabs ) ) {
     $current_main_tab       = isset( $_GET['bkx_tab'] ) ? sanitize_text_field( $_GET['bkx_tab'] ) : 'bkx_general'; //phpcs:ignore
		$bkx_setting_tabs_html .= '<h2 class="nav-tab-wrapper">';
		foreach ( $bkx_setting_tabs as $key => $bkx_tabs ) {
			$main_label             = $bkx_setting_tabs[ $key ]['label'];
			$tab_is_active          = ( $current_main_tab === $key ) ? 'nav-tab-active' : '';
			$generate_tab_url       = $screen->parent_file . '&page=bkx-setting&bkx_tab=' . $key;
			$bkx_setting_tabs_html .= '<a href="' . esc_url( $generate_tab_url ) . '" class="nav-tab ' . esc_attr( $tab_is_active ) . '">' . esc_html( ucwords( $main_label ) ) . '</a>';
		}
		$bkx_setting_tabs_html .= '</h2>';
	}
	echo $bkx_setting_tabs_html; //phpcs:ignore

	if ( ! empty( $current_main_tab ) ) {
		$current_setting_tab_path = BKX_PLUGIN_DIR_PATH . 'admin/settings/' . $current_main_tab . '-view.php';
		if ( ! file_exists( $current_setting_tab_path ) ) {
			return;
		}
		$bkx_general_submenu      = $bkx_setting_tabs[ $current_main_tab ]['submenu'];
		$bkx_general_submenu_html = '';
		if ( ! empty( $bkx_general_submenu ) ) {
			$default_active            = $bkx_setting_tabs[ $current_main_tab ]['default'];
         $current_submenu_active    = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : $default_active; //phpcs:ignore
			$bkx_general_submenu_html .= '<ul class="subsubsub">';
			$gen_count                 = 1;
			$gen_max                   = count( $bkx_general_submenu );
			foreach ( $bkx_general_submenu as $general_key => $general_submenu ) {
				$general_label     = explode( '|', $general_submenu );
				$general_tab_url   = $screen->parent_file . '&page=bkx-setting&bkx_tab=' . esc_html( $current_main_tab ) . '&section=' . esc_html( $general_key );
				$submenu_is_active = ( $current_submenu_active === $general_key ) ? 'current' : '';

				$seprater                  = ( $gen_count < $gen_max ) ? ' | ' : '';
				$bkx_general_submenu_html .= '<li><a href="' . esc_url( $general_tab_url ) . '" class="' . esc_attr( $submenu_is_active ) . '">' . esc_attr( $general_label[0] ) . '</a> ' . esc_attr( $seprater ) . ' </li>';
				$gen_count++;
			}
			$bkx_general_submenu_html .= '</ul><p> &nbsp;</p>';
		}
     echo $bkx_general_submenu_html; //phpcs:ignore
		$bkx_general_submenu_label = $bkx_general_submenu[ $current_submenu_active ];
		$bkx_submenu_active_label  = explode( '|', $bkx_general_submenu_label );
		$bkx_general_submenu_label = $bkx_submenu_active_label[0];
		if ( ! empty( $bkx_submenu_active_label[1] ) ) {
			$bkx_general_submenu_label = $bkx_submenu_active_label[1];
		}
		include_once BKX_PLUGIN_DIR_PATH . 'admin/settings/' . $current_main_tab . '-view.php';
		do_action( 'bkx_custom_tab_data_' . esc_html( $current_submenu_active ) );
	}
	?>
</div><!-- WRAP ENDS -->
