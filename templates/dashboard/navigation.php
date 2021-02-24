<?php
/**
 * BookingX Dashboard Navigation Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
$navigation_menu = bkx_get_dashboard_navigation_menu();
do_action( 'bkx_before_dashboard_navigation' );
?>
	<div class="col-sm-2">
		<div class="nav flex-column nav-pills bkx-dashboard-navigation" id="bkx-dashboard-tab" role="tablist" aria-orientation="vertical">
			<?php foreach ( $navigation_menu as $key => $menu ) : ?>
				<a class="nav-link <?php echo esc_attr( $menu['active'] == 1 ? 'active' : '' ); ?>" id="bkx-dashboard-<?php echo esc_attr( $key ); ?>-tab" data-toggle="pill" href="#bkx-dashboard-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="bkx-dashboard-<?php echo esc_attr( $key ); ?>" aria-selected="<?php echo esc_attr( $menu['active'] == 1 ? 'true' : 'false' ); ?>"><?php echo esc_html( $menu['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
<?php
do_action( 'bkx_after_dashboard_navigation' );



