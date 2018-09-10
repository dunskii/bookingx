<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$plugin_dir = plugin_dir_path(__DIR__);
require_once($plugin_dir . 'inc/booking_general_functions.php');

// Seat / Resource Metabox view and update Section

require_once($plugin_dir . 'core/seat_metabox.php');


// Base or Services Metabox view and update Section

require_once($plugin_dir . 'core/base_metabox.php');

// Addition or Extra Metabox view and update Section

require_once($plugin_dir . 'core/extra_metabox.php');

