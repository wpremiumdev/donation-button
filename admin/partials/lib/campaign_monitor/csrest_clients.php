<?php

require_once dirname(__FILE__) . '/class/base_classes.php';

class CS_REST_Clients extends CS_REST_Wrapper_Base {

    var $_clients_base_route;

    function CS_REST_Clients(
    $client_id, $auth_details, $protocol = 'https', $debug_level = CS_REST_LOG_NONE, $host = 'api.createsend.com', $log = NULL, $serialiser = NULL, $transport = NULL) {

        $this->CS_REST_Wrapper_Base($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
        $this->set_client_id($client_id);
    }

    function set_client_id($client_id) {
        $this->_clients_base_route = $this->_base_route . 'clients/' . $client_id . '/';
    }

    function get_campaigns() {
        return $this->get_request($this->_clients_base_route . 'campaigns.json');
    }

    function get_scheduled() {
        return $this->get_request($this->_clients_base_route . 'scheduled.json');
    }

    function get_drafts() {
        return $this->get_request($this->_clients_base_route . 'drafts.json');
    }

    function get_lists() {
        return $this->get_request($this->_clients_base_route . 'lists.json');
    }

    function get_lists_for_email($email_address) {
        return $this->get_request($this->_clients_base_route .
                        'listsforemail.json?email=' . urlencode($email_address));
    }

    function get_segments() {
        return $this->get_request($this->_clients_base_route . 'segments.json');
    }

    function get_suppressionlist($page_number = NULL, $page_size = NULL, $order_field = NULL, $order_direction = NULL) {

        return $this->get_request_paged($this->_clients_base_route . 'suppressionlist.json', $page_number, $page_size, $order_field, $order_direction, '?');
    }

    function suppress($emails) {
        $data = array('EmailAddresses' => $emails);
        return $this->post_request($this->_clients_base_route . 'suppress.json', $data);
    }

    function unsuppress($email) {
        return $this->put_request($this->_clients_base_route . 'unsuppress.json?email=' . urlencode($email), '');
    }

    function get_templates() {
        return $this->get_request($this->_clients_base_route . 'templates.json');
    }

    function get() {
        return $this->get_request(trim($this->_clients_base_route, '/') . '.json');
    }

    function delete() {
        return $this->delete_request(trim($this->_clients_base_route, '/') . '.json');
    }

    function create($client) {
        if (isset($client['ContactName'])) {
            trigger_error('[DEPRECATION] Use Person->add to set name on a new person in a client. For now, we will create a default person with the name provided.', E_USER_NOTICE);
        }
        if (isset($client['EmailAddress'])) {
            trigger_error('[DEPRECATION] Use Person->add to set email on a new person in a client. For now, we will create a default person with the email provided.', E_USER_NOTICE);
        }
        return $this->post_request($this->_base_route . 'clients.json', $client);
    }

    function set_basics($client_basics) {
        if (isset($client['ContactName'])) {
            trigger_error('[DEPRECATION] Use person->update to set name on a particular person in a client. For now, we will update the default person with the name provided.', E_USER_NOTICE);
        }
        if (isset($client['EmailAddress'])) {
            trigger_error('[DEPRECATION] Use person->update to set email on a particular person in a client. For now, we will update the default person with the email address provided.', E_USER_NOTICE);
        }
        return $this->put_request($this->_clients_base_route . 'setbasics.json', $client_basics);
    }

    function set_payg_billing($client_billing) {
        return $this->put_request($this->_clients_base_route . 'setpaygbilling.json', $client_billing);
    }

    function set_monthly_billing($client_billing) {
        return $this->put_request($this->_clients_base_route . 'setmonthlybilling.json', $client_billing);
    }

    function transfer_credits($transfer_data) {
        return $this->post_request($this->_clients_base_route . 'credits.json', $transfer_data);
    }

    function get_people() {
        return $this->get_request($this->_clients_base_route . 'people.json');
    }

    function get_primary_contact() {
        return $this->get_request($this->_clients_base_route . 'primarycontact.json');
    }

    function set_primary_contact($emailAddress) {
        return $this->put_request($this->_clients_base_route . 'primarycontact.json?email=' . urlencode($emailAddress), '');
    }

}
