<?php

require_once('Ctct/autoload.php');

define("APIKEY", $options_array['constant_contact']['api_key']);
define("ACCESS_TOKEN", $options_array['constant_contact']['access_token']);

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

$cc = new ConstantContact(APIKEY);
try {
    $response = $cc->getContactByEmail(ACCESS_TOKEN, $email);
    if (empty($response->results)) {
        $action = "Creating Contact";
        $contact = new Contact();
        $contact->addEmail($email);
        $contact->addList($constant_contact_list);
        $contact->first_name = $fname;
        $contact->last_name = $lname;
        $returnContact = $cc->addContact(ACCESS_TOKEN, $contact, false);
    } else {
        $action = "Updating Contact";
        $contact = $response->results[0];
        $contact->addList($constant_contact_list);
        $contact->first_name = $fname;
        $contact->last_name = $lname;
        $returnContact = $cc->updateContact(ACCESS_TOKEN, $contact, false);
    }
} catch (CtctException $ex) {
    
}
?>