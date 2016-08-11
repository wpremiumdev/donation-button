<?php

/**
 * @class       Donation_Button_Getrsponse_Helper
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Getrsponse_Helper {

    public static function init() {
        $enable_getresponse = get_option('enable_getresponse');
        if (isset($enable_getresponse) && $enable_getresponse == 'yes') {
            add_action('donation_button_getresponse_handler', array(__CLASS__, 'donation_button_getresponse_handler'), 10, 1);
        }
    }

    public static function donation_button_getresponse_handler($posted) {
        if (!isset($posted) || empty($posted)) {
            return;
        }
        $debug = (get_option('log_enable_Getrsponse') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            $log = new Donation_Button_Logger();
        }
        $apikey = get_option('getresponse_api_key');
        $campaigns = get_option('getresponse_lists');
        $first_name = isset($posted['first_name']) ? $posted['first_name'] : '';
        $last_name = isset($posted['last_name']) ? $posted['last_name'] : '';
        $payer_email = isset($posted['payer_email']) ? $posted['payer_email'] : $posted['receiver_email'];
        $merge_vars = array('FNAME' => $first_name, 'LNAME' => $last_name);
        $name = $first_name . ' ' . $last_name;
        if ((isset($apikey) && !empty($apikey)) && (isset($campaigns) && !empty($campaigns))) {
            include_once DBP_PLUGIN_DIR_PATH . 'admin/partials/lib/getresponse/getresponse.php';
            try {
                $api = new Donation_Button_Getesponse_API($apikey);
                $retval = $api->addContact($campaigns, $name, $payer_email);
                if ('yes' == $debug) {
                    if ($retval) {
                        $log->add('Getresponse', $payer_email . ' Successfully Add Contact in Getresponse');
                    } else {
                        $log->add('Getresponse', $payer_email . ' Contact already added to target campaign in Getresponse');
                    }
                }
            } catch (Exception $e) {
                if ('yes' == $debug) {
                    $log->add('Getresponse', print_r($e, true));
                }
            }
        } else {
            $log->add('Getresponse', 'Getresponse Api Key is Empty.');
        }
    }

}

Donation_Button_Getrsponse_Helper::init();