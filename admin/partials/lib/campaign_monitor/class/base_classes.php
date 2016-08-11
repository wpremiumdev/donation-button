<?php

require_once dirname(__FILE__) . '/serialisation.php';
require_once dirname(__FILE__) . '/transport.php';
require_once dirname(__FILE__) . '/log.php';

define('CS_REST_WRAPPER_VERSION', '3.1.3');
define('CS_HOST', 'api.createsend.com');
define('CS_OAUTH_BASE_URI', 'https://' . CS_HOST . '/oauth');
define('CS_OAUTH_TOKEN_URI', CS_OAUTH_BASE_URI . '/token');
define('CS_REST_WEBHOOK_FORMAT_JSON', 'json');
define('CS_REST_WEBHOOK_FORMAT_XML', 'xml');

class CS_REST_Wrapper_Result {

    var $response;
    var $http_status_code;

    function CS_REST_Wrapper_Result($response, $code) {
        $this->response = $response;
        $this->http_status_code = $code;
    }

    function was_successful() {
        return $this->http_status_code >= 200 && $this->http_status_code < 300;
    }

}

class CS_REST_Wrapper_Base {

    var $_protocol;
    var $_base_route;
    var $_serialiser;
    var $_transport;
    var $_log;
    var $_default_call_options;

    function CS_REST_Wrapper_Base(
    $auth_details, $protocol = 'https', $debug_level = CS_REST_LOG_NONE, $host = CS_HOST, $log = NULL, $serialiser = NULL, $transport = NULL) {

        if (is_string($auth_details)) {
            $auth_details = array('api_key' => $auth_details);
        }

        $this->_log = is_null($log) ? new CS_REST_Log($debug_level) : $log;

        $this->_protocol = $protocol;
        $this->_base_route = $protocol . '://' . $host . '/api/v3/';

        $this->_log->log_message('Creating wrapper for ' . $this->_base_route, get_class($this), CS_REST_LOG_VERBOSE);

        $this->_transport = is_null($transport) ?
                CS_REST_TRANSPORT_get_available($this->is_secure(), $this->_log) :
                $transport;

        $transport_type = method_exists($this->_transport, 'get_type') ? $this->_transport->get_type() : 'Unknown';
        $this->_log->log_message('Using ' . $transport_type . ' for transport', get_class($this), CS_REST_LOG_WARNING);

        $this->_serialiser = is_null($serialiser) ?
                CS_REST_SERIALISATION_get_available($this->_log) : $serialiser;

        $this->_log->log_message('Using ' . $this->_serialiser->get_type() . ' json serialising', get_class($this), CS_REST_LOG_WARNING);

        $this->_default_call_options = array(
            'authdetails' => $auth_details,
            'userAgent' => 'CS_REST_Wrapper v' . CS_REST_WRAPPER_VERSION .
            ' PHPv' . phpversion() . ' over ' . $transport_type . ' with ' . $this->_serialiser->get_type(),
            'contentType' => 'application/json; charset=utf-8',
            'deserialise' => true,
            'host' => $host,
            'protocol' => $protocol
        );
    }

    function refresh_token() {
        if (!isset($this->_default_call_options['authdetails']) ||
                !isset($this->_default_call_options['authdetails']['refresh_token'])) {
            trigger_error(
                    'Error refreshing token. There is no refresh token set on this object.', E_USER_ERROR);
            return array(NULL, NULL, NULL);
        }
        $body = "grant_type=refresh_token&refresh_token=" . urlencode(
                        $this->_default_call_options['authdetails']['refresh_token']);
        $options = array('contentType' => 'application/x-www-form-urlencoded');
        $wrap = new CS_REST_Wrapper_Base(
                NULL, 'https', CS_REST_LOG_NONE, CS_HOST, NULL, new CS_REST_DoNothingSerialiser(), NULL);

        $result = $wrap->post_request(CS_OAUTH_TOKEN_URI, $body, $options);
        if ($result->was_successful()) {
            $access_token = $result->response->access_token;
            $expires_in = $result->response->expires_in;
            $refresh_token = $result->response->refresh_token;
            $this->_default_call_options['authdetails'] = array(
                'access_token' => $access_token,
                'refresh_token' => $refresh_token
            );
            return array($access_token, $expires_in, $refresh_token);
        } else {
            trigger_error(
                    'Error refreshing token. ' . $result->response->error . ': ' . $result->response->error_description, E_USER_ERROR);
            return array(NULL, NULL, NULL);
        }
    }

    function is_secure() {
        return $this->_protocol === 'https';
    }

    function put_request($route, $data, $call_options = array()) {
        return $this->_call($call_options, CS_REST_PUT, $route, $data);
    }

    function post_request($route, $data, $call_options = array()) {
        return $this->_call($call_options, CS_REST_POST, $route, $data);
    }

    function delete_request($route, $call_options = array()) {
        return $this->_call($call_options, CS_REST_DELETE, $route);
    }

    function get_request($route, $call_options = array()) {
        return $this->_call($call_options, CS_REST_GET, $route);
    }

    function get_request_paged($route, $page_number, $page_size, $order_field, $order_direction, $join_char = '&') {
        if (!is_null($page_number)) {
            $route .= $join_char . 'page=' . $page_number;
            $join_char = '&';
        }

        if (!is_null($page_size)) {
            $route .= $join_char . 'pageSize=' . $page_size;
            $join_char = '&';
        }

        if (!is_null($order_field)) {
            $route .= $join_char . 'orderField=' . $order_field;
            $join_char = '&';
        }

        if (!is_null($order_direction)) {
            $route .= $join_char . 'orderDirection=' . $order_direction;
            $join_char = '&';
        }

        return $this->get_request($route);
    }

    function _call($call_options, $method, $route, $data = NULL) {
        $call_options['route'] = $route;
        $call_options['method'] = $method;

        if (!is_null($data)) {
            $call_options['data'] = $this->_serialiser->serialise($data);
        }

        $call_options = array_merge($this->_default_call_options, $call_options);
        $this->_log->log_message('Making ' . $call_options['method'] . ' call to: ' . $call_options['route'], get_class($this), CS_REST_LOG_WARNING);

        $call_result = $this->_transport->make_call($call_options);

        $this->_log->log_message('Call result: <pre>' . var_export($call_result, true) . '</pre>', get_class($this), CS_REST_LOG_VERBOSE);

        if ($call_options['deserialise']) {
            $call_result['response'] = $this->_serialiser->deserialise($call_result['response']);
        }

        return new CS_REST_Wrapper_Result($call_result['response'], $call_result['code']);
    }

}
