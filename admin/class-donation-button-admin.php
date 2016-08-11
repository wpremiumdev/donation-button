<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Donation_Button
 * @subpackage Donation_Button/admin
 * @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->load_dependencies();
        $this->define_constants();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-donation-button-admin-display.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-donation-button-general-setting.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-donation-button-html-output.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-donation-button-admin-widget.php';
    }

    private function define_constants() {
        if (!defined('DBP_FOR_WORDPRESS_LOG_DIR')) {
            define('DBP_FOR_WORDPRESS_LOG_DIR', ABSPATH . 'donation-button-logs/');
        }
    }

    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function

         * in that particular class.
         *

         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/donation-button-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function

         * in that particular class.
         *

         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/donation-button-admin.js', array('jquery', 'wp-color-picker'), $this->version, false);
        if (wp_script_is($this->plugin_name)) {
            wp_localize_script($this->plugin_name, 'donation_button_twilio_test_sms_button_params', apply_filters('donation_button_twilio_test_sms_button_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
            )));
        }
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('wp-color-picker');
    }

    public function paypal_donation_button_woocommerce_standard_parameters($paypal_args) {
        if (isset($paypal_args['BUTTONSOURCE'])) {
            $paypal_args['BUTTONSOURCE'] = 'mbjtechnolabs_SP';
        } else {
            $paypal_args['bn'] = 'mbjtechnolabs_SP';
        }
        return $paypal_args;
    }

    public function donation_goal_custom_post_create() {

        $labels = array(
            'name' => _x('Goals', 'post type general name', 'donation-button'),
            'singular_name' => _x('Goal', 'post type singular name', 'donation-button'),
            'menu_name' => _x('Goal', 'admin menu', 'donation-button'),
            'name_admin_bar' => _x('Goals', 'add new on admin bar', 'donation-button'),
            'add_new' => _x('Add New Goal', 'Goal', 'donation-button'),
            'add_new_item' => __('Add New Goal', 'donation-button'),
            'new_item' => __('New Goal', 'donation-button'),
            'edit_item' => __('Edit Goal', 'donation-button'),
            'view_item' => __('View Goal', 'donation-button'),
            'all_items' => __('All Goals', 'donation-button'),
            'search_items' => __('Search Goals', 'donation-button'),
            'parent_item_colon' => __('Parent Goals:', 'donation-button'),
            'not_found' => __('No Goals found.', 'donation-button'),
            'not_found_in_trash' => __('No Goals found in Trash.', 'donation-button')
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'map_meta_cap' => true,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => false,
            'rewrite' => array('slug' => 'donationgoal'),
            'capability_type' => 'post',
            'hierarchical' => false,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'supports' => array('title')
        );
        register_post_type('donationgoal', $args);
    }

    public function add_donation_goal_in_meta_boxes_detail() {

        add_meta_box('donationgoal-id', __('Goal Details'), array(__CLASS__, 'donation_goal_detail_manager_metabox'), 'donationgoal', 'normal', 'high');
    }

    public static function donation_goal_detail_manager_metabox() {

        $get_donation_button_post_meta = get_post_meta(get_the_ID(), 'donation_button_detail');
        $get_donation_button_post_meta_array = array();
        if (is_array($get_donation_button_post_meta) && count($get_donation_button_post_meta) > 0) {
            $get_donation_button_post_meta_array = $get_donation_button_post_meta[0];
        }

        $donation_button_start_date = isset($get_donation_button_post_meta_array['donation_button_start_date']) ? $get_donation_button_post_meta_array['donation_button_start_date'] : date("Y-m-d");
        $donation_button_end_date = isset($get_donation_button_post_meta_array['donation_button_end_date']) ? $get_donation_button_post_meta_array['donation_button_end_date'] : date("Y-m-d");

        $start_date = date("Y-m-d", strtotime($donation_button_start_date));
        $end_date = date("Y-m-d", strtotime($donation_button_end_date));
        $donation_button_complete_target = '0';

        $donation_button_target_amount = isset($get_donation_button_post_meta_array['donation_button_target_amount']) ? $get_donation_button_post_meta_array['donation_button_target_amount'] : 0;

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

        if ((isset($donation_button_result_array) && !empty($donation_button_result_array)) && (isset($donation_button_target_amount) && !empty($donation_button_target_amount))) {

            $donation_complete_amount = round($donation_button_result_array);

            $donation_button_complete_target = round(( 100 * $donation_complete_amount ) / $donation_button_target_amount);
            if ($donation_button_complete_target > 100) {
                $donation_button_complete_target = 100;
            }
        }
        ?>        
        <div class='wrap donation_button_div_table'>            
            <table class="widefat" cellspacing="0" >
                <thead>
                    <tr>
                        <th colspan="2"><?php _e('Goal Detail', 'donation-button'); ?></th>
                    </tr>
                </thead>
                <tbody>                    
                    <tr>
                        <th><?php _e('Donation Goal Detail', 'donation-button'); ?></th>
                        <td>
                            <textarea name="donation_button_goal_detail" class="donation_button_goal_detail"><?php echo (isset($get_donation_button_post_meta_array['donation_button_goal_detail'])) ? $get_donation_button_post_meta_array['donation_button_goal_detail'] : ''; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Donation Target Amount', 'donation-button'); ?></th>
                        <td>
                            <input type="text" id="donation_button_target_amount" class="donation_button_target_amount" name="donation_button_target_amount" value="<?php echo (isset($get_donation_button_post_meta_array['donation_button_target_amount'])) ? $get_donation_button_post_meta_array['donation_button_target_amount'] : ''; ?>">
                        </td>                        
                    </tr>
                    <tr>
                        <th><?php _e('Start Date', 'donation-button'); ?></th>
                        <td>
                            <input type="text" id="donation_button_start_date" class="donation_button_date" name="donation_button_start_date" value="<?php echo (isset($get_donation_button_post_meta_array['donation_button_start_date'])) ? $get_donation_button_post_meta_array['donation_button_start_date'] : ''; ?>" readonly>
                        </td>                        
                    </tr>
                    <tr>
                        <th><?php _e('End Date', 'donation-button'); ?></th>
                        <td>                            
                            <input type="text" id="donation_button_end_date" class="donation_button_date" name="donation_button_end_date" value="<?php echo (isset($get_donation_button_post_meta_array['donation_button_end_date'])) ? $get_donation_button_post_meta_array['donation_button_end_date'] : ''; ?>" readonly/>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Change Progress Background Color', 'donation-button'); ?></th>
                        <td>                            
                            <input type="text" id="donation_button_progress_background" class="donation_button_progress_background donation_background_color_change" name="donation_button_progress_background" value="<?php echo (isset($get_donation_button_post_meta_array['donation_button_progress_background'])) ? $get_donation_button_post_meta_array['donation_button_progress_background'] : '#eaeaea'; ?>"/>
                            <img class="donation_button_pbg_reload_color" src="<?php echo plugin_dir_url(__FILE__) . 'images/reload.png'; ?>" alt="<?php _e('Change The Progress Backgroud', 'donation-button'); ?>">

                        </td>                         
                    </tr>
                    <tr>
                        <th><?php _e('Change Percentage Background Color', 'donation-button'); ?></th>
                        <td>                            
                            <input type="text" id="donation_button_bar_percentage_background" class="donation_button_bar_percentage_background donation_background_color_change" name="donation_button_bar_percentage_background" value="<?php echo (isset($get_donation_button_post_meta_array['donation_button_bar_percentage_background'])) ? $get_donation_button_post_meta_array['donation_button_bar_percentage_background'] : '#BEC7D3'; ?>"/>
                            <img class="donation_button_bpg_reload_color" src="<?php echo plugin_dir_url(__FILE__) . 'images/reload.png'; ?>" alt="<?php _e('Change The Percentage Background', 'donation-button'); ?>">
                        </td>                         
                    </tr>
                    <tr>
                        <th><?php _e('Change Bar Color', 'donation-button'); ?></th>
                        <td>                            
                            <input type="text" id="donation_button_bar_background" class="donation_button_bar_background donation_background_color_change" name="donation_button_bar_background" value="<?php echo (isset($get_donation_button_post_meta_array['donation_button_bar_background'])) ? $get_donation_button_post_meta_array['donation_button_bar_background'] : '#666666'; ?>"/>
                            <img class="donation_button_bb_reload_color" src="<?php echo plugin_dir_url(__FILE__) . 'images/reload.png'; ?>" alt="<?php _e('Change The Progress Bar Background', 'donation-button'); ?>">
                        </td>                         
                    </tr>
                    <tr>
                        <th><?php _e('Change Percentage Font Color', 'donation-button'); ?></th>
                        <td>                            
                            <input type="text" id="donation_button_bar_and_font" class="donation_button_bar_and_font donation_background_color_change" name="donation_button_bar_and_font" value="<?php echo (isset($get_donation_button_post_meta_array['donation_button_bar_and_font'])) ? $get_donation_button_post_meta_array['donation_button_bar_and_font'] : '#000000'; ?>"/>
                            <img class="donation_button_bf_reload_color" src="<?php echo plugin_dir_url(__FILE__) . 'images/reload.png'; ?>" alt="<?php _e('Change The Progress Bar And Font Color', 'donation-button'); ?>">
                        </td>                         
                    </tr>                    
                    <tr>
                        <th><?php _e('Font Color Goal Preview', 'donation-button'); ?></th>
                        <td>                            
                            <input type="text" id="donation_button_preview_table_color" class="donation_button_preview_table_color donation_background_color_change" name="donation_button_preview_table_color" value="<?php echo (isset($get_donation_button_post_meta_array['donation_button_preview_table_color'])) ? $get_donation_button_post_meta_array['donation_button_preview_table_color'] : '#000000'; ?>"/>
                            <img class="donation_button_pt_color" src="<?php echo plugin_dir_url(__FILE__) . 'images/reload.png'; ?>" alt="<?php _e('Change The Progress Bar table Background', 'donation-button'); ?>">
                        </td>                         
                    </tr>
                </tbody>
            </table><br />
            <table class="widefat donation_button_table_hide_show" cellspacing="0" >
                <thead>
                    <tr>
                        <th colspan="2"><?php _e('Hide/Show', 'donation-button'); ?></th>
                    </tr>
                </thead>
                <tbody class="donation_button_table_tbody_hide_show">                   
                    <tr>
                        <th><?php _e('Donation Goal Detail', 'donation-button'); ?></th>
                        <td>                       
                            <?php
                            $chk_donation_goal_detail_checked = '';
                            $chk_donation_target_amount_checked = '';
                            $chk_donation_goal_start_date_checked = '';
                            $chk_donation_goal_end_date_checked = '';
                            $chk_donation_goal_display_paypal_donation_button_checked = '';

                            if (isset($get_donation_button_post_meta_array['chk_donation_goal'])) {
                                foreach ($get_donation_button_post_meta_array['chk_donation_goal'] as $value) {
                                    if ($value == 'chk_donation_goal_detail') {
                                        $chk_donation_goal_detail_checked = 'checked';
                                    } elseif ($value == 'chk_donation_target_amount') {
                                        $chk_donation_target_amount_checked = 'checked';
                                    } elseif ($value == 'chk_donation_goal_start_date') {
                                        $chk_donation_goal_start_date_checked = 'checked';
                                    } elseif ($value == 'chk_donation_goal_end_date') {
                                        $chk_donation_goal_end_date_checked = 'checked';
                                    } elseif ($value == 'chk_donation_goal_display_paypal_donation_button') {
                                        $chk_donation_goal_display_paypal_donation_button_checked = 'checked';
                                    }
                                }
                            }
                            ?>
                            <input type="checkbox" id="chk_donation_goal_detail_click" name="chk_donation_goal[]" value="chk_donation_goal_detail" <?php echo $chk_donation_goal_detail_checked; ?>/>                 
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Donation Target Amount', 'donation-button'); ?></th>
                        <td>
                            <input type="checkbox" id="chk_donation_target_amount_click" name="chk_donation_goal[]" value="chk_donation_target_amount" <?php echo $chk_donation_target_amount_checked; ?>/>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Start Date', 'donation-button'); ?></th>
                        <td>
                            <input type="checkbox" id="chk_donation_goal_start_date_click" name="chk_donation_goal[]" value="chk_donation_goal_start_date" <?php echo $chk_donation_goal_start_date_checked; ?>/>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('End Date', 'donation-button'); ?></th>
                        <td>
                            <input type="checkbox" id="chk_donation_goal_end_date_click" name="chk_donation_goal[]" value="chk_donation_goal_end_date" <?php echo $chk_donation_goal_end_date_checked; ?>/>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Display Paypal Donation Button', 'donation-button'); ?></th>
                        <td>
                            <input type="checkbox" id="chk_donation_goal_display_paypal_donation_button_click" name="chk_donation_goal[]" value="chk_donation_goal_display_paypal_donation_button" <?php echo $chk_donation_goal_display_paypal_donation_button_checked; ?>/>
                        </td>
                    </tr>
                </tbody>
            </table><br />
            <table class="widefat donation_button_table_backgroud_color" cellspacing="0" >
                <thead>
                    <tr>
                        <th colspan="2"><?php _e('Goal Preview', 'donation-button'); ?></th>
                    </tr>
                </thead>
                <tbody class="donation_button_table_tbody_backgroud_color">                    
                    <tr id="label_donation_goal_detail_tr">
                        <th><?php _e('Donation Goal Detail', 'donation-button'); ?></th>
                        <td>
                            <label class="label_donation_goal_detail lbl" ><?php echo (isset($get_donation_button_post_meta_array['donation_button_goal_detail'])) ? $get_donation_button_post_meta_array['donation_button_goal_detail'] : ''; ?></label>                        
                        </td>
                    </tr>
                    <tr id="label_donation_goal_target_amount_lbl_tr">
                        <th><?php _e('Donation Target Amount', 'donation-button'); ?></th>
                        <td>
                            <label class="label_donation_goal_target_amount_lbl" ><?php echo (isset($get_donation_button_post_meta_array['donation_button_target_amount'])) ? $get_donation_button_post_meta_array['donation_button_target_amount'] : ''; ?></label>                        
                        </td>
                    </tr>
                    <tr id="label_donation_goal_start_date_tr">
                        <th><?php _e('Start Date', 'donation-button'); ?></th>
                        <td>
                            <label class="label_donation_goal_start_date lbl" ><?php echo (isset($get_donation_button_post_meta_array['donation_button_start_date'])) ? $get_donation_button_post_meta_array['donation_button_start_date'] : ''; ?></label>                        
                        </td>
                    </tr>
                    <tr id="label_donation_goal_end_date_tr">
                        <th><?php _e('End Date', 'donation-button'); ?></th>
                        <td>                            
                            <label class="label_donation_goal_end_date lbl" ><?php echo (isset($get_donation_button_post_meta_array['donation_button_end_date'])) ? $get_donation_button_post_meta_array['donation_button_end_date'] : ''; ?></label>                        
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="donation_button_container">  
                                <div class="donation-button-bar-main-container donation-button-background-color">
                                    <div class="wrap">
                                        <div class="donation-button-bar-percentage" data-percentage="<?php echo $donation_button_complete_target; ?>"></div>
                                        <div class="donation-button-bar-container">
                                            <div class="donation-button-bar"></div>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </td>
                    </tr>
                    <tr id="label_donation_goal_display_paypal_donation_button_tr">                        
                        <td>                            
                            <label class="label_donation_goal_display_paypal_donation_button lbl" ><?php echo do_shortcode('[paypal_donation_button]'); ?> </label>                        
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php
    }

    public function save_post_donation_details($postID, $post, $update) {

        $slug = 'donationgoal';
        if ($slug != $post->post_type) {
            return;
        }
        if (isset($_POST) && empty($_POST)) {
            return;
        }
        $donation_button_array_merge = array();
        $donation_button_array_merge_final = array();
        $get_post_request_data_donation_button = $_REQUEST;

        //  if (isset($get_post_request_data_donation_button['donation_button_goal_detail']) && !empty($get_post_request_data_donation_button['donation_button_goal_detail'])) {

        $t_amount = (isset($get_post_request_data_donation_button['donation_button_target_amount'])) ? $get_post_request_data_donation_button['donation_button_target_amount'] : '';
        $s_date = (isset($get_post_request_data_donation_button['donation_button_start_date'])) ? $get_post_request_data_donation_button['donation_button_start_date'] : '';
        $e_date = (isset($get_post_request_data_donation_button['donation_button_end_date'])) ? $get_post_request_data_donation_button['donation_button_end_date'] : '';

        if ((isset($t_amount) && !empty($t_amount)) && (isset($s_date) && !empty($s_date)) && (isset($e_date) && !empty($e_date))) {
            $donation_button_array_field = array(
                'donation_button_goal_detail' => (isset($get_post_request_data_donation_button['donation_button_goal_detail'])) ? $get_post_request_data_donation_button['donation_button_goal_detail'] : '',
                'donation_button_target_amount' => (isset($get_post_request_data_donation_button['donation_button_target_amount'])) ? $get_post_request_data_donation_button['donation_button_target_amount'] : '',
                'donation_button_start_date' => (isset($get_post_request_data_donation_button['donation_button_start_date'])) ? $get_post_request_data_donation_button['donation_button_start_date'] : '',
                'donation_button_end_date' => (isset($get_post_request_data_donation_button['donation_button_end_date'])) ? $get_post_request_data_donation_button['donation_button_end_date'] : '',
                'donation_button_progress_background' => (isset($get_post_request_data_donation_button['donation_button_progress_background'])) ? $get_post_request_data_donation_button['donation_button_progress_background'] : '',
                'donation_button_bar_percentage_background' => (isset($get_post_request_data_donation_button['donation_button_bar_percentage_background'])) ? $get_post_request_data_donation_button['donation_button_bar_percentage_background'] : '',
                'donation_button_bar_background' => (isset($get_post_request_data_donation_button['donation_button_bar_background'])) ? $get_post_request_data_donation_button['donation_button_bar_background'] : '',
                'donation_button_bar_and_font' => (isset($get_post_request_data_donation_button['donation_button_bar_and_font'])) ? $get_post_request_data_donation_button['donation_button_bar_and_font'] : '',
                'donation_button_preview_table_color' => (isset($get_post_request_data_donation_button['donation_button_preview_table_color'])) ? $get_post_request_data_donation_button['donation_button_preview_table_color'] : '',
                'chk_donation_goal' => (isset($get_post_request_data_donation_button['chk_donation_goal'])) ? $get_post_request_data_donation_button['chk_donation_goal'] : '',
            );
            $donation_button_array_merge = array_merge($donation_button_array_merge, $donation_button_array_field);

            foreach ($get_post_request_data_donation_button as $key => $value) {
                if (array_key_exists($key, $donation_button_array_merge)) {
                    $donation_button_array_merge_final[$key] = $value;
                }
            }
            update_post_meta($postID, 'donation_button_detail', $donation_button_array_merge_final);
        } else {

            $notise_value = "";
            if (isset($t_amount) && empty($t_amount)) {
                $notise_value .= "Donation target amount empty.<br />";
            }
            if (isset($s_date) && empty($s_date)) {
                $notise_value .= "Donation start date empty.<br />";
            }

            if (isset($e_date) && empty($e_date)) {
                $notise_value .= "Donation end date empty.<br />";
            }
            set_transient('goal_require_field_notice_error', $notise_value, 20);
        }
        //  }
    }

    public function goal_require_field_empty_notice_error() {

        $goal_require_field_notice_error = get_transient('goal_require_field_notice_error');
        delete_transient('goal_require_field_notice_error');
        if (isset($goal_require_field_notice_error) && !empty($goal_require_field_notice_error)) {
            $class = 'notice notice-error';
            $message = __($goal_require_field_notice_error, 'donation-button');
            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
        }
    }

    public function set_custom_edit_donation_goal_columns($columns) {

        unset($columns['date']);
        $columns['donation_button_short_code'] = __('Short Code', 'paypal-invoicing');
        $columns['date'] = __('Date', 'paypal-invoicing');
        return $columns;
    }

    public function custom_donationgoal_columns($column, $post_id) {

        switch ($column) {

            case 'donation_button_short_code' :
                echo $this->create_donation_goal_short_code($post_id);
                break;
            default :
                break;
        }
    }

    public function create_donation_goal_short_code($id) {

        $goal_array_data = get_post_meta($id, 'donation_button_detail');
        $goal_array = array();
        if (is_array($goal_array_data) && count($goal_array_data) > 0) {
            $goal_array = $goal_array_data[0];
        }


        $t_amount = isset($goal_array['donation_button_target_amount']) ? $goal_array['donation_button_target_amount'] : '';
        $start_date = date("Y-m-d", strtotime(isset($goal_array['donation_button_start_date']) ? $goal_array['donation_button_start_date'] : ''));
        $end_date = date("Y-m-d", strtotime(isset($goal_array['donation_button_end_date']) ? $goal_array['donation_button_end_date'] : ''));

        if ("1970-01-01" == $start_date) {
            $start_date = "";
        }

        if ("1970-01-01" == $end_date) {
            $end_date = "";
        }

        if ((!isset($t_amount) || empty($t_amount)) || (!isset($start_date) || empty($start_date)) || (!isset($end_date) || empty($end_date))) {
            $result = 'N/A';
        } else {
            $result = '[donation_goal id="' . $id . '"]';
        }
        return $result;
    }

    public function bs_donation_goal_table_sorting($columns) {
        $columns['donation_button_short_code'] = 'donation_button_short_code';
        return $columns;
    }

    public function donation_add_my_media_button() {
        ?>
        <a href="javascript:;" class="button donation_popup_container_button" style="background-color: #0091cd; border: 1px solid #0091cd;box-shadow: inset 0px 1px 0px 0px #0091cd;color: #FFFFFF;">PayPal Donation Button</a>		

        <?php
        add_thickbox();
        echo '<a style="display: none;" href="#TB_inline?height=&amp;width=470&amp;&inlineId=donation_popup_container" class="thickbox donation_popup_container">PayPal Donation Button</a>';
        ?>
        <div id="donation_popup_container" style="display: none;" class="wrap">            
            <div  class="donation-payment-form-style-9" id="donation-payment-accordion">
                <ul>
                    <li>
                        <a href="#donation_enable_table_border">Enable Table Border Front-End</a>
                        <div id="donation_enable_table_border" class="donation-payment-accordion">
                            <div class="wrap" style="margin:0px;"><table class="widefat"><tr><td style="padding-top: 20px;font-size: 15px;width:auto;"><input type="checkbox" id="donation_payment_enable_border" name="donation_payment_enable_border" value=""> Enable Table Border</td></tr><tr><td><select hidden style="height: 38px;width: 100%;" name="donation_payment_table_border" id="donation_payment_table_border" class="donation-payment-field-style donation-payment-class-select"><option value="0">Select Table Border</option><option value="1">1px</option><option value="2">2px</option><option value="3">3px</option><option value="4">4px</option><option value="5">5px</option></select></td></tr></table></div>
                        </div>
                    </li>
                    <li>
                        <a href="#donation_set_align">Set Button Align Front-End</a>
                        <div id="donation_set_align" class="donation-payment-accordion">
                            <div class="wrap" style="margin:0px;"><table class="widefat"><tr><td><select style="height: 38px;width: 100%;" name="donation_set_button_align" id="donation_set_button_align" class="donation-payment-field-style donation-payment-class-select"><option value="align">Set Button Alignment</option><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></td></tr></table></div>
                        </div>
                    </li>
                    <li>
                        <a href="#donation_button_create_price_shortcode">Create Price Shortcode</a>
                        <div id="donation_button_create_price_shortcode" class="donation-payment-accordion">
                            <div class="wrap" style="margin:0px;"><table class="widefat"><tr><td><select style="height: 38px;" name="donation_payment_tab_price_shortcode_price" id="donation_payment_tab_price_shortcode_price" class="donation-payment-field-style donation-payment-class-select"><option value="none">Select Price Shortcode</option><option value="1">Simple Price Shortcode</option><option value="2">Options Price Shortcode</option></select></td></tr></table></div>
                            <div class="donation-payment-div-option-create-price"></div>
                        </div>
                    </li>
                    <li>
                        <a href="#donation_button_create_custom_shortcode">Create Custom Shortcode</a>
                        <div id="donation_button_create_custom_shortcode" style=" height: 190px;overflow: auto;" class="donation-payment-accordion">
                            <div class="wrap" style="margin:0px;">
                                <table id="donation-payment-table-0" class="widefat" data-custom="0" style="box-shadow: inset 0 0 10px green;">
                                    <tr>
                                        <td colspan="2">
                                            <input class="donation_payment_add_new_custom_button" type="button" id="donation_payment_add_new_custom_button" value="Add New Custom Option">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input style="height: 38px;width: 100%;" type = "text" name ="donation_payment_custom_lable0" id = "donation_payment_custom_lable0" class = "donation-payment-field-style" placeholder = "Enter Custom Lable Name">
                                        </td>
                                    </tr>
                                    <tr id="donation-payment-table-option-0" data-tr="0">
                                        <td>
                                            <input style="height: 38px;width: 90%;" type = "text" name = "on00" id = "on00" class = "donation-payment-field-style" placeholder = "Key">
                                        </td>
                                        <td>
                                            <input style="height: 38px;width: 90%;" type = "text" name = "os00" id = "os00" class = "donation-payment-field-style" placeholder = "Value">
                                            <span id="donation-payment-add-row-0" class="donation-payment-custom-add donation-add-remove-icon-paypal" data-custom-span="0">
                                                <img src="<?php echo plugin_dir_url(__FILE__); ?>images/add.png">
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </li>
                </ul>
                <input class="donation-payment-background-color-table" type="button" id="donation_payment_insert" value="Create Donation Payment Button"> 
                <input type="hidden" class="DONATION_PAYMENT_SITE_URL" name="DONATION_PAYMENT_SITE_URL" value="<?php echo plugin_dir_url(__FILE__); ?>">
                <input type="hidden" class="DONATION_PAYMENT_NUMBER_OF_TABLE" name="DONATION_PAYMENT_NUMBER_OF_TABLE" value="0">
            </div>
        </div>
        <?php
    }

}