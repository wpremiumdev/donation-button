<?php

require_once('csrest_clients.php');
require_once ('csrest_general.php');

$auth = array('api_key' => $data_api_key_1);

$wrap = new CS_REST_Clients($data_api_key_2, $auth);

$result = $wrap->get_lists();

$lists = $result->response;

if (count($lists) > 0 and ! isset($lists->Message)) {
    $is_connected = 1;
} else {
    $is_connected = 0;
    $lists = array();
}
?>