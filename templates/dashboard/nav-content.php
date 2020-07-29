<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$BkxDashboard = new BkxDashboard();
$page_id = get_the_ID();
$dashboard_columns = bkx_get_dashboard_orders_columns();
$BkxBooking = new BkxBooking();
$search['search_date'] = date('Y-m-d');
$search['search_by'] = 'future';
$search['user_id'] = get_current_user_id();

$GetFutureBookedRecords = $BkxBooking->GetBookedRecordsByUser($search);
$search['search_by'] = 'past';
$GetPastBookedRecords = $BkxBooking->GetBookedRecordsByUser($search); ?>
<?php do_action('bkx_dashboard_bookings_before_nav_content'); ?>
<div class="tab-content" id="bkx-bookings">
    <div class="tab-pane fade show active" id="future" role="tabpanel" aria-labelledby="future-tab">
        <table class="table dashboard-bookings">
            <thead class="thead-light">
            <tr>
                <?php foreach ($dashboard_columns as $column_id => $column_name) : ?>
                    <th scope="col"
                        class="<?php echo esc_attr($column_id); ?>"><?php echo esc_html($column_name); ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($GetFutureBookedRecords)) {
                echo $BkxDashboard->booking_html($GetFutureBookedRecords, $page_id);
            }else{
                echo "<tr><td colspan='5'>No Booking's Found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
        <table class="table dashboard-bookings">
            <thead class="thead-light">
            <tr>
                <?php foreach ($dashboard_columns as $column_id => $column_name) : ?>
                    <th scope="col" class="<?php echo esc_attr($column_id); ?>"><?php echo esc_html($column_name); ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($GetPastBookedRecords)) {
                echo $BkxDashboard->booking_html($GetPastBookedRecords, $page_id);
            }else{
                echo "<tr><td colspan='5'>No Booking's Found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php do_action('bkx_dashboard_bookings_nav_content'); ?>
</div>
<?php do_action('bkx_dashboard_bookings_after_nav_content'); ?>
