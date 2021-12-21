<?php

/**
 * Plugin Name:  WP Nathan
 * Description:  Small description
 * Plugin URI:   ""
 * Author:       Nathan Rodrigues
 * Version:      1.0
 * Text Domain:  wp-nathan
 * Domain Path:  /languages
 * License:      GPL v2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/

/**
* Exit if file called directly
*/
if (! defined('ABSPATH')) {
    exit;
}

define('WPNAT_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('WPNAT_URL', trailingslashit(plugins_url('/', __FILE__)));
define('WPNAT_TETXDOMAIN', 'wp-nathan');
define('WPNAT_PLUGIN_NAME', 'WP Nathan');

/**
* Public functions
*/

if (! class_exists('WpnatPublicFunctions')) {
    include_once WPNAT_PATH . 'assets/classes/class-public-functions.php';
}

/**
* Admin functions
*/

if (! class_exists('WpnatAdminFunctions')) {
    include_once WPNAT_PATH . 'admin/classes/class-admin-functions.php';
}
