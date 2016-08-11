<?php

/**
 * @class       Donation_Button_Campaign_Monitor_Helper
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Campaign_Monitor_Helper {

    public static function init() {
        $enable_campaignmonitor = get_option('enable_campaignmonitor');
        if (isset($enable_campaignmonitor) && $enable_campaignmonitor == 'yes') {
            add_action('donation_button_campaign_monitor_handler', array(__CLASS__, 'donation_button_campaign_monitor_handler'), 10, 1);
        }
    }

    public static function donation_button_campaign_monitor_handler($posted) {

        $cm_api_key = get_option("campaignmonitor_api_key");
        $client_id = get_option("campaignmonitor_client_id");
        $cm_list_id = get_option("campaignmonitor_lists");

        $fname = isset($posted['first_name']) ? $posted['first_name'] : '';
        $lname = isset($posted['last_name']) ? $posted['last_name'] : '';
        $email = isset($posted['payer_email']) ? $posted['payer_email'] : $posted['receiver_email'];

        $debug = (get_option('log_enable_campaignmonitor') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            $log = new Donation_Button_Logger();
        }

        if ((isset($cm_api_key) && !empty($cm_api_key)) && (isset($client_id) && !empty($client_id)) && (isset($cm_list_id) && !empty($cm_list_id))) {

            include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/campaign_monitor/csrest_subscribers.php';
            $wrap = new CS_REST_Subscribers($cm_list_id, $cm_api_key);
            try {

                $response = $wrap->get($email);
                if ($response->http_status_code == "200") {

                    $result = $wrap->update($email, array(
                        'EmailAddress' => $email,
                        'Name' => $fname . ' ' . $lname,
                        'CustomFields' => array(),
                        'Resubscribe' => true
                    ));
                    if ("yes" == $debug) {

                        if ($response->response->State == "Unsubscribed") {
                            $log->add('CampaignMonitor', ' CampaignMonitor new contact ' . $email . ' added to selected contact list');
                        } else {
                            $log->add('CampaignMonitor', ' CampaignMonitor update contact ' . $email . ' to selected contact list');
                        }
                    }
                } else {

                    $result = $wrap->add(array(
                        'EmailAddress' => $email,
                        'Name' => $fname . ' ' . $lname,
                        'CustomFields' => array(),
                        'Resubscribe' => true
                    ));
                    if (isset($result) && 'yes' == $debug) {
                        $log->add('CampaignMonitor', ' CampaignMonitor new contact ' . $email . ' added to selected contact list');
                    }
                }
            } catch (Exception $e) {

                if ('yes' == $debug) {
                    $log->add('CampaignMonitor', prin_r($e, true));
                }
            }
        } else {

            if ('yes' == $debug) {
                $log->add('CampaignMonitor', 'Campaign Monitor API Key OR Campaign Monitor Client ID does not set');
            }
        }
    }

}

Donation_Button_Campaign_Monitor_Helper::init();