<?php
/**
 *  Plugin Name: Booking X
 *  Plugin URI: https://booking-x.com/
 *  Description: Booking X is a booking and appointments plugin for WordPress
 *  Version: 0.7.6
 *  Author: Dunskii Web Services
 *  Author URI: https://dunskii.com
 *  Text Domain: bookingx
 *  Domain Path: /i18n/languages/
 *  License:     GPL v3
 */
/**
 * Booking X
 * Copyright (C) 2007-2019, BookingX
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('ABSPATH') || exit;
define('BKX_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('BKX_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('BKX_PLUGIN_PUBLIC_URL', BKX_PLUGIN_DIR_URL . "public");
define('BKX_PLUGIN_PUBLIC_PATH', BKX_PLUGIN_DIR_PATH . "public");
define('BKX_PLUGIN_VER', '0.7.6.1');
define('BKX_BLOCKS_ASSETS', BKX_PLUGIN_DIR_URL . "includes/core/blocks/assets/");
define('BKX_BLOCKS_ASSETS_BASE_PATH', BKX_PLUGIN_DIR_PATH . "includes\core\blocks\assets");
if ( ! defined( 'BKX_PLUGIN_FILE' ) ) {
    define( 'BKX_PLUGIN_FILE', __FILE__ );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bookingx-activator.php
 */

function activate_bookingx(){
    require_once plugin_dir_path(__FILE__) . 'includes/class-bookingx-activator.php';
    $Bookingx_Activator = new Bookingx_Activator();
    $Bookingx_Activator->activate();
}

register_activation_hook(__FILE__, 'activate_bookingx');

require plugin_dir_path(__FILE__) . 'includes/class-bookingx.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 0.7.6
 */
function run_bookingx()
{
    $plugin = new Bookingx();
    $plugin->run();
    // Global for backwards compatibility.
    $GLOBALS['bkx'] = BKX();

    /**
     * Gutenberg Blocks for Gutenberg
     */
    // Load Editor Blocks if WordPress is 5.0+.
    if (function_exists('register_block_type')) {

        // Load block framework.
        if (!class_exists('Bkx_Blocks')) {
            require_once BKX_PLUGIN_DIR_PATH . '/includes/core/blocks/class-bkx-blocks.php';
        }

        // Load included Blocks.
        Bookingx::glob_require_once('/includes/core/blocks/class-bkx-block-*.php');

    }
}

function BKX()
{
    return Bookingx::instance();
}

run_bookingx();