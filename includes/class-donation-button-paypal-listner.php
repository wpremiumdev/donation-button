<?php

/**
 * @class       Donation_Button_General_Setting
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_PayPal_listner {

    public function __construct() {

        $this->liveurl = 'https://ipnpb.paypal.com/cgi-bin/webscr';
        $this->testurl = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
    }

    public function check_ipn_request() {
        @ob_clean();
        $ipn_response = !empty($_POST) ? $_POST : false;
        if ($ipn_response && $this->check_ipn_request_is_valid($ipn_response)) {
            header('HTTP/1.1 200 OK');
            return true;
        } else {
            return false;
        }
    }

    public function check_ipn_request_is_valid($ipn_response) {
        $is_sandbox = (isset($ipn_response['test_ipn'])) ? 'yes' : 'no';
        if ('yes' == $is_sandbox) {
            $paypal_adr = $this->testurl;
        } else {
            $paypal_adr = $this->liveurl;
        }
        $validate_ipn = array('cmd' => '_notify-validate');
        $validate_ipn += stripslashes_deep($ipn_response);
        $params = array(
            'body' => $validate_ipn,
            'sslverify' => false,
            'timeout' => 60,
            'httpversion' => '1.1',
            'compress' => false,
            'decompress' => false,
            'user-agent' => 'donation-button/'
        );
        $response = wp_remote_post($paypal_adr, $params);
        if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr($response['body'], 'VERIFIED')) {
            return true;
        }
        return false;
    }

    public function successful_request($IPN_status) {
        $ipn_response = !empty($_POST) ? $_POST : false;
        $ipn_response['IPN_status'] = ( $IPN_status == true ) ? 'Verified' : 'Invalid';
        $posted = stripslashes_deep($ipn_response);
        do_action('donation_button_send_notification_mail', $posted);
        $this->ipn_response_data_handler($posted);
    }

    public function ipn_response_data_handler($posted = null) {
        global $wp;
        $debug = (get_option('log_enable_general_settings') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            $log = new Donation_Button_Logger();
            $log->add('paypal_donation_button_post_callback', print_r($posted, true));
        }
        if (isset($posted) && !empty($posted)) {
            if (isset($posted['txn_id'])) {
                $paypal_txn_id = $posted['txn_id'];
            } else {
                return false;
            }
            if ($this->donation_button_exist_post_by_title($paypal_txn_id) == false) {
                $insert_ipn_array = array(
                    'ID' => '',
                    'post_type' => 'donation_list',
                    'post_status' => 'publish',
                    'post_title' => $paypal_txn_id,
                );
                $post_id = wp_insert_post($insert_ipn_array);
                if ('yes' == get_option('enable_mailchimp')) {
                    do_action('donation_button_mailchimp_handler', $posted);
                }
                if ('yes' == get_option('enable_getresponse')) {
                    do_action('donation_button_getresponse_handler', $posted);
                }
                if ('yes' == get_option('enable_icontact')) {
                    do_action('donation_button_icontact_handler', $posted);
                }
                if ('yes' == get_option('enable_infusionsoft')) {
                    do_action('donation_button_infusionsoft_handler', $posted);
                }
                if ('yes' == get_option('enable_constant_contact')) {
                    do_action('donation_button_constant_contact_handler', $posted);
                }
                if ('yes' == get_option('enable_campaignmonitor')) {
                    do_action('donation_button_campaign_monitor_handler', $posted);
                }
                if ('yes' == get_option('donation_button_twilio_sms_enable_admin_sms')) {
                    do_action('donation_button_twilio_sms_handler', $posted);
                }
                $this->ipn_response_postmeta_handler($post_id, $posted);
            } else {
                $post_id = $this->donation_button_exist_post_by_title($paypal_txn_id);
                wp_update_post(array('ID' => $post_id, 'post_status' => 'publish'));
                $this->ipn_response_postmeta_handler($post_id, $posted);
            }
        }
    }

    public function ipn_response_postmeta_handler($post_id, $posted) {
        foreach ($posted as $metakey => $metavalue)
            update_post_meta($post_id, $metakey, $metavalue);
    }

    function donation_button_exist_post_by_title($ipn_txn_id) {
        global $wpdb;
        $post_data = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s ", $ipn_txn_id, 'donation_list'));
        if (empty($post_data)) {
            return false;
        } else {
            return $post_data[0];
        }
    }

}