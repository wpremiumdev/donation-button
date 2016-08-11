<?php

/**
 * @class      Donation_Button_Admin_Display
 * @since      1.0.0
 * @package    Donation_Button
 * @subpackage Donation_Button/includes
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Admin_Display {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_menu'));
    }

    public static function add_settings_menu() {

        add_options_page('Paypal Donation Options', 'Paypal Donation', 'manage_options', 'donation-button', array(__CLASS__, 'donation_button_options'));
    }

    public static function donation_button_options() {
        $setting_tabs = apply_filters('donation_button_options_setting_tab', array('general' => 'General', 'email' => 'Send Email', 'mailchimp' => 'MailChimp', 'getresponse' => 'Getresponse', 'icontact' => 'Icontact', 'infusionsoft' => 'Infusionsoft', 'constantcontact' => 'Constant Contact', 'campaignmonitor' => 'Campaign Monitor', 'twilio' => 'Twilio', 'help' => 'Help'));
        $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($setting_tabs as $name => $label)
                echo '<a href="' . admin_url('admin.php?page=donation-button&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            ?>
        </h2>
        <?php
        foreach ($setting_tabs as $setting_tabkey => $setting_tabvalue) {
            switch ($setting_tabkey) {
                case $current_tab:
                    do_action('donation_button_' . $setting_tabkey . '_setting_save_field');
                    do_action('donation_button_' . $setting_tabkey . '_setting');
                    break;
            }
        }
    }

}

Donation_Button_Admin_Display::init();