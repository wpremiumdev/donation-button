<?php

require_once('csrest_clients.php');
require_once ('csrest_general.php');
require_once ('csrest_subscribers.php');

$auth = array('api_key' => $options_array['campaign_monitor']['api_key']);

$wrap = new CS_REST_Subscribers($campaign_monitor_list, $auth);

$result = $wrap->add(array(
    'EmailAddress' => $email,
    'Name' => $fname . ' ' . $lname,
    'CustomFields' => array(),
    'Resubscribe' => true
        )
);
?>