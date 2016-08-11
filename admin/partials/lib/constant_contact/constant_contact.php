<?php
require_once('Ctct/autoload.php');
use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;
    
$concontact_api_key = get_option('constantcontact_api_key');
$constantcontact_access_token = get_option('constantcontact_access_token');

define("APIKEY", $concontact_api_key);
define("ACCESS_TOKEN", $constantcontact_access_token);

if ((isset($concontact_api_key) && !empty($concontact_api_key)) && ( isset($constantcontact_access_token) && !empty($constantcontact_access_token))) {
    $cc = new ConstantContact(APIKEY);
    try {
        $lists_tmp = $cc->getLists(ACCESS_TOKEN);
    } catch (CtctException $ex) {
        
    }
} else {
    $donation_button_constantcontact_lists['false'] = __("API Key is empty.", 'eddms');
}
?>