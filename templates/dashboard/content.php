<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
} ?>
<div class="col-sm-12">
    <?php do_action('bkx_dashboard_before_content'); ?>
    <div class="tab-content" id="bkx-dashboard-tabContent">
        <div class="tab-pane fade show active" id="bkx-dashboard-bookings" role="tabpanel"
             aria-labelledby="bkx-dashboard-bookings-tab">
            <?php bkx_get_template('dashboard/listing.php'); ?>
        </div>
        <div class="tab-pane fade" id="bkx-dashboard-my_account" role="tabpanel"
             aria-labelledby="bkx-dashboard-my_account-tab">
            <?php bkx_get_template('dashboard/my-account/my-account.php'); ?>
        </div>
        <?php do_action('bkx_dashboard_content_html'); ?>
    </div>
    <?php do_action('bkx_dashboard_after_content'); ?>
</div>