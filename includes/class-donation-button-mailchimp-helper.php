<?php

/**
 * @class       Donation_Button_MailChimp_Helper
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_MailChimp_Helper {

    public static function init() {
        $enable_mailchimp = get_option('enable_mailchimp');
        if (isset($enable_mailchimp) && $enable_mailchimp == 'yes') {
            add_action('donation_button_mailchimp_handler', array(__CLASS__, 'donation_button_mailchimp_handler'), 10, 1);
        }
    }

    public static function donation_button_mailchimp_handler($posted) {

        if (!isset($posted) || empty($posted)) {
            return;
        }
        $debug = (get_option('log_enable_mailchimp') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            $log = new Donation_Button_Logger();
        }
        $apikey = get_option('mailchimp_api_key');
        $listId = get_option('mailchimp_lists');
        $first_name = isset($posted['first_name']) ? $posted['first_name'] : '';
        $last_name = isset($posted['last_name']) ? $posted['last_name'] : '';
        $payer_email = isset($posted['payer_email']) ? $posted['payer_email'] : $posted['receiver_email'];
        $merge_vars = array('FNAME' => $first_name, 'LNAME' => $last_name);

        if (isset($apikey) && !empty($apikey)) {
            if (isset($listId) && !empty($listId)) {
                include_once 'class-donation-button-mcapi.php';
                $api = new Donation_Button_MailChimp_MCAPI($apikey);
                try {
                    $retval = $api->listSubscribe($listId, $payer_email, $merge_vars, $email_type = 'html');
                    if ('yes' == $debug) {
                        if ("true" == $retval) {
                            $log->add('MailChimp', $payer_email . ' Successfully Add Contact in Mailchimp');
                        } else {
                            $log->add('MailChimp', $payer_email . ' in Mailchimp');
                        }
                    }
                } catch (Mailchimp_Error $e) {
                    if ('yes' == $debug) {
                        $log->add('MailChimp', print_r($e, true));
                    }
                }
            } else {
                if ('yes' == $debug) {
                    $log->add('MailChimp', 'MailChimp List Id is Empty.');
                }
            }
        } else {
            if ('yes' == $debug) {
                $log->add('MailChimp', 'MailChimp Api Key is Empty.');
            }
        }
    }

}
Donation_Button_MailChimp_Helper::init();