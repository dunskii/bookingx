<?php
$plugin_dir = plugin_dir_path( __DIR__ );
require_once($plugin_dir.'booking_general_functions.php');

// Seat / Resource Metabox view and update Section

require_once($plugin_dir.'inc/seat_metabox.php');


// Base or Services Metabox view and update Section

require_once($plugin_dir.'inc/base_metabox.php');

// Addition or Extra Metabox view and update Section

require_once($plugin_dir.'inc/extra_metabox.php');