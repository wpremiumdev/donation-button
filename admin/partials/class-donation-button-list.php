<?php

/**
 * @class       Donation_Button_List
 * @version	1.0.0
 * @package	donation-button
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Donation_Button_List {

    public static function init() {
        add_action('admin_print_scripts', array(__CLASS__, 'disable_autosave'));
        if (is_admin()) {
            add_action('init', array(__CLASS__, 'donation_button_register_post_types'), 5);
        }
        add_action('add_meta_boxes', array(__CLASS__, 'donation_button_remove_meta_boxes'), 10);
        add_action('manage_edit-donation_list_columns', array(__CLASS__, 'donation_button_add_donation_list_columns'), 10, 2);
        add_action('manage_donation_list_posts_custom_column', array(__CLASS__, 'donation_button_render_donation_list_columns'), 2);
        add_filter('manage_edit-donation_list_sortable_columns', array(__CLASS__, 'donation_button_donation_list_sortable_columns'));
        add_action('pre_get_posts', array(__CLASS__, 'donation_button_ipn_column_orderby'));
        add_action('add_meta_boxes', array(__CLASS__, 'donation_button_add_meta_boxes_ipn_data_custome_fields'), 31);
    }

    public static function donation_button_register_post_types() {
        global $wpdb;
        if (post_type_exists('donation_list')) {
            return;
        }
        do_action('donation_button_register_post_type');
        register_post_type('donation_list', apply_filters('donation_button_register_post_type_ipn', array(
            'labels' => array(
                'name' => __('Donation List', 'donation-button'),
                'singular_name' => __('Donation List', 'donation-button'),
                'menu_name' => _x('Donation List', 'Admin menu name', 'donation-button'),
                'add_new' => __('Add Donation List', 'donation-button'),
                'add_new_item' => __('Add New Donation List', 'donation-button'),
                'edit' => __('Edit', 'donation-button'),
                'edit_item' => __('View Donation List', 'donation-button'),
                'new_item' => __('New Donation List', 'donation-button'),
                'view' => __('View Donation List', 'donation-button'),
                'view_item' => __('View Donation List', 'donation-button'),
                'search_items' => __('Search Donation List', 'donation-button'),
                'not_found' => __('No Donation List found', 'donation-button'),
                'not_found_in_trash' => __('No Donation List found in trash', 'donation-button'),
                'parent' => __('Parent Donation List', 'donation-button')
            ),
            'description' => __('This is where you can add new IPN to your store.', 'donation-button'),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false,
            ),
            'map_meta_cap' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'donation_list'),
            'query_var' => true,
            'menu_icon' => DBP_PLUGIN_URL . 'admin/images/donation-button.png',
            'supports' => array('title'),
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
                        )
                )
        );
    }

    public static function donation_button_remove_meta_boxes() {
        remove_meta_box('submitdiv', 'donation_list', 'side');
        remove_meta_box('slugdiv', 'donation_list', 'normal');
    }

    public static function donation_button_add_donation_list_columns($existing_columns) {
        $columns = array();
        $columns['cb'] = '<input type="checkbox" />';
        $columns['title'] = _x('Transaction ID', 'column name');
        $columns['first_name'] = _x('Name / Company', 'column name');
        $columns['mc_gross'] = __('Amount', 'column name');
        $columns['txn_type'] = __('Transaction Type', 'column name');
        $columns['payment_status'] = __('Payment status');
        $columns['payment_date'] = _x('Date', 'column name');
        return $columns;
    }

    public static function donation_button_render_donation_list_columns($column) {
        global $post;
        switch ($column) {
            case 'payment_date' :
                echo esc_attr(get_post_meta($post->ID, 'payment_date', true));
                break;
            case 'first_name' :
                echo esc_attr(get_post_meta($post->ID, 'first_name', true) . ' ' . get_post_meta($post->ID, 'last_name', true));
                echo (get_post_meta($post->ID, 'payer_business_name', true)) ? ' / ' . get_post_meta($post->ID, 'payer_business_name', true) : '';
                break;
            case 'mc_gross' :
                echo esc_attr(get_post_meta($post->ID, 'mc_gross', true)) . ' ' . esc_attr(get_post_meta($post->ID, 'mc_currency', true));
                break;
            case 'txn_type' :
                echo esc_attr(get_post_meta($post->ID, 'txn_type', true));
                break;

            case 'payment_status' :
                echo esc_attr(get_post_meta($post->ID, 'payment_status', true));
                break;
        }
    }

    public static function disable_autosave() {
        global $post;

        if ($post && get_post_type($post->ID) === 'donation_list') {
            wp_dequeue_script('autosave');
        }
    }

    public static function donation_button_donation_list_sortable_columns($columns) {
        $custom = array(
            'title' => 'txn_id',
            'first_name' => 'first_name',
            'mc_gross' => 'mc_gross',
            'txn_type' => 'txn_type',
            'payment_status' => 'payment_status',
            'payment_date' => 'payment_date'
        );
        return wp_parse_args($custom, $columns);
    }

    public static function donation_button_ipn_column_orderby($query) {
        global $wpdb;
        if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'donation_list' && isset($_GET['orderby']) && $_GET['orderby'] != 'None') {
            $query->query_vars['orderby'] = 'meta_value';
            $query->query_vars['meta_key'] = $_GET['orderby'];
        }
    }

    public static function donation_button_add_meta_boxes_ipn_data_custome_fields() {

        add_meta_box('donation-list-ipn-data-custome-field', __('Donation List Fields', 'donation-button'), array(__CLASS__, 'donation_button_display_ipn_custome_fields'), 'donation_list', 'normal', 'high');
    }

    public static function donation_button_display_ipn_custome_fields() {
        if ($keys = get_post_custom_keys()) {
            echo "<div class='wrap'>";
            echo "<table class='widefat'><thead>
                        <tr>
                            <th>" . __('IPN Field Name', 'donation-button') . "</th>
                            <th>" . __('IPN Field Value', 'donation-button') . "</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>" . __('IPN Field Name', 'donation-button') . "</th>
                            <th>" . __('IPN Field Value', 'donation-button') . "</th>

                        </tr>
                    </tfoot>";
            foreach ((array) $keys as $key) {
                $keyt = trim($key);
                if (is_protected_meta($keyt, 'post'))
                    continue;
                $values = array_map('trim', get_post_custom_values($key));
                $value = implode($values, ', ');
                echo "<tr><th class='post-meta-key'>$key:</th> <td>$value</td></tr>";
            }
            echo "</table>";
            echo "</div";
        }
    }

}

Donation_Button_List::init();