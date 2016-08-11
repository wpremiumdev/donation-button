<?php

/**
 * @class      Donation_Button_Admin_Widget
 * @since      1.0.0
 * @package    Donation_Button
 * @subpackage Donation_Button/includes
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Admin_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'donation_button_admin_widget', __('PayPal Donation', 'donation-button')
        );
    }

    public function widget($args, $instance) {
        echo do_shortcode('[paypal_donation_button]');
    }

    public function form($instance) {
        $donation_button_custom_button = get_option('donation_button_custom_button');
        $donation_button_button_image = get_option('donation_button_button_image');
        if (isset($donation_button_button_image) && !empty($donation_button_button_image)) {
            switch ($donation_button_button_image) {
                case 'button1':
                    $button_url = 'https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif';
                    break;
                case 'button2':
                    $button_url = 'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif';
                    break;
                case 'button3':
                    $button_url = 'https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif';
                    break;
                case 'button4':
                    $button_url = 'https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_74x21.png';
                    break;
                case 'button5':
                    $button_url = 'https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_92x26.png';
                    break;
                case 'button6':
                    $button_url = 'https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_cc_147x47.png';
                    break;
                case 'button7':
                    $button_url = 'https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_pp_142x27.png';
                    break;
                case 'button8':
                    $button_url = 'https://www.paypalobjects.com/en_AU/i/btn/x-click-but11.gif';
                    break;
                case 'button9':
                    $button_url = 'https://www.paypalobjects.com/en_AU/i/btn/x-click-but21.gif';
                    break;
                case 'button10':
                    $button_url = get_option('donation_button_custom_button');
                    break;
            }
        } elseif (isset($donation_button_custom_button) && !empty($donation_button_custom_button)) {
            $button_url = $donation_button_custom_button;
        } else {
            $button_url = 'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif';
        }
        $donation_button_button_label = get_option('donation_button_button_label');
        $output = '';
        if (isset($donation_button_button_label) && !empty($donation_button_button_label)) {
            $output .= '<p><label for=' . esc_attr($donation_button_button_label) . '>' . esc_attr($donation_button_button_label) . '</label></p>';
        }
        if (isset($button_url) && !empty($button_url)) {
            $output .= '<input type="image" name="submit" border="0" src="' . esc_url($button_url) . '" alt="PayPal - The safer, easier way to pay online">';
        }
        echo $output;
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

    public function donation_button_button_generator_Test() {
        $donation_button_custom_button = get_option('donation_button_custom_button');
        $donation_button_button_image = get_option('donation_button_button_image');
        $donation_button_reference = get_option('donation_button_reference');
        $donation_button_purpose = get_option('donation_button_purpose');
        $donation_button_amount = get_option('donation_button_amount');
        $donation_button_notify_url = get_option('donation_button_notify_url');
        $donation_button_return_page = site_url('?Donation_Button&action=ipn_handler');
        $donation_button_currency = get_option('donation_button_currency');
        $donation_button_bussiness_email = get_option('donation_button_bussiness_email');
        $donation_button_PayPal_sandbox = get_option('donation_button_PayPal_sandbox');
        $donation_button_button_label = get_option('donation_button_button_label');
        if (isset($donation_button_button_image) && !empty($donation_button_button_image)) {
            switch ($donation_button_button_image) {
                case 'button1':
                    $button_url = 'https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif';
                    break;
                case 'button2':
                    $button_url = 'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif';
                    break;
                case 'button3':
                    $button_url = 'https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif';
                    break;
            }
        } elseif (isset($donation_button_custom_button) && !empty($donation_button_custom_button)) {
            $button_url = $donation_button_custom_button;
        } else {
            $button_url = 'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif';
        }
        if (isset($donation_button_PayPal_sandbox) && $donation_button_PayPal_sandbox == 'yes') {
            $paypal_url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $paypal_url = 'https://ipnpb.paypal.com/cgi-bin/webscr';
        }
        $output = '';
        $output .= '<form action="' . esc_url($paypal_url) . '" method="post" target="_blank">';
        if (isset($donation_button_button_label) && !empty($donation_button_button_label)) {
            $output .= '<p><label for=' . esc_attr($donation_button_button_label) . '>' . esc_attr($donation_button_button_label) . '</label></p>';
        }
        $output .= '<input type="hidden" name="business" value="' . esc_attr($donation_button_bussiness_email) . '">';
        $output .= '<input type="hidden" name="bn" value="mbjtechnolabs_SP">';
        $output .= '<input type="hidden" name="cmd" value="_donations">';
        if (isset($donation_button_purpose) && !empty($donation_button_purpose)) {
            $output .= '<input type="hidden" name="item_name" value="' . esc_attr($donation_button_purpose) . '">';
        }
        if (isset($donation_button_reference) && !empty($donation_button_reference)) {
            $output .= '<input type="hidden" name="item_number" value="' . esc_attr($donation_button_reference) . '">';
        }
        if (isset($donation_button_amount) && !empty($donation_button_amount)) {
            $output .= '<input type="hidden" name="amount" value="' . esc_attr($donation_button_amount) . '">';
        }
        if (isset($donation_button_currency) && !empty($donation_button_currency)) {
            $output .= '<input type="hidden" name="currency_code" value="' . esc_attr($donation_button_currency) . '">';
        }
        if (isset($donation_button_notify_url) && !empty($donation_button_notify_url)) {
            $output .= '<input type="hidden" name="notify_url" value="' . esc_url($donation_button_notify_url) . '">';
        }
        if (isset($donation_button_return_page) && !empty($donation_button_return_page)) {
            $donation_button_return_page = get_permalink($donation_button_return_page);
            $output .= '<input type="hidden" name="return" value="' . esc_url($donation_button_return_page) . '">';
        }
        $output .= '<input type="image" name="submit" border="0" src="' . esc_url($button_url) . '" alt="PayPal - The safer, easier way to pay online">';
        $output .= '</form>';
        return $output;
    }

}

function register_foo_widget() {
    register_widget('Donation_Button_Admin_Widget');
}

add_action('widgets_init', 'register_foo_widget');