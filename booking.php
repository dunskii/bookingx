<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 *  Plugin Name: Booking X
 *  Plugin URI: https://booking-x.com/
 *  Description: BookingX is a booking and appointments plugin for WordPress
 *  Version: 0.5.18.09.18.1
 *  Author: Dunskii Web Services
 *  Author URI: https://dunskii.com
 *  Text Domain: bookingx
 *  Domain Path: /i18n/languages/
 *  License:     GPL v3
 */
/**
 * BookingX
 * Copyright (C) 2007-2018, BookingX
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
define('BKX_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('BKX_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('BKX_PLUGIN_VER', '0.5.18.09.18.1');
require_once dirname(__FILE__) . '/BkxAutoload.php';
register_activation_hook(__FILE__, 'bkx_create_base_builtup');
