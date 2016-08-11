<?php

/**
 * @class       Donation_Button_Icontact_Helper
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Icontact_Helper {

    public static function init() {
        $enable_icontact = get_option('enable_icontact');
        if (isset($enable_icontact) && $enable_icontact == 'yes') {
            add_action('donation_button_icontact_handler', array(__CLASS__, 'donation_button_icontact_handler'), 10, 1);
        }
    }

    public static function donation_button_icontact_handler($posted) {
        if (!isset($posted) || empty($posted)) {
            return;
        }
        $ic_api_key = get_option("icontact_api_id");
        $app_password = get_option("icontact_api_password");
        $app_username = get_option("icontact_api_username");
        $icontact_list = get_option("icontact_lists");
        $fname = isset($posted['first_name']) ? $posted['first_name'] : '';
        $lname = isset($posted['last_name']) ? $posted['last_name'] : '';
        $email = isset($posted['payer_email']) ? $posted['payer_email'] : $posted['receiver_email'];
        $debug = (get_option('log_enable_icontact') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            $log = new Donation_Button_Logger();
        }
        if ((isset($ic_api_key) && !empty($ic_api_key)) && (isset($app_username) && !empty($app_username)) && (isset($app_password) && !empty($app_password))) {
            if (isset($icontact_list) && !empty($icontact_list)) {
                include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/icontact/icontact.php';
                iContactApi::getInstance()->setConfig(array(
                    'appId' => $ic_api_key,
                    'apiUsername' => $app_username,
                    'apiPassword' => $app_password,
                ));
                $iContact = iContactApi::getInstance();
                try {
                    $contactid = $iContact->addContact($email, 'normal', null, $fname, $lname, null, null, null, null, null, null, null, null, null);
                    $subscribed = $iContact->subscribeContactToList($contactid->contactId, $icontact_list, 'normal');
                    if ('yes' == $debug) {
                        if ($subscribed) {
                            $log->add('Icontact', $email . ' Successfully Add Contact in iContact');
                        } else {
                            $log->add('Icontact', $email . ' Contact already added to target campaign in iContact');
                        }
                    }
                } catch (Exception $e) {
                    if ('yes' == $debug) {
                        $log->add('Icontact', prin_r($e, true));
                    }
                }
            }
        }
    }

}

Donation_Button_Icontact_Helper::init();