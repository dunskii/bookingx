<?php
/**
 * BookingX Dashboard Nav Section Content Template Page
 *
 * @link  https://dunskii.com
 * @since 1.0
 *
 * @package    Bookingx
 * @subpackage Bookingx/Templates
 */

defined( 'ABSPATH' ) || exit;
$BkxDashboard               = new BkxDashboard();
$page_id                    = get_the_ID();
$dashboard_columns          = bkx_get_dashboard_orders_columns();
$BkxBooking                 = new BkxBooking();
$search_args['search_date'] = date( 'Y-m-d' ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
$search_args['search_by']   = 'future';
$search_args['user_id']     = get_current_user_id();

$GetFutureBookedRecords   = $BkxBooking->GetBookedRecordsByUser( $search_args );
$search_args['search_by'] = 'past';
$GetPastBookedRecords     = $BkxBooking->GetBookedRecordsByUser( $search_args ); ?>
<?php do_action( 'bkx_dashboard_bookings_before_nav_content' ); ?>
<div class="tab-content" id="bkx-bookings">
	<div class="tab-pane fade show active" id="future" role="tabpanel" aria-labelledby="future-tab">
		<table class="table dashboard-bookings">
			<thead class="thead-light">
			<tr>
				<?php foreach ( $dashboard_columns as $column_id => $column_name ) : ?>
					<th scope="col" class="<?php echo esc_attr( $column_id ); ?>"><?php echo esc_html( $column_name ); ?></th>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tbody>
			<?php
			if ( ! empty( $GetFutureBookedRecords ) ) {
             echo $BkxDashboard->booking_html( $GetFutureBookedRecords, esc_html( $page_id ) ); // phpcs:ignore
			} else {
				echo "<tr><td colspan='5'>" . esc_html__( 'No Booking\'s Found', 'bookingx' ) . '</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>
	<div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
		<table class="table dashboard-bookings">
			<thead class="thead-light">
			<tr>
				<?php foreach ( $dashboard_columns as $column_id => $column_name ) : ?>
					<th scope="col" class="<?php echo esc_attr( $column_id ); ?>"><?php echo esc_html( $column_name ); ?></th>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tbody>
			<?php
			if ( ! empty( $GetPastBookedRecords ) ) {
             echo $BkxDashboard->booking_html( $GetPastBookedRecords, esc_html( $page_id ) ); // phpcs:ignore
			} else {
				echo "<tr><td colspan='5'>No Booking's Found</td></tr>";
			}
			?>
			</tbody>
		</table>
	</div>
	<?php do_action( 'bkx_dashboard_bookings_nav_content' ); ?>
</div>
<?php do_action( 'bkx_dashboard_bookings_after_nav_content' ); ?>
