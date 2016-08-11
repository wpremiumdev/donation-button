<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Donation_Button
 * @subpackage Donation_Button/includes
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Donation_Button_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    public function __construct() {
        $this->plugin_name = 'donation-button';
        $this->version = '1.4.9';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        add_action('init', array($this, 'add_endpoint'), 0);
        add_action('parse_request', array($this, 'handle_api_requests'), 0);
        add_action('donation_button_send_notification_mail', array($this, 'donation_button_send_notification_mail'), 10, 1);
        add_action('donation_button_api_ipn_handler', array($this, 'donation_button_api_ipn_handler'));
        $prefix = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_" . DBP_PLUGIN_BASENAME, array($this, 'plugin_action_links'), 10, 4);
        add_filter('widget_text', 'do_shortcode');
    }

    public function plugin_action_links($actions, $plugin_file, $plugin_data, $context) {
        $custom_actions = array(
            'configure' => sprintf('<a href="%s">%s</a>', admin_url('options-general.php?page=donation-button'), __('Configure', 'donation-button')),
            'support' => sprintf('<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/plugin/donation-button/', __('Support', 'donation-button')),
            'review' => sprintf('<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/view/plugin-reviews/donation-button', __('Write a Review', 'donation-button')),
        );

        return array_merge($custom_actions, $actions);
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-donation-button-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-donation-button-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-donation-button-list.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-logger.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-mailchimp-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-getrsponse-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-icontact-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-infusionsoft-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-constant-contact-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-campaign-monitor-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-twilio-sms-helper.php';
        $this->loader = new Donation_Button_Loader();
    }

    private function set_locale() {

        $plugin_i18n = new Donation_Button_i18n();
        $plugin_i18n->set_domain($this->get_plugin_name());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks() {
        $plugin_admin = new Donation_Button_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_filter('woocommerce_paypal_args', $plugin_admin, 'paypal_donation_button_woocommerce_standard_parameters', 99, 1);
        $this->loader->add_action('init', $plugin_admin, 'donation_goal_custom_post_create');
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_donation_goal_in_meta_boxes_detail', 10);
        $this->loader->add_action('admin_notices', $plugin_admin, 'goal_require_field_empty_notice_error');
        $this->loader->add_action('save_post', $plugin_admin, 'save_post_donation_details', 10, 3);
        $this->loader->add_filter('manage_edit-donationgoal_columns', $plugin_admin, 'set_custom_edit_donation_goal_columns');
        $this->loader->add_action('manage_donationgoal_posts_custom_column', $plugin_admin, 'custom_donationgoal_columns', 10, 2);
        $this->loader->add_filter('manage_edit-donationgoal_sortable_columns', $plugin_admin, 'bs_donation_goal_table_sorting');
        $this->loader->add_action('media_buttons', $plugin_admin, 'donation_add_my_media_button');
    }

    private function define_public_hooks() {
        $plugin_public = new Donation_Button_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('the_posts', $plugin_public, 'paypal_donation_load_shortcode_asset');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

    public function handle_api_requests() {
        global $wp;
        if (isset($_GET['action']) && $_GET['action'] == 'ipn_handler') {
            $wp->query_vars['Donation_Button'] = $_GET['action'];
        }
        if (!empty($wp->query_vars['Donation_Button'])) {
            ob_start();
            $api = strtolower(esc_attr($wp->query_vars['Donation_Button']));
            do_action('donation_button_api_' . $api);
            ob_end_clean();
            die('1');
        }
    }

    public function add_endpoint() {
        add_rewrite_endpoint('Donation_Button', EP_ALL);
    }

    public function donation_button_api_ipn_handler() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-donation-button-paypal-listner.php';
        $Donation_Button_PayPal_listner = new Donation_Button_PayPal_listner();
        if ($Donation_Button_PayPal_listner->check_ipn_request()) {
            $Donation_Button_PayPal_listner->successful_request($IPN_status = true);
        } else {
            $Donation_Button_PayPal_listner->successful_request($IPN_status = false);
        }
    }

    public function set_html_content_type() {
        return 'text/html';
    }

    public function donation_button_send_notification_mail($posted) {

        $template = get_option('donation_buttons_email_body_text');
        $template_value = isset($template) ? $template : get_option('donation_buttons_email_body_text_pre');
        $parse_templated = $this->donation_button_template_vars_replacement($template_value, $posted);
        $from_name = get_option('donation_buttons_email_from_name');
        $from_name_value = isset($from_name) ? $from_name : 'From';
        $sender_address = get_option('donation_buttons_email_from_address');
        $sender_address_value = isset($sender_address) ? $sender_address : get_option('admin_email');
        if (isset($from_name_value) && !empty($from_name_value)) {
            $headers = "From: " . $from_name_value . " <" . $sender_address_value . ">";
        }
        if (isset($posted['payer_email']) && !empty($posted['payer_email'])) {
            $subject = get_option('donation_buttons_email_subject');
            $subject_value = isset($subject) ? $subject : 'Thank you for your donation';
            $enable_admin = get_option('donation_buttons_admin_notification');
            $admin_email_array = $this->donation_button_get_admin_user();
            if (isset($headers) && !empty($headers)) {
                wp_mail($posted['payer_email'], $subject_value, $parse_templated, $headers);
                if ($enable_admin) {
                    $this->donation_button_send_admin_notification_mail($admin_email_array, $subject_value, $parse_templated, $headers);
                    //wp_mail($admin_email, $subject_value, $parse_templated, $headers);
                }
            } else {
                wp_mail($posted['payer_email'], $subject_value, $parse_templated);
                if ($enable_admin) {
                    $this->donation_button_send_admin_notification_mail($admin_email_array, $subject_value, $parse_templated, '');
                    //wp_mail($admin_email, $subject_value, $parse_templated);
                }
            }
        }
    }

    public function donation_button_get_admin_user() {
        
        $result = array();
        $blogusers = get_users('role=Administrator');
        foreach ($blogusers as $user) {
            $result[] = $user->user_email;
        }
        return $result;        
    }

    public function donation_button_send_admin_notification_mail($admin_email_array, $subject_value, $parse_templated, $headers) {        
        $admin_email_array = array_unique($admin_email_array);
        if (!empty($admin_email_array)) {           
            foreach ($admin_email_array as $key => $admin_email) {
                try {                   
                    wp_mail($admin_email, $subject_value, $parse_templated, $headers);
                } catch (Exception $e) {
                    
                }
            }
        }
    }

    public function donation_button_template_vars_replacement($template, $posted) {
        $to_replace = array(
            'blog_url' => get_option('siteurl'),
            'home_url' => get_option('home'),
            'blog_name' => get_option('blogname'),
            'blog_description' => get_option('blogdescription'),
            'admin_email' => get_option('admin_email'),
            'date' => date_i18n(get_option('date_format')),
            'time' => date_i18n(get_option('time_format')),
            'txn_id' => $posted['txn_id'],
            'receiver_email' => $posted['receiver_email'],
            'payment_date' => $posted['payment_date'],
            'first_name' => $posted['first_name'],
            'last_name' => $posted['last_name'],
            'mc_currency' => $posted['mc_currency'],
            'mc_gross' => $posted['mc_gross']
        );
        foreach ($to_replace as $tag => $var) {

            $template = str_replace('%' . $tag . '%', $var, $template);
        }
        return $template;
    }

}