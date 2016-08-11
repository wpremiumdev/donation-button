<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Donation_Button
 * @subpackage Donation_Button/public
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->donation_button_add_shortcode();
        add_filter('widget_text', 'do_shortcode');
        add_filter('wp_nav_menu_items', 'do_shortcode');
        add_shortcode('paypal_donation_list', array(__CLASS__, 'donation_button_paypal_donation_list'));
        add_shortcode('donation_goal', array(__CLASS__, 'donation_button_goal_for_wordpress'));
    }

    public function enqueue_styles_datatable() {

        wp_enqueue_style($this->plugin_name . 'public', plugin_dir_url(__FILE__) . 'css/donation-button-public.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'publicDataTablecss', '//cdn.datatables.net/1.10.7/css/jquery.dataTables.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'publicDataTable', '//cdn.datatables.net/responsive/1.0.6/css/dataTables.responsive.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts_for_shortcode_datatable() {

        wp_enqueue_script($this->plugin_name . 'DataTablejs', '//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'DataTable', '//cdn.datatables.net/responsive/1.0.6/js/dataTables.responsive.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'public_datatable', plugin_dir_url(__FILE__) . 'js/donation-button-public-datatable.js', array('jquery'), $this->version, false);
    }

    public function enqueue_styles_goal() {
        wp_enqueue_style($this->plugin_name . 'public', plugin_dir_url(__FILE__) . 'css/donation-button-public.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts_for_shortcode_donation_goal() {
        wp_enqueue_script($this->plugin_name . 'public_goal', plugin_dir_url(__FILE__) . 'js/donation-button-public.js', array('jquery'), $this->version, false);
    }

    public function donation_button_add_shortcode() {
        add_shortcode('paypal_donation_button', array($this, 'donation_button_button_generator'));
    }

    public function donation_button_button_generator($atts) {
        $donation_dropdown_string = '';
        $donation_payment_table_border_td = '';
        $donation_payment_table_border = '';
        $donation_button_align = '';
        $donation_button_amount = '';
        $donation_output_tr_amount = '';

        if (isset($atts['border']) && $atts['border'] != '0') {
            $donation_payment_table_border_td = 'border-top:' . $atts['border'] . 'px solid #ddd';
            $donation_payment_table_border = 'border-bottom:' . $atts['border'] . 'px solid #ddd';
        } else {
            $donation_payment_table_border_td = 'border:medium none';
            $donation_payment_table_border = 'border:medium none';
        }
        ?>
        <style>
            #donation_buttons td{
                <?php echo $donation_payment_table_border_td; ?>;
                background: inherit !important;
            }
            #donation_buttons table{               
                <?php echo $donation_payment_table_border; ?>;
                margin: auto;
                width: auto;
            }
        </style>
        <?php
        if (isset($atts['align']) && !empty($atts['align'])) {
            $donation_button_align = 'align="' . $atts['align'] . '"';
        }

        if (isset($atts) && !empty($atts)) {
            if (is_array($atts)) {
                $donation_dropdown_string = $this->create_dropdown_option_donation_button($atts);
            }
        }

        if (isset($atts['price']) && !empty($atts['price'])) {
            $donation_button_amount = ($atts['price']) ? $atts['price'] : '';
            $donation_button_amount_input = '<input type="hidden" class="set_donation_button_amount" name="amount" value="' . esc_attr($donation_button_amount) . '">';
        } elseif (isset($atts['lable_0']) && !empty($atts['lable_0'])) {
            $donation_output_tr_amount = $this->donation_create_dropdown_option_button_option_code($atts['lable_0'], $atts);
        } else {
            $donation_button_amount = (get_option('donation_button_amount')) ? get_option('donation_button_amount') : '';
            $donation_button_amount_input = '<input type="hidden" class="set_donation_button_amount" name="amount" value="' . esc_attr($donation_button_amount) . '">';
        }


        $donation_button_custom_button = get_option('donation_button_custom_button');
        $donation_button_button_image = get_option('donation_button_button_image');
        $donation_button_reference = get_option('donation_button_reference');
        $donation_button_purpose = get_option('donation_button_purpose');
        $donation_button_notify_url = site_url('?Donation_Button&action=ipn_handler');
        $donation_button_return_page = get_option('donation_button_return_page');
        $donation_button_currency = (get_option('donation_button_currency')) ? get_option('donation_button_currency') : 'USD';
        $donation_button_bussiness_email = get_option('donation_button_bussiness_email');
        $donation_button_PayPal_sandbox = get_option('donation_button_PayPal_sandbox');
        $donation_button_button_label = get_option('donation_button_button_label');

        $donation_paypal_url = $this->get_button_url_donation($donation_button_button_image, $donation_button_custom_button, $donation_button_PayPal_sandbox);
        $button_url = $donation_paypal_url['button_url'];
        $paypal_url = $donation_paypal_url['paypal_url'];


        ob_start();

        $output = '';

        $output = '<div class="page-sidebar widget" id="donation_buttons">';

        $output .= '<form action="' . esc_url($paypal_url) . '" method="post" target="_blank" ' . $donation_button_align . '>';

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
            $output .= $donation_button_amount_input;
        }

        if (isset($donation_button_button_label) && !empty($donation_button_button_label)) {
            $output .= "<table $donation_button_align><tbody><tr><td><label for=\"$donation_button_button_label\">" . $donation_button_button_label . "</label></td></tr></tbody></table>";
        }

        if (isset($donation_dropdown_string) && !empty($donation_dropdown_string)) {
            $output .= '<table ' . $donation_button_align . '><tbody>' . $donation_output_tr_amount . $donation_dropdown_string . '<tr><td><input style="margin-top:10px;" type="image" name="submit" border="0" src="' . esc_url($button_url) . '" alt="PayPal - The safer, easier way to pay online"></td></tr></tbody></table>';
        } else {
            $output .= '<table ' . $donation_button_align . '><tbody>' . $donation_output_tr_amount . '<tr><td><input style="margin-top:10px;" type="image" name="submit" border="0" src="' . esc_url($button_url) . '" alt="PayPal - The safer, easier way to pay online"></td></tr></tbody></table>';
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
        $output .= '</form></div>';

        return $output;
        return ob_get_clean();
    }

    public function create_dropdown_option_donation_button($atts) {

        $result = "";
        $lable_name = isset($atts['lable_name']) ? $atts['lable_name'] : '';
        $donation_lable_name = $this->Donation_Get_Lable_name($lable_name);
        $loop_count = 0;
        if (isset($atts['lable_0']) && !empty($atts['lable_0'])) {
            unset($donation_lable_name[0]);
            $donation_lable_name = array_values($donation_lable_name);
        }
        foreach ($atts as $key => $value) {
            if ("price" != $key && "border" != $key && "lable_name" != $key && "align" != $key && "lable_0" != $key) {
                $result .= $this->donation_array_value_replace_hear($donation_lable_name[$loop_count], $value, $loop_count);
                $loop_count++;
            }
        }
        return $result;
    }

    public function donation_create_dropdown_option_button_option_code($atts, $lable_name) {

        $result = "";
        $lable_name_value = isset($lable_name['lable_name']) ? $lable_name['lable_name'] : '';
        $donation_lable_name = $this->Donation_Get_Lable_name($lable_name_value);
        $currency_selected = (get_option('donation_button_currency')) ? get_option('donation_button_currency') : 'USD';
        $currency_symbol = self::donation_button_get_currency_payment_symbol($currency_selected);

        $result .= $this->donation_array_value_replace_hear_price($donation_lable_name[0], $atts, $currency_symbol, $currency_selected);
        unset($donation_lable_name[0]);
        $donation_lable_name = array_values($donation_lable_name);

        return $result;
    }

    public function donation_array_value_replace_hear_price($lable, $data, $currency_symbol, $currency_selected) {
        $result = "<tr><td><input type='hidden' name='donation_option_price_hidden' value='" . $lable . "'>" . $lable . "</td></tr><tr><td><select name='amount'>";
        $string = "";
        $data = trim($data);
        $data = trim($data);
        $sub_option = explode(' | ', $data);
        foreach ($sub_option as $key => $value) {

            $array_export_data = array();
            $array_export_data = $this->donation_value_expload_with_regex($value);
            $string .= "<option value=\"" . $array_export_data['key'] . "\">" . $array_export_data['value'] . ' - ' . $currency_symbol . $array_export_data['key'] . ' ' . $currency_selected . "</option>";
        }
        $result .= $string . "</select></td></tr>";

        return $result;
    }

    public function Donation_Get_Lable_name($atts) {
        $result = array();
        if (isset($atts) && !empty($atts)) {
            $result = explode(', ', $atts);
        }
        return $result;
    }

    public function donation_array_value_replace_hear($lable, $data, $i) {
        $result = "<tr><td><input type='hidden' name='on" . $i . "' value='" . $lable . "'>" . $lable . "</td></tr><tr><td><select name='os" . $i . "'>";
        $string = "";
        $data = trim($data);
        $data = trim($data);
        $sub_option = explode(' | ', $data);
        foreach ($sub_option as $key => $value) {

            $array_export_data = array();
            $array_export_data = $this->donation_value_expload_with_regex($value);
            $string .= "<option value=\"" . $array_export_data['key'] . "\">" . $array_export_data['value'] . "</option>";
        }
        $result .= $string . "</select></td></tr>";

        return $result;
    }

    public function get_button_url_donation($donation_button_button_image, $donation_button_custom_button, $donation_button_PayPal_sandbox) {
        $result_array = array();
        $button_url = "";
        $paypal_url = "";
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

        if (isset($donation_button_PayPal_sandbox) && $donation_button_PayPal_sandbox == 'yes') {
            $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
        }

        $result_array['button_url'] = $button_url;
        $result_array['paypal_url'] = $paypal_url;

        return $result_array;
    }

    public function donation_value_expload_with_regex($value) {
        $result_array = array();

        $value_regex = "/value=('|\")+[^*]+(price=)/";
        $price_regex = "/price=('|\")+[^*]+/";
        $value_name = preg_match($value_regex, $value, $matches_out_value);
        $price_name = preg_match($price_regex, $value, $matches_out_price);
        $matches_out_value[0] = str_replace(" price=", "", $matches_out_value[0]);
        $result_array['value'] = trim(str_replace("value='", "", $matches_out_value[0]), "'");
        $result_array['key'] = trim(str_replace("price='", "", $matches_out_price[0]), "'");

        return $result_array;
    }

    public static function donation_button_paypal_donation_list($atts) {
        extract(shortcode_atts(array(
            'txn_type' => 'any',
            'payment_status' => '',
            'limit' => 10,
            'field1' => 'txn_id',
            'field2' => 'payment_date',
            'field3' => 'first_name',
            'field4' => 'last_name',
            'field5' => 'mc_gross',
                        ), $atts));

        ob_start();
        $args = array(
            'post_type' => 'donation_list',
            'posts_per_page' => $limit,
        );
        if (isset($atts) && !empty($atts)) {
            $start_loop = 1;
            $field_key_header = array();
            $field_key = array();
            foreach ($atts as $atts_key => $atts_value) {
                if (array_key_exists('field' . $start_loop, $atts)) {
                    $field_key_header['field' . $start_loop] = ucwords(str_replace('_', ' ', $atts['field' . $start_loop]));
                    $field_key['field' . $start_loop] = $atts['field' . $start_loop];
                }
                $start_loop = $start_loop + 1;
            }
        } else {
            $atts = array('field1' => 'txn_id',
                'field2' => 'payment_date',
                'field3' => 'first_name',
                'field4' => 'last_name',
                'field5' => 'mc_gross');
            $start_loop = 1;
            $field_key_header = array();
            $field_key = array();
            foreach ($atts as $atts_key => $atts_value) {
                if (array_key_exists('field' . $start_loop, $atts)) {
                    $field_key_header['field' . $start_loop] = ucwords(str_replace('_', ' ', $atts['field' . $start_loop]));
                    $field_key['field' . $start_loop] = $atts['field' . $start_loop];
                }
                $start_loop = $start_loop + 1;
            }
        }

        $posts = get_posts($args);
        if ($posts) {
            $mainhtml = '';
            $output = '';
            $output .= '<table id="example" class="display" cellspacing="0" width="100%"><thead>';

            $thead = "<tr>";

            if (!empty($field_key_header)) {
                foreach ($field_key_header as $field_key_header_key => $field_key_header_value) {
                    $thead .= "<th>" . $field_key_header_value . "</th>";
                }
            }
            $thead .= "</tr>";
            $thead_end = '</thead>';
            $tfoot_start = "<tfoot>";
            $tfoot_end = "</tfoot>";
            $mainhtml .= $output . $thead . $thead_end . $tfoot_start . $thead . $tfoot_end;
            $tbody_start = "<tbody>";
            $tbody = "";
            foreach ($posts as $post):
                $tbody .= "<tr>";

                if (isset($field_key) && !empty($field_key)) {
                    foreach ($field_key as $field_key_key => $field_key_value) {
                        $tbody .= "<td>" . get_post_meta($post->ID, $field_key_value, true) . "</td>";
                    }
                }
                $tbody .= "</tr>";
            endforeach;

            $tbody_end = "</tbody>";
            $mainhtml .= $tbody_start . $tbody . $tbody_end;
            $mainhtml .= "</table>";
            return $mainhtml;
            return ob_get_clean();
        } else {
            $mainhtml = "no records found";
            return ob_get_clean();
        }
    }

    public static function donation_button_goal_for_wordpress($atts) {

        if (isset($atts['id']) && !empty($atts['id'])) {

            $get_donation_button_post_meta_short_code = get_post_meta($atts['id'], 'donation_button_detail');
            $get_donation_button_post_meta_short_code_array = $get_donation_button_post_meta_short_code[0];

            $start_date = date("Y-m-d", strtotime($get_donation_button_post_meta_short_code_array['donation_button_start_date']));
            $end_date = date("Y-m-d", strtotime($get_donation_button_post_meta_short_code_array['donation_button_end_date']));
            $donation_button_complete_target = '0';

            $display_donation_button_goal_detail = FALSE;
            $display_donation_button_goal_target_amount = FALSE;
            $display_donation_button_goal_start_date = FALSE;
            $display_donation_button_goal_end_date = FALSE;
            $display_paypal_donation_button = FALSE;

            if (isset($get_donation_button_post_meta_short_code_array['chk_donation_goal'])) {
                foreach ($get_donation_button_post_meta_short_code_array['chk_donation_goal'] as $value) {
                    if ($value == 'chk_donation_goal_detail') {
                        $display_donation_button_goal_detail = TRUE;
                    } elseif ($value == 'chk_donation_target_amount') {
                        $display_donation_button_goal_target_amount = TRUE;
                    } elseif ($value == 'chk_donation_goal_start_date') {
                        $display_donation_button_goal_start_date = TRUE;
                    } elseif ($value == 'chk_donation_goal_end_date') {
                        $display_donation_button_goal_end_date = TRUE;
                    } elseif ($value == 'chk_donation_goal_display_paypal_donation_button') {
                        $display_paypal_donation_button = TRUE;
                    }
                }
            }

            global $wpdb;
            $meta_key = 'mc_gross';
            $donation_button_result_array = $wpdb->get_var($wpdb->prepare("
		SELECT sum(meta_value) 
		FROM $wpdb->postmeta,$wpdb->posts 
		WHERE meta_key = %s
                AND $wpdb->posts.ID = $wpdb->postmeta.post_id 
                AND $wpdb->postmeta.meta_key = 'mc_gross'            
                AND $wpdb->posts.post_status = 'publish' 
                AND $wpdb->posts.post_type = 'donation_list'
                AND (date($wpdb->posts.post_date) BETWEEN %s AND %s)
            ", $meta_key, $start_date, $end_date));

            if (isset($donation_button_result_array) && !empty($donation_button_result_array)) {

                $donation_button_target_amount = $get_donation_button_post_meta_short_code_array['donation_button_target_amount'];
                $donation_complete_amount = round($donation_button_result_array);
                $donation_button_complete_target = round(( 100 * $donation_complete_amount ) / $donation_button_target_amount);
                if ($donation_button_complete_target > 100) {
                    $donation_button_complete_target = 100;
                }
            }
            ?> 

            <table class="widefat donation_button_table_backgroud_color" cellspacing="0" >                  
                <input type="text" class="donation_button_progress_background_color" id="donation_button_progress_background_color" value="<?php echo $get_donation_button_post_meta_short_code_array['donation_button_progress_background']; ?>" hidden>
                <input type="text" class="donation_button_bar_percentage_background_color" id="donation_button_bar_percentage_background_color" value="<?php echo $get_donation_button_post_meta_short_code_array['donation_button_bar_percentage_background']; ?>" hidden>
                <input type="text" class="donation_button_bar_background_color" id="donation_button_bar_background_color" value="<?php echo $get_donation_button_post_meta_short_code_array['donation_button_bar_background']; ?>" hidden>
                <input type="text" class="donation_button_bar_and_font_color" id="donation_button_bar_and_font_color" value="<?php echo $get_donation_button_post_meta_short_code_array['donation_button_bar_and_font']; ?>" hidden>
                <input type="text" class="donation_button_preview_table_color_color" id="donation_button_preview_table_color_color" value="<?php echo $get_donation_button_post_meta_short_code_array['donation_button_preview_table_color']; ?>" hidden>

                <tbody class="donation_button_table_tbody_backgroud_color">                    
                    <?php if ($display_donation_button_goal_detail) { ?>
                        <tr>
                            <th><?php _e('Donation Goal Detail', 'donation-button'); ?></th>
                            <td>
                                <label class="label_donation_goal_detail lbl" ><?php echo (isset($get_donation_button_post_meta_short_code_array['donation_button_goal_detail'])) ? $get_donation_button_post_meta_short_code_array['donation_button_goal_detail'] : ''; ?></label>                        
                            </td>
                        </tr>                   
                    <?php } if ($display_donation_button_goal_target_amount) { ?>
                        <tr>
                            <th><?php _e('Donation Target Amount', 'donation-button'); ?></th>
                            <td>
                                <label class="label_donation_goal_target_amount_lbl" ><?php echo (isset($get_donation_button_post_meta_short_code_array['donation_button_target_amount'])) ? $get_donation_button_post_meta_short_code_array['donation_button_target_amount'] : ''; ?></label>                        
                            </td>
                        </tr>                   
                    <?php } if ($display_donation_button_goal_start_date) { ?>
                        <tr>
                            <th><?php _e('Start Date', 'donation-button'); ?></th>
                            <td>
                                <label class="label_donation_goal_start_date lbl" ><?php echo (isset($get_donation_button_post_meta_short_code_array['donation_button_start_date'])) ? $get_donation_button_post_meta_short_code_array['donation_button_start_date'] : ''; ?></label>                        
                            </td>
                        </tr>
                    <?php } if ($display_donation_button_goal_end_date) { ?>
                        <tr>
                            <th><?php _e('End Date', 'donation-button'); ?></th>
                            <td>                            
                                <label class="label_donation_goal_end_date lbl" ><?php echo (isset($get_donation_button_post_meta_short_code_array['donation_button_end_date'])) ? $get_donation_button_post_meta_short_code_array['donation_button_end_date'] : ''; ?></label>                        
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2">
                            <div id="donation_button_container">  
                                <div class="donation-button-bar-main-container donation-button-background-color">
                                    <div class="wrap">
                                        <div class="donation-button-bar-percentage" data-percentage="<?php echo isset($donation_button_complete_target) ? $donation_button_complete_target : '0'; ?>"></div>
                                        <div class="donation-button-bar-container">
                                            <div class="donation-button-bar"></div>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </td>
                    </tr>
                    <?php if ($display_paypal_donation_button) { ?>
                        <tr>                        
                            <td>                            
                                <label class="label_donation_goal_display_donation_button lbl" ><?php echo do_shortcode('[paypal_donation_button]'); ?></label>                        
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        }
    }

    public static function donation_button_get_currency_payment_symbol($currency) {

        $currency_symbol = '';

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
        return $currency_symbol;
    }

    public function paypal_donation_load_shortcode_asset($posts) {
        if (empty($posts)) {
            return $posts;
        }

        $found = false;
        $result_shortcode = array();
        foreach ($posts as $post) {


            if (strpos($post->post_content, '[donation_goal id=') !== false || strpos($post->post_content, '[donation_goal id=') !== false) {
                $result_shortcode['goal'] = true;
            }

            if (strpos($post->post_content, '[paypal_donation_list') !== false || strpos($post->post_content, '[paypal_donation_list') !== false) {
                $result_shortcode['list'] = true;
            }
        }

        if ((isset($result_shortcode['goal']) && !empty($result_shortcode['goal'])) && (isset($result_shortcode['list']) && !empty($result_shortcode['list']))) {

            $this->enqueue_styles_datatable();
            $this->enqueue_scripts_for_shortcode_donation_goal();
            $this->enqueue_scripts_for_shortcode_datatable();
        } elseif (isset($result_shortcode['goal']) && !empty($result_shortcode['goal'])) {

            $this->enqueue_styles_goal();
            $this->enqueue_scripts_for_shortcode_donation_goal();
        } elseif (isset($result_shortcode['list']) && !empty($result_shortcode['list'])) {

            $this->enqueue_styles_datatable();
            $this->enqueue_scripts_for_shortcode_datatable();
        }

        return $posts;
    }

}