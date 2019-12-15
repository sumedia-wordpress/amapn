<?php

/**
 * Sumedia Amazon Partnernet
 *
 * @package     Sumedia_Amapn
 * @copyright   Copyright (C) 2019, Sumedia - kontakt@sumedia-webdesign.de
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Sumedia Amapn
 * Plugin URI:  https://github.com/sumedia-wordpress/amapn
 * Description: Use Amazon Partnernet Links with non-tracking data privacy
 * Version:     0.2.1
 * Requires at least: 5.3 (nothing else tested yet)
 * Requires PHP: 5.6.0 (not tested, could work)
 * Author:      Sven Ullmann
 * Author URI:  https://www.sumedia-webdesign.de
 * License:     GPL-3.0-or-later
 * Text Domain: sumedia-amapn
 * Domain Path: /languages/
 * Bug Reporting: https://github.com/sumedia-wordpress/amapn/issues
 *
 * WC requires at least: 3.0
 * WC tested up to: 3.8
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

if (!function_exists( 'add_filter')) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'sumedia-base.php');

if (defined('SUMEDIA_BASE_VERSION')) {

    define('SUMEDIA_AMAPN_VERSION', '0.2.1');
    define('SUMEDIA_AMAPN_PLUGIN_NAME', dirname(plugin_basename(__FILE__)));

    $autoloader = Sumedia_Base_Autoloader::get_instance();
    $autoloader->register_autoload_dir(SUMEDIA_AMAPN_PLUGIN_NAME, 'inc');
    $autoloader->register_autoload_dir(SUMEDIA_AMAPN_PLUGIN_NAME, 'view');
    $autoloader->register_autoload_dir(SUMEDIA_AMAPN_PLUGIN_NAME, Suma\ds('admin/view'));
    $autoloader->register_autoload_dir(SUMEDIA_AMAPN_PLUGIN_NAME, Suma\ds('admin/form'));
    $autoloader->register_autoload_dir(SUMEDIA_AMAPN_PLUGIN_NAME, Suma\ds('admin/table'));
    $autoloader->register_autoload_dir(SUMEDIA_AMAPN_PLUGIN_NAME, Suma\ds('admin/controller'));

    $plugin = new Sumedia_Amapn_Plugin();
    register_activation_hook(__FILE__, [$plugin, 'install']);
    add_action('plugins_loaded', [$plugin, 'init'], 20);
}
