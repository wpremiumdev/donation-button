<?php

class Donation_Button_Campaign_Monitor_API {

    public function __construct($api_key) {
        
        require_once('csrest_clients.php');
        require_once ('csrest_general.php');
        $this->api_key = $api_key;
    }

    public function get_lists() {
        
        $client_id = get_option('campaignmonitor_client_id');
        $auth = array('api_key' => $this->api_key);
        $wrap = new CS_REST_Clients($client_id, $auth);
        $result = $wrap->get_lists();
        $list_name = $result->response;
        return $list_name;
    }
}
?>