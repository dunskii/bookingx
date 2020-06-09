<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<ul class="nav nav-tabs bkx-nav-tabs" id="bkx-dashboard" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="future-tab" data-toggle="tab" href="#future" role="tab" aria-controls="future" aria-selected="true"><?php echo __( 'Future Bookings', 'bookingx' ) ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="past-tab" data-toggle="tab" href="#past" role="tab" aria-controls="past" aria-selected="false"><?php echo __( 'Past Bookings', 'bookingx' ) ?></a>
    </li>
    <?php do_action('bkx_dashboard_bookings_nav_tabs');?>
</ul>
