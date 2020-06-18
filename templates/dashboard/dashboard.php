<?php
/**
 * Dashboard Page
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
do_action('bkx_before_dashboard');
?>
    <div class="container-fluid">
        <div class="row">
            <?php
            do_action('bkx_dashboard_navigation');
            do_action('bkx_dashboard_content');
            ?>
        </div>
    </div>
<?php
do_action('bkx_after_dashboard');
