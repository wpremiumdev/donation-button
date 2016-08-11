<?php

/**
 * @since      1.0.0
 * @package    Donation_Button
 * @subpackage Donation_Button/includes
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_i18n {

    /**
     * The domain specified for this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $domain    The domain identifier for this plugin.
     */
    private $domain;

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
                $this->domain, false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

    public function set_domain($domain) {
        $this->domain = $domain;
    }

}