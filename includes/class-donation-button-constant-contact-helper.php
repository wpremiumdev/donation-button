<?php

/**
 * @class       Donation_Button_Constant_Contact_Helper
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/constant_contact/Ctct/autoload.php';

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;
use Ctct\Auth\SessionDataStore;
use Ctct\Auth\CtctDataStore;
use Ctct\Services;

include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/constant_contact/Ctct/ConstantContact.php';

class Donation_Button_Constant_Contact_Helper {

    public static function init() {
        $enable_constant_contact = get_option('enable_constant_contact');
        if (isset($enable_constant_contact) && $enable_constant_contact == 'yes') {
            add_action('donation_button_constant_contact_handler', array(__CLASS__, 'donation_button_constant_contact_handler'), 10, 1);
        }
    }

    public static function donation_button_constant_contact_handler($posted) {
        $cc_list_id = get_option("donation_button_constantcontact_lists");
        $cc_api_key = get_option("constantcontact_api_key");
        $access_token = get_option("constantcontact_access_token");
        $fname = isset($posted['first_name']) ? $posted['first_name'] : '';
        $lname = isset($posted['last_name']) ? $posted['last_name'] : '';
        $email = isset($posted['payer_email']) ? $posted['payer_email'] : $posted['receiver_email'];
        $debug = (get_option('log_enable_constant_contact') == 'yes') ? 'yes' : 'no';
        if ('yes' == $debug) {
            $log = new Donation_Button_Logger();
        }
        if ((isset($cc_api_key) && !empty($cc_api_key)) && (isset($access_token) && !empty($access_token)) && (isset($cc_list_id) && !empty($cc_list_id))) {
            try {
                $ConstantContact = new ConstantContact($cc_api_key);
                $response = $ConstantContact->getContactByEmail($access_token, $email);
                if (empty($response->results)) {
                    $Contact = new Contact();
                    $Contact->addEmail($email);
                    $Contact->addList($cc_list_id);
                    $Contact->first_name = $fname;
                    $Contact->last_name = $lname;
                    $NewContact = $ConstantContact->addContact($access_token, $Contact, false);
                    if (isset($NewContact) && 'yes' == $debug) {
                        $log->add('ConstantContact', ' ConstantContact new contact ' . $email . ' added to selected contact list');
                    }
                } else {
                    $Contact = $response->results[0];
                    $Contact->first_name = $fname;
                    $Contact->last_name = $lname;
                    $Contact->addList($cc_list_id);
                    $new_contact = $ConstantContact->updateContact($access_token, $Contact, false);
                    if (isset($new_contact) && 'yes' == $debug) {
                        $log->add('ConstantContact', ' ConstantContact update contact ' . $email . ' to selected contact list');
                    }
                }
            } catch (CtctException $ex) {
                $error = $ex->getErrors();
                $log->add('ConstantContact', print_r($error, true));
            }
        } else {
            if ('yes' == $debug) {
                $log->add('ConstantContact', 'Constant Contact API Key OR Constant Contact Access Token does not set');
            }
        }
    }

}

Donation_Button_Constant_Contact_Helper::init();