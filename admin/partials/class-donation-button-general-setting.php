<?php
/**
 * @class       Donation_Button_General_Setting
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

class Donation_Button_General_Setting {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {

        add_action('donation_button_general_setting', array(__CLASS__, 'donation_button_general_setting_function'));
        add_action('donation_button_email_setting', array(__CLASS__, 'donation_button_email_setting_function'));
        add_action('donation_button_help_setting', array(__CLASS__, 'donation_button_help_setting'));
        add_action('donation_button_mailchimp_setting_save_field', array(__CLASS__, 'donation_button_mailchimp_setting_save_field'));
        add_action('donation_button_mailchimp_setting', array(__CLASS__, 'donation_button_mailchimp_setting'));
        add_action('donation_button_general_setting_save_field', array(__CLASS__, 'donation_button_general_setting_save_field'));
        add_action('donation_button_email_setting_save_field', array(__CLASS__, 'donation_button_email_setting_save_field'));
        add_action('donation_button_getresponse_setting_save_field', array(__CLASS__, 'donation_button_getresponse_setting_save_field'));
        add_action('donation_button_getresponse_setting', array(__CLASS__, 'donation_button_getresponse_setting'));
        add_action('donation_button_icontact_setting_save_field', array(__CLASS__, 'donation_button_icontact_setting_save_field'));
        add_action('donation_button_icontact_setting', array(__CLASS__, 'donation_button_icontact_setting'));
        add_action('donation_button_infusionsoft_setting_save_field', array(__CLASS__, 'donation_button_infusionsoft_setting_save_field'));
        add_action('donation_button_infusionsoft_setting', array(__CLASS__, 'donation_button_infusionsoft_setting'));
        add_action('donation_button_constantcontact_setting_save_field', array(__CLASS__, 'donation_button_constantcontact_setting_save_field'));
        add_action('donation_button_constantcontact_setting', array(__CLASS__, 'donation_button_constantcontact_setting'));
        add_action('donation_button_campaignmonitor_setting_save_field', array(__CLASS__, 'donation_button_campaignmonitor_setting_save_field'));
        add_action('donation_button_campaignmonitor_setting', array(__CLASS__, 'donation_button_campaignmonitor_setting'));
        add_action('donation_button_twilio_setting_save_field', array(__CLASS__, 'donation_button_twilio_setting_save_field'));
        add_action('donation_button_twilio_setting', array(__CLASS__, 'donation_button_twilio_setting'));
        add_action('wp_ajax_donation_button_twilio_send_test_sms', array(__CLASS__, 'donation_button_twilio_send_test_sms'));
    }

    public static function donation_button_email_setting_field() {
        $email_body = "Hello %first_name% %last_name%,
Thank you for your donation!

Your PayPal transaction ID is: %txn_id%
PayPal donation receiver email address: %receiver_email%
PayPal donation date: %payment_date%
PayPal donor first name: %first_name%
PayPal donor last name: %last_name%
PayPal donation currency: %mc_currency%
PayPal donation amount: %mc_gross%

Thanks you very much,
Store Admin";
        update_option('donation_buttons_email_body_text_pre', $email_body);
        $settings = apply_filters('donation_buttons_email_settings', array(
            array('type' => 'sectionend', 'id' => 'email_recipient_options'),
            array('title' => __('Email settings', 'donation-button'), 'type' => 'title', 'desc' => __('Set your own sender name and email address. Default WordPress values will be used if empty.', 'donation-button'), 'id' => 'email_options'),
            array(
                'title' => __('Enable/Disable', 'donation-button'),
                'type' => 'checkbox',
                'desc' => __('Enable this email notification for donor', 'donation-button'),
                'default' => 'yes',
                'id' => 'donation_buttons_donor_notification'
            ),
            array(
                'title' => __('Enable/Disable', 'donation-button'),
                'type' => 'checkbox',
                'desc' => __('Enable this email notification for website admin', 'donation-button'),
                'default' => 'yes',
                'id' => 'donation_buttons_admin_notification'
            ),
            array(
                'title' => __('"From" Name', 'donation-button'),
                'desc' => '',
                'id' => 'donation_buttons_email_from_name',
                'type' => 'text',
                'css' => 'min-width:300px;',
                'default' => esc_attr(get_bloginfo('title')),
                'autoload' => false
            ),
            array(
                'title' => __('"From" Email Address', 'donation-button'),
                'desc' => '',
                'id' => 'donation_buttons_email_from_address',
                'type' => 'email',
                'custom_attributes' => array(
                    'multiple' => 'multiple'
                ),
                'css' => 'min-width:300px;',
                'default' => get_option('admin_email'),
                'autoload' => false
            ),
            array(
                'title' => __('Email subject', 'donation-button'),
                'desc' => '',
                'id' => 'donation_buttons_email_subject',
                'type' => 'text',
                'css' => 'min-width:300px;',
                'default' => 'Thank you for your donation',
                'autoload' => false
            ),
            array('type' => 'sectionend', 'id' => 'email_options'),
            array(
                'title' => __('Email body', 'donation-button'),
                'desc' => __('The text to appear in the Donation Email. Please read more Help section(tab) for more dynamic tag', 'donation-button'),
                'id' => 'donation_buttons_email_body_text',
                'css' => 'width:100%; height: 500px;',
                'type' => 'textarea',
                'editor' => 'false',
                'default' => $email_body,
                'autoload' => false
            ),
            array('type' => 'sectionend', 'id' => 'email_template_options'),
        ));

        return $settings;
    }

    public static function help() {
        echo '<p>' . __('Some dynamic tags can be included in your email template :', 'wp-better-emails') . '</p>
                        <ul>
                                <li>' . __('<strong>%blog_url%</strong> : will be replaced with your blog URL.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%home_url%</strong> : will be replaced with your home URL.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%blog_name%</strong> : will be replaced with your blog name.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%blog_description%</strong> : will be replaced with your blog description.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%admin_email%</strong> : will be replaced with admin email.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%date%</strong> : will be replaced with current date, as formatted in <a href="options-general.php">general options</a>.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%time%</strong> : will be replaced with current time, as formatted in <a href="options-general.php">general options</a>.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%txn_id%</strong> : will be replaced with PayPal donation transaction ID.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%receiver_email%</strong> : will be replaced with PayPal donation receiver email address%.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%payment_date%</strong> : will be replaced with PayPal donation date%.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%first_name%</strong> : will be replaced with PayPal donation first name%.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%last_name%</strong> : will be replaced with PayPal donation last name%.', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%mc_currency%</strong> : will be replaced with PayPal donation currency like USD', 'wp-better-emails') . '</li>
                                <li>' . __('<strong>%mc_gross%</strong> : will be replaced with PayPal donation amount', 'wp-better-emails') . '</li>
                          </ul>';
    }

    public static function donation_button_email_setting_function() {
        $donation_button_setting_fields = self::donation_button_email_setting_field();
        $Html_output = new Donation_Button_Html_output();
        ?>
        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($donation_button_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function donation_button_setting_fields() {
        $currency_code_options = self::get_donation_button_currencies();
        foreach ($currency_code_options as $code => $name) {
            $currency_code_options[$code] = $name . ' (' . self::get_donation_button_symbol($code) . ')';
        }
        $fields[] = array('title' => __('PayPal Account Setup', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('Enable PayPal sandbox', 'donation-button'),
            'type' => 'checkbox',
            'id' => 'donation_button_PayPal_sandbox',
            'label' => __('Enable PayPal sandbox', 'donation-button'),
            'default' => 'no',
            'css' => 'min-width:300px;',
            'desc' => sprintf(__('PayPal sandbox can be used to test payments. Sign up for a developer account <a href="%s">here</a>.', 'donation-button'), 'https://developer.paypal.com/'),
        );
        $fields[] = array(
            'title' => __('PayPal Email Address or Merchant Account ID', 'donation-button'),
            'type' => 'text',
            'id' => 'donation_button_bussiness_email',
            'desc' => __('This is the Paypal Email Address or Merchant Account ID where the payments will go.', 'donation-button'),
            'default' => '',
            'placeholder' => 'you@youremail.com',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );
        $fields[] = array(
            'title' => __('Currency', 'donation-button'),
            'desc' => __('This is the currency for your visitors to make Payments or Donations in.', 'donation-button'),
            'id' => 'donation_button_currency',
            'css' => 'min-width:250px;',
            'default' => 'GBP',
            'type' => 'select',
            'class' => 'chosen_select',
            'options' => $currency_code_options
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        $fields[] = array('title' => __('Optional Settings', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('Button Label', 'donation-button'),
            'type' => 'text',
            'id' => 'donation_button_button_label',
            'desc' => __('PayPal donation button label  (Optional).', 'donation-button'),
            'default' => '',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );
        $fields[] = array(
            'title' => __('Return Page', 'donation-button'),
            'id' => 'donation_button_return_page',
            'desc' => __('URL to which the donator comes to after completing the donation; for example, a URL on your site that displays a "Thank you for your donation".', 'donation-button'),
            'type' => 'single_select_page',
            'default' => '',
            'class' => 'chosen_select_nostd',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Amount', 'donation-button'),
            'type' => 'text',
            'id' => 'donation_button_amount',
            'desc' => __('The default amount for a donation (Optional).', 'donation-button'),
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Purpose', 'donation-button'),
            'type' => 'text',
            'id' => 'donation_button_purpose',
            'desc' => __('The default purpose of a donation (Optional).', 'donation-button'),
            'default' => '',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );
        $fields[] = array(
            'title' => __('Reference', 'donation-button'),
            'type' => 'text',
            'id' => 'donation_button_reference',
            'desc' => __('Default reference for the donation (Optional).', 'donation-button'),
            'default' => '',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        $fields[] = array('title' => __('Donation Button', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('Select Donation Button', 'donation-button'),
            'id' => 'donation_button_button_image',
            'default' => 'no',
            'type' => 'radio',
            'desc' => __('Select Button.', 'donation-button'),
            'options' => array(
                'button1' => __('<img style="vertical-align: middle;" alt="small" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif">', 'donation-button'),
                'button2' => __('<img style="vertical-align: middle;" alt="large" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif">', 'donation-button'),
                'button3' => __('<img style="vertical-align: middle;" alt="cards" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif">', 'donation-button'),
                'button4' => __('<img style="vertical-align: middle;" alt="cards" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_74x21.png">', 'donation-button'),
                'button5' => __('<img style="vertical-align: middle;" alt="cards" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_92x26.png">', 'donation-button'),
                'button6' => __('<img style="vertical-align: middle;" alt="cards" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_cc_147x47.png">', 'donation-button'),
                'button7' => __('<img style="vertical-align: middle;" alt="cards" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_pp_142x27.png">', 'donation-button'),
                'button8' => __('<img style="vertical-align: middle;" alt="cards" src="https://www.paypalobjects.com/en_AU/i/btn/x-click-but11.gif">', 'donation-button'),
                'button9' => __('<img style="vertical-align: middle;" alt="cards" src="https://www.paypalobjects.com/en_AU/i/btn/x-click-but21.gif">', 'donation-button'),
                'button10' => __('Custom Button ( If you select this option then pleae enter url in Custom Button textbox, Otherwise donation button will not display. )', 'donation-button')
            ),
        );
        $fields[] = array(
            'title' => __('Debug Log', 'donation-button'),
            'id' => 'log_enable_general_settings',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'donation-button'),
            'default' => 'no',
            'desc' => sprintf(__('Log General events, inside <code>%s</code>', 'donation-button'), DBP_PLUGIN_DIR)
        );
        $fields[] = array(
            'title' => __('Custom Button', 'donation-button'),
            'type' => 'text',
            'id' => 'donation_button_custom_button',
            'desc' => __('Enter a URL to a custom donation button.', 'donation-button'),
            'default' => '',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function donation_button_general_setting_save_field() {
        $donation_button_setting_fields = self::donation_button_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        $Html_output->save_fields($donation_button_setting_fields);
    }

    public static function donation_button_email_setting_save_field() {
        $donation_button_email_setting_field = self::donation_button_email_setting_field();
        $Html_output = new Donation_Button_Html_output();
        $Html_output->save_fields($donation_button_email_setting_field);
    }

    public static function donation_button_help_setting() {
        ?>
        <div class="postbox">
            <h2><label for="title">&nbsp;&nbsp;Plugin Usage</label></h2>
            <div class="inside">      
                <p>There are a few ways you can use this plugin:</p>
                <ol>
                    <li>Configure the options below and then add the shortcode <strong>[paypal_donation_button]</strong> to a post or page (where you want the payment button)</li>
                    <li>Call the function from a template file: <strong>&lt;?php echo do_shortcode( '[paypal_donation_button]' ); ?&gt;</strong></li>
                    <li>Use the <strong>PayPal Donation</strong> Widget from the Widgets menu</li>
                </ol>
                <p><h3>Archive of PayPal Buttons and Images</h3><br>
                The following reference pages list the localized PayPal buttons and images and their URLs.
                </p>
                <p><h4>English</h4></p>
                <ul>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/AU/">Australia</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/US-UK/">United Kingdom</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/US-UK/">United States</a></li>
                </ul>
                <p><h4>Asia-Pacific</h4></p>
                <ul>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/JP/">Japan</a></li>
                </ul>
                <p><h4>EU Non-English</h4></p>
                <ul>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/DE/">Germany</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/ES/">Spain</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/FR/">France</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/IT/">Italy</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/NL/">Netherlands</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/PL/">Poland</a></li>
                </ul>
                <br>
                <h2> <label>Email dynamic tag list</label></h2>
        <?php self::help(); ?>
            </div></div>
        <?php
    }

    public static function donation_button_general_setting_function() {
        $donation_button_setting_fields = self::donation_button_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        ?>
        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($donation_button_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function get_donation_button_currencies() {
        return array_unique(
                apply_filters('donation_button_currencies', array(
            'AED' => __('United Arab Emirates Dirham', 'donation-button'),
            'AUD' => __('Australian Dollars', 'donation-button'),
            'BDT' => __('Bangladeshi Taka', 'donation-button'),
            'BRL' => __('Brazilian Real', 'donation-button'),
            'BGN' => __('Bulgarian Lev', 'donation-button'),
            'CAD' => __('Canadian Dollars', 'donation-button'),
            'CLP' => __('Chilean Peso', 'donation-button'),
            'CNY' => __('Chinese Yuan', 'donation-button'),
            'COP' => __('Colombian Peso', 'donation-button'),
            'CZK' => __('Czech Koruna', 'donation-button'),
            'DKK' => __('Danish Krone', 'donation-button'),
            'DOP' => __('Dominican Peso', 'donation-button'),
            'EUR' => __('Euros', 'donation-button'),
            'HKD' => __('Hong Kong Dollar', 'donation-button'),
            'HRK' => __('Croatia kuna', 'donation-button'),
            'HUF' => __('Hungarian Forint', 'donation-button'),
            'ISK' => __('Icelandic krona', 'donation-button'),
            'IDR' => __('Indonesia Rupiah', 'donation-button'),
            'INR' => __('Indian Rupee', 'donation-button'),
            'NPR' => __('Nepali Rupee', 'donation-button'),
            'ILS' => __('Israeli Shekel', 'donation-button'),
            'JPY' => __('Japanese Yen', 'donation-button'),
            'KIP' => __('Lao Kip', 'donation-button'),
            'KRW' => __('South Korean Won', 'donation-button'),
            'MYR' => __('Malaysian Ringgits', 'donation-button'),
            'MXN' => __('Mexican Peso', 'donation-button'),
            'NGN' => __('Nigerian Naira', 'donation-button'),
            'NOK' => __('Norwegian Krone', 'donation-button'),
            'NZD' => __('New Zealand Dollar', 'donation-button'),
            'PYG' => __('Paraguayan Guaraní', 'donation-button'),
            'PHP' => __('Philippine Pesos', 'donation-button'),
            'PLN' => __('Polish Zloty', 'donation-button'),
            'GBP' => __('Pounds Sterling', 'donation-button'),
            'RON' => __('Romanian Leu', 'donation-button'),
            'RUB' => __('Russian Ruble', 'donation-button'),
            'SGD' => __('Singapore Dollar', 'donation-button'),
            'ZAR' => __('South African rand', 'donation-button'),
            'SEK' => __('Swedish Krona', 'donation-button'),
            'CHF' => __('Swiss Franc', 'donation-button'),
            'TWD' => __('Taiwan New Dollars', 'donation-button'),
            'THB' => __('Thai Baht', 'donation-button'),
            'TRY' => __('Turkish Lira', 'donation-button'),
            'USD' => __('US Dollars', 'donation-button'),
            'VND' => __('Vietnamese Dong', 'donation-button'),
            'EGP' => __('Egyptian Pound', 'donation-button'),
                        )
                )
        );
    }

    public static function get_donation_button_symbol($currency = '') {
        if (!$currency) {
            $currency = get_donation_button_currencies();
        }
        switch ($currency) {
            case 'AED' :
                $currency_symbol = 'د.إ';
                break;
            case 'BDT':
                $currency_symbol = '&#2547;&nbsp;';
                break;
            case 'BRL' :
                $currency_symbol = '&#82;&#36;';
                break;
            case 'BGN' :
                $currency_symbol = '&#1083;&#1074;.';
                break;
            case 'AUD' :
            case 'CAD' :
            case 'CLP' :
            case 'COP' :
            case 'MXN' :
            case 'NZD' :
            case 'HKD' :
            case 'SGD' :
            case 'USD' :
                $currency_symbol = '&#36;';
                break;
            case 'EUR' :
                $currency_symbol = '&euro;';
                break;
            case 'CNY' :
            case 'RMB' :
            case 'JPY' :
                $currency_symbol = '&yen;';
                break;
            case 'RUB' :
                $currency_symbol = '&#1088;&#1091;&#1073;.';
                break;
            case 'KRW' : $currency_symbol = '&#8361;';
                break;
            case 'PYG' : $currency_symbol = '&#8370;';
                break;
            case 'TRY' : $currency_symbol = '&#8378;';
                break;
            case 'NOK' : $currency_symbol = '&#107;&#114;';
                break;
            case 'ZAR' : $currency_symbol = '&#82;';
                break;
            case 'CZK' : $currency_symbol = '&#75;&#269;';
                break;
            case 'MYR' : $currency_symbol = '&#82;&#77;';
                break;
            case 'DKK' : $currency_symbol = 'kr.';
                break;
            case 'HUF' : $currency_symbol = '&#70;&#116;';
                break;
            case 'IDR' : $currency_symbol = 'Rp';
                break;
            case 'INR' : $currency_symbol = 'Rs.';
                break;
            case 'NPR' : $currency_symbol = 'Rs.';
                break;
            case 'ISK' : $currency_symbol = 'Kr.';
                break;
            case 'ILS' : $currency_symbol = '&#8362;';
                break;
            case 'PHP' : $currency_symbol = '&#8369;';
                break;
            case 'PLN' : $currency_symbol = '&#122;&#322;';
                break;
            case 'SEK' : $currency_symbol = '&#107;&#114;';
                break;
            case 'CHF' : $currency_symbol = '&#67;&#72;&#70;';
                break;
            case 'TWD' : $currency_symbol = '&#78;&#84;&#36;';
                break;
            case 'THB' : $currency_symbol = '&#3647;';
                break;
            case 'GBP' : $currency_symbol = '&pound;';
                break;
            case 'RON' : $currency_symbol = 'lei';
                break;
            case 'VND' : $currency_symbol = '&#8363;';
                break;
            case 'NGN' : $currency_symbol = '&#8358;';
                break;
            case 'HRK' : $currency_symbol = 'Kn';
                break;
            case 'EGP' : $currency_symbol = 'EGP';
                break;
            case 'DOP' : $currency_symbol = 'RD&#36;';
                break;
            case 'KIP' : $currency_symbol = '&#8365;';
                break;
            default : $currency_symbol = '';
                break;
        }
        return apply_filters('donation_button_currency_symbol', $currency_symbol, $currency);
    }

    public static function donation_button_mcapi_setting_fields() {
        $fields[] = array('title' => __('MailChimp Integration', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array('title' => __('Enable MailChimp', 'donation-button'), 'type' => 'checkbox', 'desc' => '', 'id' => 'enable_mailchimp');
        $fields[] = array(
            'title' => __('MailChimp API Key', 'donation-button'),
            'desc' => __('Enter your API Key. <a target="_blank" href="http://admin.mailchimp.com/account/api-key-popup">Get your API key</a>', 'donation-button'),
            'id' => 'mailchimp_api_key',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('MailChimp lists', 'donation-button'),
            'desc' => __('After you add your MailChimp API Key above and save it this list will be populated.', 'Option'),
            'id' => 'mailchimp_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => self::donation_buttons_angelleye_get_mailchimp_lists(get_option('mailchimp_api_key'))
        );
        $fields[] = array(
            'title' => __('Force MailChimp lists refresh', 'donation-button'),
            'desc' => __("Check and 'Save changes' this if you've added a new MailChimp list and it's not showing in the list above.", 'donation-button'),
            'id' => 'donation_buttons_force_refresh',
            'type' => 'checkbox',
        );
        $fields[] = array(
            'title' => __('Debug Log', 'donation-button'),
            'id' => 'log_enable_mailchimp',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'donation-button'),
            'default' => 'no',
            'desc' => sprintf(__('Log Mailchimp events, inside <code>%s</code>', 'donation-button'), DBP_PLUGIN_DIR)
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function donation_button_mailchimp_setting() {
        $mcapi_setting_fields = self::donation_button_mcapi_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        ?>
        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($mcapi_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function donation_buttons_angelleye_get_mailchimp_lists($apikey) {
        $mailchimp_lists = array();

        $enable_mailchimp = get_option('enable_mailchimp');
        if (isset($enable_mailchimp) && $enable_mailchimp == 'yes') {
            $mailchimp_lists = unserialize(get_transient('mailchimp_mailinglist'));
            $mailchimp_debug_log = (get_option('log_enable_mailchimp') == 'yes') ? 'yes' : 'no';
            $log = new Donation_Button_Logger();
            if (empty($mailchimp_lists) || get_option('donation_buttons_force_refresh') == 'yes') {
                include_once DBP_PLUGIN_DIR . '/includes/class-donation-button-mcapi.php';
                $mailchimp_api_key = get_option('mailchimp_api_key');
                $apikey = (isset($mailchimp_api_key)) ? $mailchimp_api_key : '';
                $api = new Donation_Button_MailChimp_MCAPI($apikey);
                $retval = $api->lists();
                if ($api->errorCode) {
                    unset($mailchimp_lists);
                    $mailchimp_lists['false'] = __("Unable to load MailChimp lists, check your API Key.", 'doation-button');
                    if ('yes' == $mailchimp_debug_log) {
                        $log->add('MailChimp', 'Unable to load MailChimp lists, check your API Key.');
                    }
                } else {
                    unset($mailchimp_lists);
                    if ($retval['total'] == 0) {
                        if ('yes' == $mailchimp_debug_log) {
                            $log->add('MailChimp', 'You have not created any lists at MailChimp.');
                        }
                        $mailchimp_lists['false'] = __("You have not created any lists at MailChimp", 'doation-button');
                        return $mailchimp_lists;
                    }
                    foreach ($retval['data'] as $list) {
                        $mailchimp_lists[$list['id']] = $list['name'];
                    }
                    if ('yes' == $mailchimp_debug_log) {
                        $log->add('MailChimp', 'MailChimp Get List Success..');
                    }
                    set_transient('mailchimp_mailinglist', serialize($mailchimp_lists), 86400);
                    update_option('donation_buttons_force_refresh', 'no');
                }
            }
        }
        return $mailchimp_lists;
    }

    public static function donation_button_mailchimp_setting_save_field() {
        $mcapi_setting_fields = self::donation_button_mcapi_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        $Html_output->save_fields($mcapi_setting_fields);
    }

    public static function donation_button_getresponse_setting_fields() {
        $fields[] = array('title' => __('Getresponse Integration', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array('title' => __('Enable Getresponse', 'donation-button'), 'type' => 'checkbox', 'desc' => '', 'id' => 'enable_getresponse');
        $fields[] = array(
            'title' => __('Getresponse API Key', 'donation-button'),
            'desc' => __('Enter your API Key. <a target="_blank" href="https://app.getresponse.com/account.html#api">Get your API key</a>', 'donation-button'),
            'id' => 'getresponse_api_key',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Getresponse lists', 'donation-button'),
            'desc' => __('After you add your Getresponse API Key above and save it this list will be populated.', 'Option'),
            'id' => 'getresponse_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => self::donation_button_get_getresponse_lists(get_option('getresponse_api_key'))
        );
        $fields[] = array(
            'title' => __('Force Getresponse lists refresh', 'donation-button'),
            'desc' => __("Check and 'Save changes' this if you've added a new Getresponse list and it's not showing in the list above.", 'donation-button'),
            'id' => 'donation_button_getresponse_force_refresh',
            'type' => 'checkbox',
        );
        $fields[] = array(
            'title' => __('Debug Log', 'donation-button'),
            'id' => 'log_enable_Getresponse',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'donation-button'),
            'default' => 'no',
            'desc' => sprintf(__('Log Getresponse events, inside <code>%s</code>', 'donation-button'), DBP_PLUGIN_DIR)
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function donation_button_getresponse_setting() {
        $getresponse_setting_fields = self::donation_button_getresponse_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        ?>
        <form id="Getresponse_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($getresponse_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="Getresponse_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function donation_button_get_getresponse_lists($apikey) {
        $getresponse_lists = array();

        $enable_getresponse = get_option('enable_getresponse');
        if (isset($enable_getresponse) && $enable_getresponse == 'yes') {
            $getresponse_debug = (get_option('log_enable_Getrsponse') == 'yes') ? 'yes' : 'no';
            $log = new Donation_Button_Logger();
            if (isset($apikey) && !empty($apikey)) {
                $getresponse_lists = unserialize(get_transient('donation_button_getresponse_list'));
                if (empty($getresponse_lists) || get_option('donation_button_getresponse_force_refresh') == 'yes') {

                    include_once DBP_PLUGIN_DIR_PATH . 'admin/partials/lib/getresponse/getresponse.php';
                    $api = new Donation_Button_Getesponse_API($apikey);
                    $campaigns = $api->getCampaigns();
                    $campaigns = (array) $campaigns;

                    if (count($campaigns) > 0 and is_array($campaigns)) {
                        unset($getresponse_lists);
                        foreach ($campaigns as $list_id => $list) {
                            $list = (array) $list;
                            $getresponse_lists[$list_id] = $list['name'];
                        }
                        delete_transient('donation_button_getresponse_list');
                        set_transient('donation_button_getresponse_list', serialize($getresponse_lists), 86400);
                        if ('yes' == $getresponse_debug) {
                            $log->add('Getresponse', 'Getresponse Get List Success..');
                        }
                        update_option('donation_button_getresponse_force_refresh', 'no');
                    } else {
                        unset($getresponse_lists);
                        $getresponse_lists = array();
                        $getresponse_lists['false'] = __("Unable to load Getresponse lists, check your API Key.", 'autoresponder');
                        if ('yes' == $getresponse_debug) {
                            $log->add('Getresponse', 'Unable to load Getresponse lists, check your API Key.');
                        }
                    }
                }
            } else {
                $getresponse_lists['false'] = __("API Key is empty.", 'autoresponder');
                if ('yes' == $getresponse_debug) {
                    $log->add('Getresponse', 'API Key is empty.');
                }
            }
        }
        return $getresponse_lists;
    }

    public static function donation_button_getresponse_setting_save_field() {
        $getresponse_setting_fields = self::donation_button_getresponse_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        $Html_output->save_fields($getresponse_setting_fields);
    }

    public static function donation_button_icontact_setting_fields() {
        $fields[] = array('title' => __('Icontact Integration', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array('title' => __('Enable Icontact', 'donation-button'), 'type' => 'checkbox', 'desc' => '', 'id' => 'enable_icontact');
        $fields[] = array(
            'title' => __('Icontact Application ID', 'donation-button'),
            'desc' => __('Obtained when you Register the API application. <a target="_blank" href="https://app.icontact.com/icp/core/registerapp/">Get your API key</a> This identifier is used to uniquely identify your application.', 'donation-button'),
            'id' => 'icontact_api_id',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Icontact Username/Email ID', 'donation-button'),
            'desc' => __('The iContact username for logging into your iContact account. If you are using the sandbox for testing, this is your sandbox environment username.', 'donation-button'),
            'id' => 'icontact_api_username',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Icontact Application Password', 'donation-button'),
            'desc' => __('The API application password set when the application was registered. This API password is used as input when your application authenticates to the API. This password is not the same as the password you use to log in to iContact.', 'donation-button'),
            'id' => 'icontact_api_password',
            'type' => 'password',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Icontact lists', 'donation-button'),
            'desc' => __('After you add your Icontact API Key above and save it this list will be populated.', 'Option'),
            'id' => 'icontact_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => self::donation_button_get_icontact_lists()
        );
        $fields[] = array(
            'title' => __('Force Icontact lists refresh', 'donation-button'),
            'desc' => __("Check and 'Save changes' this if you've added a new Icontact list and it's not showing in the list above.", 'donation-button'),
            'id' => 'donation_button_icontact_force_refresh',
            'type' => 'checkbox',
        );
        $fields[] = array(
            'title' => __('Debug Log', 'donation-button'),
            'id' => 'log_enable_icontact',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'donation-button'),
            'default' => 'no',
            'desc' => sprintf(__('Log Icontact events, inside <code>%s</code>', 'donation-button'), DBP_PLUGIN_DIR)
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function donation_button_icontact_setting() {
        $icontact_setting_fields = self::donation_button_icontact_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        ?>
        <form id="Icontact_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($icontact_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="Icontact_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function donation_button_get_icontact_lists() {
        $icontact_lists = array();

        $enable_icontact = get_option('enable_icontact');
        if (isset($enable_icontact) && $enable_icontact == 'yes') {

            $icontact_lists = unserialize(get_transient('donation_button_icontact_list'));
            $icontact_debug = (get_option('log_enable_icontact') == 'yes') ? 'yes' : 'no';
            $log = new Donation_Button_Logger();
            if (empty($icontact_lists) || get_option('donation_button_icontact_force_refresh') == 'yes') {
                include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/icontact/icontact.php';
                $icontact_api_id = get_option('icontact_api_id');
                $icontact_api_username = get_option('icontact_api_username');
                $icontact_api_password = get_option('icontact_api_password');
                if ((isset($icontact_api_id) && !empty($icontact_api_id)) && (isset($icontact_api_username) && !empty($icontact_api_username)) && (isset($icontact_api_password) && !empty($icontact_api_password))) {
                    iContactApi::getInstance()->setConfig(array(
                        'appId' => get_option('icontact_api_id'),
                        'apiUsername' => get_option('icontact_api_username'),
                        'apiPassword' => get_option('icontact_api_password'),
                    ));
                    $oiContact = iContactApi::getInstance();
                    try {
                        $lists = $oiContact->getLists();
                    } catch (Exception $oException) {
                        unset($icontact_lists);
                        $icontact_lists['false'] = 'API details is invalid';
                        if ('yes' == $icontact_debug) {
                            $log->add('Icontact', 'Icontact API Details is Invalid.');
                        }
                    }
                    if (count($lists) > 0 and is_array($lists)) {
                        unset($icontact_lists);
                        foreach ($lists as $list) {
                            $icontact_lists[$list->listId] = $list->name;
                        }
                        delete_transient('donation_button_icontact_list');
                        set_transient('donation_button_icontact_list', serialize($icontact_lists), 86400);
                        if ('yes' == $icontact_debug) {
                            $log->add('Icontact', 'Icontact Get List Success..');
                        }
                        update_option('donation_button_icontact_force_refresh', 'no');
                    }
                } else {
                    $icontact_lists['false'] = __("Required information is empty.", 'donation-button');
                    if ('yes' == $icontact_debug) {
                        $log->add('Icontact', 'Required information is empty.');
                    }
                }
            }
        }
        return $icontact_lists;
    }

    public static function donation_button_icontact_setting_save_field() {
        $icontact_setting_fields = self::donation_button_icontact_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        $Html_output->save_fields($icontact_setting_fields);
    }

    public static function donation_button_infusionsoft_setting_fields() {
        $fields[] = array('title' => __('Infusionsoft Integration', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array('title' => __('Enable Infusionsoft', 'donation-button'), 'type' => 'checkbox', 'desc' => '', 'id' => 'enable_infusionsoft');
        $fields[] = array(
            'title' => __('Infusionsoft API Key', 'donation-button'),
            'desc' => __('Enter Infusionsoft API Key', 'donation-button'),
            'id' => 'infusionsoft_api_key',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Infusionsoft Application Name', 'donation-button'),
            'desc' => __('Enter Infusionsoft Application Name.', 'donation-button'),
            'id' => 'infusionsoft_api_app_name',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );

        $fields[] = array(
            'title' => __('Infusionsoft lists', 'donation-button'),
            'desc' => __('After you add your Infusionsoft API Key above and save it this list will be populated.', 'Option'),
            'id' => 'infusionsoft_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => self::donation_button_get_infusionsoft_lists()
        );
        $fields[] = array(
            'title' => __('Force Infusionsoft lists refresh', 'donation-button'),
            'desc' => __("Check and 'Save changes' this if you've added a new Infusionsoft list and it's not showing in the list above.", 'donation-button'),
            'id' => 'donation_button_infusionsoft_force_refresh',
            'type' => 'checkbox',
        );
        $fields[] = array(
            'title' => __('Debug Log', 'donation-button'),
            'id' => 'log_enable_infusionsoft',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'donation-button'),
            'default' => 'no',
            'desc' => sprintf(__('Log Infusionsoft events, inside <code>%s</code>', 'donation-button'), DBP_PLUGIN_DIR)
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function donation_button_infusionsoft_setting() {
        $infusionsoft_setting_fields = self::donation_button_infusionsoft_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        ?>
        <form id="Infusionsoft_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($infusionsoft_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="Infusionsoft_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function donation_button_get_infusionsoft_lists() {
        $infusionsoft_lists = array();


        $enable_infusionsoft = get_option('enable_infusionsoft');
        if (isset($enable_infusionsoft) && $enable_infusionsoft == 'yes') {

            $infusionsoft_debug = (get_option('log_enable_infusionsoft') == 'yes') ? 'yes' : 'no';
            $log = new Donation_Button_Logger();
            $infusionsoft_lists = unserialize(get_transient('donation_button_infusionsoft_list'));
            if (empty($infusionsoft_lists) || get_option('donation_button_infusionsoft_force_refresh') == 'yes') {
                include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/infusionsoft/isdk.php';
                $infusionsoft_api_key = get_option('infusionsoft_api_key');
                $infusionsoft_api_app_name = get_option('infusionsoft_api_app_name');
                if ((isset($infusionsoft_api_key) && !empty($infusionsoft_api_key)) && (isset($infusionsoft_api_app_name) && !empty($infusionsoft_api_app_name))) {
                    $app = new iSDK;
                    try {
                        if ($app->cfgCon($infusionsoft_api_app_name, $infusionsoft_api_key)) {
                            $returnFields = array('Id', 'Name');
                            $query = array('Id' => '%');
                            $lists = $app->dsQuery("Campaign", 1000, 0, $query, $returnFields);
                        }
                    } catch (Exception $e) {
                        unset($infusionsoft_lists);
                        $infusionsoft_lists['false'] = $e->getMessage();
                    }

                    if (count($lists) > 0 and is_array($lists)) {
                        unset($infusionsoft_lists);
                        foreach ($lists as $list) {
                            $infusionsoft_lists[$list['Id']] = $list['Name'];
                        }
                        delete_transient('donation_button_infusionsoft_list');
                        set_transient('donation_button_infusionsoft_list', serialize($infusionsoft_lists), 86400);
                        if ('yes' == $infusionsoft_debug) {
                            $log->add('Infusionsoft', 'Infusionsoft Get List Success..');
                        }
                        update_option('donation_button_infusionsoft_force_refresh', 'no');
                    } else {
                        if ('yes' == $infusionsoft_debug) {
                            $log->add('Infusionsoft', 'Infusionsoft API Key And Application Name Please Check.');
                        }
                    }
                } else {
                    if ('yes' == $infusionsoft_debug) {
                        $log->add('Infusionsoft', 'Required information is empty.');
                    }
                    $infusionsoft_lists['false'] = __("Required information is empty.", 'donation-button');
                }
            }
        }
        return $infusionsoft_lists;
    }

    public static function donation_button_infusionsoft_setting_save_field() {
        $infusionsoft_setting_fields = self::donation_button_infusionsoft_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        $Html_output->save_fields($infusionsoft_setting_fields);
    }

    public static function donation_button_constantcontact_setting_fields() {

        $fields[] = array('title' => __('Constant Contact Integration', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array('title' => __('Enable Constant Contact', 'donation-button'), 'type' => 'checkbox', 'desc' => '', 'id' => 'enable_constant_contact');

        $fields[] = array(
            'title' => __('Constant Contact API Key', 'donation-button'),
            'desc' => __('Enter your API Key. <a target="_blank" href="https://constantcontact.mashery.com/apps/mykeys">Get your API key</a>', 'donation-button'),
            'id' => 'constantcontact_api_key',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Constant Contact Access Token', 'donation-button'),
            'desc' => __('Enter Your Access Token', 'donation-button'),
            'id' => 'constantcontact_access_token',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );

        $fields[] = array(
            'title' => __('Constant Contact lists', 'donation-button'),
            'desc' => __('After you add your Constant Contact API Key above and save it this list will be populated.', 'Option'),
            'id' => 'donation_button_constantcontact_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => self::donation_button_get_constantcontact_lists()
        );

        $fields[] = array(
            'title' => __('Force Constant Contact lists refresh', 'donation-button'),
            'desc' => __("Check and 'Save changes' this if you've added a new Constant Contact list and it's not showing in the list above.", 'donation-button'),
            'id' => 'donation_button_constantcontact_force_refresh',
            'type' => 'checkbox',
        );

        $fields[] = array(
            'title' => __('Debug Log', 'donation-button'),
            'id' => 'log_enable_constant_contact',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'donation-button'),
            'default' => 'no',
            'desc' => sprintf(__('Log Constant Contact events, inside <code>%s</code>', 'donation-button'), DBP_PLUGIN_DIR)
        );


        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        return $fields;
    }

    public static function donation_button_constantcontact_setting() {
        $constantcontact_setting_fields = self::donation_button_constantcontact_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        ?>
        <form id="Constant_Contact_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($constantcontact_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="Constant_Contact_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function donation_button_get_constantcontact_lists() {
        $donation_button_constantcontact_lists = array();

        $enable_constant_contact = get_option('enable_constant_contact');
        if (isset($enable_constant_contact) && $enable_constant_contact == 'yes') {
            $concontact_api_key = get_option('constantcontact_api_key');
            $constantcontact_access_token = get_option('constantcontact_access_token');
            $constant_contact_debug = (get_option('log_enable_constant_contact') == 'yes') ? 'yes' : 'no';
            $log = new Donation_Button_Logger();
            if ((isset($concontact_api_key) && !empty($concontact_api_key)) && ( isset($constantcontact_access_token) && !empty($constantcontact_access_token))) {

                $donation_button_constantcontact_lists = unserialize(get_transient('constantcontact_lists'));
                if (empty($donation_button_constantcontact_lists) || get_option('donation_button_constantcontact_force_refresh') == 'yes') {

                    try {
                        $cc = new ConstantContact($concontact_api_key);
                        $list_name = $cc->getLists($constantcontact_access_token);

                        if (isset($list_name) && !empty($list_name)) {
                            unset($donation_button_constantcontact_lists);
                            $donation_button_constantcontact_lists = array();
                            foreach ($list_name as $list_namekey => $list_namevalue) {

                                $donation_button_constantcontact_lists[$list_namevalue->id] = $list_namevalue->name;
                            }

                            set_transient('constantcontact_lists', serialize($donation_button_constantcontact_lists), 86400);

                            if ('yes' == $constant_contact_debug) {
                                $log->add('ConstantContact', 'ConstantContact Get List Success..');
                            }

                            update_option('donation_button_constantcontact_force_refresh', 'no');
                        } else {

                            if ('yes' == $constant_contact_debug) {
                                $log->add('ConstantContact', 'No ConstantContact List Available, check your API Key.');
                            }

                            $donation_button_constantcontact_lists['false'] = __("Unable to load Constant Contact lists, check your API Key.", 'paypal-ipn-for-wordpress-constant-contact');
                        }
                    } catch (CtctException $ex) {
                        unset($donation_button_constantcontact_lists);
                        $donation_button_constantcontact_lists = array();
                        $donation_button_constantcontact_lists['false'] = __("Unable to load Constant Contact lists, check your API Key.", 'paypal-ipn-for-wordpress-constant-contact');
                        set_transient('constantcontact_lists', serialize($donation_button_constantcontact_lists), 86400);

                        if ('yes' == $constant_contact_debug) {
                            $log->add('ConstantContact', 'Unable to load Constant Contact lists, check your API Key.');
                        }
                    }
                }
            }
        }
        return $donation_button_constantcontact_lists;
    }

    public static function donation_button_constantcontact_setting_save_field() {
        $constantcontact_setting_fields = self::donation_button_constantcontact_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        $Html_output->save_fields($constantcontact_setting_fields);
    }

    public static function donation_button_campaignmonitor_setting_fields() {
        $fields[] = array('title' => __('Campaign Monitor Integration', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array('title' => __('Enable Campaign Monitor', 'donation-button'), 'type' => 'checkbox', 'desc' => '', 'id' => 'enable_campaignmonitor');
        $fields[] = array(
            'title' => __('Campaign Monitor API Key', 'donation-button'),
            'desc' => __('Enter your API Key. <a target="_blank" href="https://login.createsend.com/l">Get your API key And Client Id</a>', 'donation-button'),
            'id' => 'campaignmonitor_api_key',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Campaign Monitor Client ID', 'donation-button'),
            'desc' => __('Enter Campaign Monitor Client ID.', 'donation-button'),
            'id' => 'campaignmonitor_client_id',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Campaign Monitor lists', 'donation-button'),
            'desc' => __('After you add your Campaign Monitor API Key above and save it this list will be populated.', 'Option'),
            'id' => 'campaignmonitor_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => self::donation_button_get_campaignmonitor_lists(get_option('campaignmonitor_api_key'))
        );
        $fields[] = array(
            'title' => __('Force Campaign Monitor lists refresh', 'donation-button'),
            'desc' => __("Check and 'Save changes' this if you've added a new Campaign Monitor list and it's not showing in the list above.", 'donation-button'),
            'id' => 'donation_button_campaignmonitor_force_refresh',
            'type' => 'checkbox',
        );
        $fields[] = array(
            'title' => __('Debug Log', 'donation-button'),
            'id' => 'log_enable_campaignmonitor',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'donation-button'),
            'default' => 'no',
            'desc' => sprintf(__('Log Campaign Monitor events, inside <code>%s</code>', 'donation-button'), DBP_PLUGIN_DIR)
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function donation_button_campaignmonitor_setting() {
        $campaignmonitor_setting_fields = self::donation_button_campaignmonitor_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        ?>
        <form id="campaignmonitor_integration_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($campaignmonitor_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="campaignmonitor_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
            <?php
        }

        public static function donation_button_get_campaignmonitor_lists($apikey) {
            $campaignmonitor_lists = array();

            $enable_campaignmonitor = get_option('enable_campaignmonitor');
            if (isset($enable_campaignmonitor) && $enable_campaignmonitor == 'yes') {

                $campaignmonitor_debug = (get_option('log_enable_campaignmonitor') == 'yes') ? 'yes' : 'no';
                $log = new Donation_Button_Logger();
                if (isset($apikey) && !empty($apikey)) {
                    $campaignmonitor_lists = unserialize(get_transient('donation_button_campaignmonitor_list'));
                    if (empty($campaignmonitor_lists) || get_option('donation_button_campaignmonitor_force_refresh') == 'yes') {
                        include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/campaign_monitor/cmapi.php';
                        $api = new Donation_Button_Campaign_Monitor_API($apikey);
                        $lists = $api->get_lists();
                        if (count($lists) > 0 and is_array($lists)) {
                            unset($campaignmonitor_lists);
                            $campaignmonitor_lists = array();
                            foreach ($lists as $key => $value) {
                                $campaignmonitor_lists[$value->ListID] = $value->Name;
                            }
                            delete_transient('donation_button_campaignmonitor_list');
                            set_transient('donation_button_campaignmonitor_list', serialize($campaignmonitor_lists), 86400);
                            if ('yes' == $campaignmonitor_debug) {
                                $log->add('Campaign Monitor', 'Campaign Monitor Get List Success..');
                            }
                            update_option('donation_button_campaignmonitor_force_refresh', 'no');
                        } else {
                            unset($campaignmonitor_lists);
                            $campaignmonitor_lists = array();
                            $campaignmonitor_lists['false'] = __("Unable to load Campaign Monitor lists, check your API Key.", 'donation-button');
                            if ('yes' == $campaignmonitor_debug) {
                                $log->add('Campaign Monitor', 'Unable to load Campaign Monitor lists, check your API Key.');
                            }
                        }
                    }
                } else {
                    $campaignmonitor_lists['false'] = __("API Key is empty.", 'donation-button');
                    if ('yes' == $campaignmonitor_debug) {
                        $log->add('Campaign Monitor', 'API Key is empty.');
                    }
                }
            }
            return $campaignmonitor_lists;
        }

        public static function donation_button_campaignmonitor_setting_save_field() {
            $campaignmonitor_setting_fields = self::donation_button_campaignmonitor_setting_fields();
            $Html_output = new Donation_Button_Html_output();
            $Html_output->save_fields($campaignmonitor_setting_fields);
        }

        public static function donation_button_twilio_setting_fields() {
            $fields[] = array('title' => __('Admin Notifications', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'admin_notifications_options');
            $fields[] = array(
                'title' => __('Enable new order SMS admin notifications.', 'donation-button'),
                'id' => 'donation_button_twilio_sms_enable_admin_sms',
                'default' => 'no',
                'type' => 'checkbox'
            );
            $fields[] = array(
                'title' => __('Admin Mobile Number', 'donation-button'),
                'id' => 'donation_button_twilio_sms_admin_sms_recipients',
                'desc' => __('Enter the mobile number (starting with the country code) where the New Order SMS should be sent. Send to multiple recipients by separating numbers with commas.', 'donation-button'),
                'default' => '15451225415',
                'type' => 'text'
            );
            $fields[] = array(
                'title' => __('Admin SMS Message', 'donation-button'),
                'id' => 'donation_button_twilio_sms_admin_sms_template',
                'desc' => __('Use these tags to customize your message: %first_name%, %last_name%, %receiver_email%, %payment_date%, %mc_gross%. Remember that SMS messages are limited to 160 characters.', 'donation-button'),
                'css' => 'min-width:500px;',
                'type' => 'textarea'
            );
            $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
            $fields[] = array('title' => __('Twilio Settings', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'twilio_settings_options');
            $fields[] = array(
                'title' => __('Account SID', 'donation-button'),
                'id' => 'donation_button_twilio_sms_account_sid',
                'desc' => __('Log into your Twilio Account to find your Account SID.', 'donation-button'),
                'type' => 'text',
                'css' => 'min-width:300px;',
            );
            $fields[] = array(
                'title' => __('Auth Token', 'donation-button'),
                'id' => 'donation_button_twilio_sms_auth_token',
                'desc' => __('Log into your Twilio Account to find your Auth Token.', 'donation-button'),
                'type' => 'text',
                'css' => 'min-width:300px;',
            );
            $fields[] = array(
                'title' => __('From Number', 'donation-button'),
                'id' => 'donation_button_twilio_sms_from_number',
                'desc' => __('Enter the number to send SMS messages from. This must be a purchased number from Twilio.', 'donation-button'),
                'type' => 'text',
                'css' => 'min-width:300px;',
            );
            $fields[] = array(
                'desc' => __('Enable this to log Twilio API errors to the log. Use this if you are having issues sending SMS.', 'donation-button'),
                'title' => __('Log Errors', 'donation-button'),
                'id' => 'donation_button_twilio_sms_log_errors',
                'default' => 'no',
                'type' => 'checkbox'
            );
            $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
            $fields[] = array('title' => __('Send Test SMS', 'donation-button'), 'type' => 'title', 'desc' => '', 'id' => 'send_test_sms_options');
            $fields[] = array(
                'title' => __('Mobile Number', 'donation-button'),
                'id' => 'donation_button_twilio_sms_test_mobile_number',
                'name' => 'donation_button_twilio_sms_test_mobile_number',
                'desc' => __('Enter the mobile number (starting with the country code) where the test SMS should be send. Note that if you are using a trial Twilio account, this number must be verified first.', 'donation-button'),
                'type' => 'text',
                'css' => 'min-width:300px;'
            );
            $fields[] = array(
                'title' => __('Message', 'donation-button'),
                'id' => 'donation_button_twilio_sms_test_message',
                'name' => 'donation_button_twilio_sms_test_message',
                'desc' => __('Remember that SMS messages are limited to 160 characters.', 'donation-button'),
                'type' => 'textarea',
                'css' => 'min-width: 500px;'
            );
            $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
            return $fields;
        }

        public static function donation_button_twilio_setting() {
            $sms_setting_fields = self::donation_button_twilio_setting_fields();
            $Html_output = new Donation_Button_Html_output();
            ?>
        <form id="twilio_sms_form" enctype="multipart/form-data" action="" method="post">
        <?php $Html_output->init($sms_setting_fields); ?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc"></th>
                        <td class="forminp">
                            <input type="button" class="button" id="donation_button_twilio_test_sms_button" name="donation_button_twilio_test_sms_button" value="<?php esc_attr_e('Send', 'Option'); ?>"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="twilio_sms" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function donation_button_twilio_setting_save_field() {
        $twilio_sms_setting_fields = self::donation_button_twilio_setting_fields();
        $Html_output = new Donation_Button_Html_output();
        $Html_output->save_fields($twilio_sms_setting_fields);
    }

    public static function donation_button_twilio_send_test_sms() {

        //check_ajax_referer('donation_button_twilio_test_sms_button', 'security');


        $donation_button_twilio_sms_log_errors = get_option('donation_button_twilio_sms_log_errors');
        if (isset($donation_button_twilio_sms_log_errors) && $donation_button_twilio_sms_log_errors == 'yes') {
            $log = new Donation_Button_Logger();
        }

        include_once DBP_PLUGIN_DIR_PATH . '/admin/partials/lib/Twilio.php';
        $AccountSid = get_option("donation_button_twilio_sms_account_sid");
        $AuthToken = get_option("donation_button_twilio_sms_auth_token");
        $from_number = get_option("donation_button_twilio_sms_from_number");
        $test_mobile_number = $_POST['donation_button_twilio_sms_test_mobile_number'];
        $test_message = sanitize_text_field($_POST['donation_button_twilio_sms_test_message']);

        $http = new Services_Twilio_TinyHttp(
                'https://api.twilio.com', array('curlopts' => array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 2,
            ))
        );
        try {
            $client = new Services_Twilio($AccountSid, $AuthToken, "2010-04-01", $http);
            $message = $client->account->messages->create(array(
                "From" => $from_number,
                "To" => $test_mobile_number,
                "Body" => $test_message,
            ));
            $response['success'] = "Successfully Sent message {$message->sid}";
            if ('yes' == get_option('twilio_sms_woo_log_errors')) {
                $log->add('Twilio SMS', 'TEST SMS Sent message ' . $message->sid);
            }
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            if ('yes' == get_option('twilio_sms_woo_log_errors')) {
                $log->add('Twilio SMS', 'TEST SMS Error message ' . $e->getMessage());
            }
        }
        echo json_encode($response);
        die();
    }

}

Donation_Button_General_Setting::init();