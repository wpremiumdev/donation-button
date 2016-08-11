<?php

/**
 * @class       Donation_Button_Twilio_SMS_Helper
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Twilio_SMS_Helper {

    public static function init() {
        $donation_button_twilio_sms_enable_admin_sms = get_option('donation_button_twilio_sms_enable_admin_sms');
        if (isset($donation_button_twilio_sms_enable_admin_sms) && $donation_button_twilio_sms_enable_admin_sms == 'yes') {
            add_action('donation_button_twilio_sms_handler', array(__CLASS__, 'donation_button_twilio_sms_handler'), 10, 1);
        }
    }

    public static function donation_button_twilio_sms_handler($posted) {

        $debug = (get_option('donation_button_twilio_sms_log_errors') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            $log = new Donation_Button_Logger();
        }

        $account_sid = get_option("donation_button_twilio_sms_account_sid");
        $auth_token = get_option("donation_button_twilio_sms_auth_token");
        $from_number = get_option("donation_button_twilio_sms_from_number");
        $template = get_option("donation_button_twilio_sms_admin_sms_template");
        $recipients = get_option("donation_button_twilio_sms_admin_sms_recipients");
        //$log->add('Twilio SMS before', $template);		
        $message = self::replace_message_body($template, $posted);
        //$log->add('Twilio SMS after', $message);

        if ((isset($account_sid) && !empty($account_sid)) && (isset($auth_token) && !empty($auth_token)) && (isset($from_number) && !empty($from_number)) && (isset($recipients) && !empty($recipients))) {

            include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/Twilio.php';
            $http = new Services_Twilio_TinyHttp(
                    'https://api.twilio.com', array('curlopts' => array(
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 2,
            )));

            $client = new Services_Twilio($account_sid, $auth_token, "2010-04-01", $http);
            try {
                $message = $client->account->messages->create(array(
                    "From" => $from_number,
                    "To" => $recipients,
                    "Body" => $message,
                ));
                if ('yes' == get_option('donation_button_twilio_sms_log_errors')) {
                    $log->add('Twilio SMS', 'SMS Sent message ' . $message->sid);
                }
            } catch (Exception $e) {
                if ('yes' == get_option('donation_button_twilio_sms_log_errors')) {
                    $log->add('Twilio SMS', 'SMS Error message ' . $e->getMessage());
                }
            }
        }
    }

    public static function replace_message_body($message, $posted) {

        $replacements_string = array(
            '%first_name%' => isset($posted['first_name']) ? $posted['first_name'] : '',
            '%last_name%' => isset($posted['last_name']) ? $posted['last_name'] : '',
            '%receiver_email%' => isset($posted['receiver_email']) ? $posted['receiver_email'] : '',
            '%payment_date%' => isset($posted['payment_date']) ? $posted['payment_date'] : '',
            '%mc_gross%' => isset($posted['mc_gross']) ? $posted['mc_gross'] : '',
        );
        return str_replace(array_keys($replacements_string), $replacements_string, $message);
    }

}

Donation_Button_Twilio_SMS_Helper::init();
