<?php
/**
 *  Plugin Name: Booking X
 *  Plugin URI: https://booking-x.com/
 *  Description: Booking X is a booking and appointments plugin for WordPress
 *  Version: 1.0.7
 *  Requires at least: 5.0
 *  Requires PHP: 7.0
 *  Author: Booking X
 *  Author URI: https://booking-x.com/
 *  Text Domain: bookingx
 *  Domain Path: /i18n/languages/
 *  License:     GPL v3
 *
 * @package Bookingx
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

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'BKX_PLUGIN_FILE' ) ) {
	define( 'BKX_PLUGIN_FILE', __FILE__ );
}
define( 'BKX_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BKX_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'BKX_PLUGIN_PUBLIC_URL', BKX_PLUGIN_DIR_URL . 'public' );
define( 'BKX_PLUGIN_PUBLIC_PATH', BKX_PLUGIN_DIR_PATH . 'public' );
define( 'BKX_PLUGIN_VER', '1.0.7' );
define( 'BKX_STORE_URL', 'https://booking-x.com' );
define( 'BKX_PACKAGES_BLOCKS', BKX_PLUGIN_DIR_PATH . 'includes/packages/blocks' );
define( 'BKX_PACKAGES_BLOCKS_URL', BKX_PLUGIN_DIR_URL . 'includes/packages/blocks' );
define( 'BKX_BLOCKS_ASSETS', BKX_PLUGIN_DIR_URL . 'includes/core/blocks/assets/' );
define( 'BKX_BLOCKS_ASSETS_BASE_PATH', BKX_PLUGIN_DIR_PATH . "includes\core\blocks\assets" );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bookingx-activator.php
 */
function activate_bookingx() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-bookingx-activator.php';
	$bookingx_activator = new Bookingx_Activator();
	$bookingx_activator->activate();
}

register_activation_hook( __FILE__, 'activate_bookingx' );
/**
 * The code that runs during plugin deactivate.
 * This action is deactivate_bookingx hook initialized while deactivate plugin
 */
function deactivate_bookingx() {
	wp_clear_scheduled_hook( 'bkx_check_booking_process' );
}

register_deactivation_hook( __FILE__, 'deactivate_bookingx' );

require plugin_dir_path( __FILE__ ) . 'includes/class-bookingx.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0
 */
function run_bookingx() {
	$plugin = new Bookingx();
	$plugin->run();
	// Global for backwards compatibility.
	$GLOBALS['bkx'] = BKX();
}

/**
 * Global BKX() Called
 *
 * @return Bookingx|null
 */
function BKX() {
	return Bookingx::instance();
}
run_bookingx();
