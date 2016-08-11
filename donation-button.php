<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Donation Button
 * Plugin URI:        http://localleadminer.com/
 * Description:       Easy to used PayPal Donation button with Auto Responder.
 * Version:           1.4.9
 * Author:            mbj-webdevelopment
 * Author URI:        http://localleadminer.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       donation-button
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('DBP_PLUGIN_URL'))
    define('DBP_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('DBP_PLUGIN_DIR'))
    define('DBP_PLUGIN_DIR', dirname(__FILE__));

if (!defined('DBP_PLUGIN_DIR_PATH')) {
    define('DBP_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}

if (!defined('DBP_FOR_WORDPRESS_LOG_DIR')) {
    $upload_dir = wp_upload_dir();
    define('DBP_FOR_WORDPRESS_LOG_DIR', $upload_dir['basedir'] . '/donation-button-logs/');
}

/**
 * define plugin basename
 */
if (!defined('DBP_PLUGIN_BASENAME')) {
    define('DBP_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-donation-button-activator.php
 */
function activate_donation_button() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-donation-button-activator.php';
    Donation_Button_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-donation-button-deactivator.php
 */
function deactivate_donation_button() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-donation-button-deactivator.php';
    Donation_Button_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_donation_button');
register_deactivation_hook(__FILE__, 'deactivate_donation_button');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-donation-button.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_donation_button() {

    $plugin = new Donation_Button();
    $plugin->run();
}

run_donation_button();