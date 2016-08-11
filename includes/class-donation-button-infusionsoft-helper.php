<?php

/**
 * @class       Donation_Button_Infusionsoft_Helper
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Infusionsoft_Helper {

    public static function init() {
        $enable_infusionsoft = get_option('enable_infusionsoft');
        if (isset($enable_infusionsoft) && $enable_infusionsoft == 'yes') {
            add_action('donation_button_infusionsoft_handler', array(__CLASS__, 'donation_button_infusionsoft_handler'), 10, 1);
        }
    }

    public static function donation_button_infusionsoft_handler($posted) {
        $is_api_key = get_option("infusionsoft_api_key");
        $app_name = get_option("infusionsoft_api_app_name");
        $is_list_id = get_option("infusionsoft_lists");
        $fname = isset($posted['first_name']) ? $posted['first_name'] : '';
        $lname = isset($posted['last_name']) ? $posted['last_name'] : '';
        $email = isset($posted['payer_email']) ? $posted['payer_email'] : $posted['receiver_email'];
        $debug = (get_option('log_enable_infusionsoft') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            $log = new Donation_Button_Logger();
        }
        if ((isset($is_api_key) && !empty($is_api_key)) && (isset($app_name) && !empty($app_name)) && (isset($is_list_id) && !empty($is_list_id))) {
            include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/infusionsoft/isdk.php';
            $app = new iSDK;
            try {
                if ($app->cfgCon($app_name, $is_api_key)) {
                    $contactid = $app->addCon(array('FirstName' => $fname, 'LastName' => $lname, 'Email' => $email));
                    $infusionsoft_result = $app->campAssign($contactid, $is_list_id);
                    if ('yes' == $debug) {
                        $log->add('Infusionsoft', print_r($infusionsoft_result, true));
                    }
                }
            } catch (Exception $e) {
                if ('yes' == $debug) {
                    $log->add('Infusionsoft', print_r($e, true));
                }
            }
        }
    }

}

Donation_Button_Infusionsoft_Helper::init();