jQuery(function ($) {

    jQuery('#donation_payment_enable_border').val('0');
    var progress = jQuery('.donation-button-bar-percentage[data-percentage]');
    var percentage = Math.ceil(progress.attr('data-percentage'));
    $({countNum: 0}).animate({countNum: percentage}, {
        duration: 300,
        easing: 'linear',
        step: function () {
            var pct = '';
            pct = Math.floor(percentage) + '%';
            progress.text(pct) && progress.siblings().children().css('width', pct);
        }
    });

    jQuery('.donation_button_date').datepicker({
        dateFormat: 'dd-mm-yy'
    });
    jQuery('.donation_background_color_change').wpColorPicker();

    var donation_button_progress_background = jQuery('#donation_button_progress_background').val();
    var donation_button_bar_percentage_background = jQuery('#donation_button_bar_percentage_background').val();
    var donation_button_bar_background = jQuery('#donation_button_bar_background').val();
    var donation_button_bar_and_font = jQuery('#donation_button_bar_and_font').val();
    var donation_button_preview_table_color = jQuery('#donation_button_preview_table_color').val();

    jQuery('.donation-button-bar-main-container').css('background', donation_button_progress_background);
    jQuery('.donation-button-bar-percentage').css('background', donation_button_bar_percentage_background);
    jQuery('.donation-button-bar-container').css('background-color', donation_button_bar_percentage_background);
    jQuery('.donation-button-bar').css('background-color', donation_button_bar_background);
    jQuery('.donation-button-bar-main-container').css('color', donation_button_bar_and_font);
    jQuery('.donation_button_table_tbody_backgroud_color tr th').css('color', donation_button_preview_table_color);
    jQuery('.donation_button_table_tbody_backgroud_color tr td').css('color', donation_button_preview_table_color);


    jQuery(document).on('click', '.donation_button_pbg_reload_color', function () {
        var donation_button_progress_background = jQuery('#donation_button_progress_background').val();
        jQuery('.donation-button-bar-main-container').css('background', donation_button_progress_background);
    });
    jQuery(document).on('click', '.donation_button_bpg_reload_color', function () {
        var donation_button_bar_percentage_background = jQuery('#donation_button_bar_percentage_background').val();
        jQuery('.donation-button-bar-percentage').css('background', donation_button_bar_percentage_background);
        jQuery('.donation-button-bar-container').css('background-color', donation_button_bar_percentage_background);
    });
    jQuery(document).on('click', '.donation_button_bb_reload_color', function () {
        var donation_button_bar_background = jQuery('#donation_button_bar_background').val();
        jQuery('.donation-button-bar').css('background-color', donation_button_bar_background);
    });
    jQuery(document).on('click', '.donation_button_bf_reload_color', function () {
        var donation_button_bar_and_font = jQuery('#donation_button_bar_and_font').val();
        jQuery('.donation-button-bar-main-container').css('color', donation_button_bar_and_font);
    });
    jQuery(document).on('click', '.donation_button_pt_color', function () {
        var donation_button_preview_table_color = jQuery('#donation_button_preview_table_color').val();
        jQuery('.donation_button_table_tbody_backgroud_color tr th').css('color', donation_button_preview_table_color);
        jQuery('.donation_button_table_tbody_backgroud_color tr td').css('color', donation_button_preview_table_color);
    });

    jQuery(document).on('focusout', '.donation_button_goal_detail', function () {
        jQuery('.label_donation_goal_detail').text(jQuery(this).val());
    });

    jQuery(document).on('change', '#donation_button_start_date', function () {
        jQuery('.label_donation_goal_start_date').text(jQuery('#donation_button_start_date').val());
    });
    jQuery(document).on('change', '#donation_button_end_date', function () {
        jQuery('.label_donation_goal_end_date').text(jQuery('#donation_button_end_date').val());
    });
    jQuery(document).on('focusout', '#donation_button_target_amount', function () {
        jQuery('.label_donation_goal_target_amount_lbl').text(jQuery('#donation_button_target_amount').val());
    });

    if (jQuery('#chk_donation_goal_detail_click').is(":checked")) {
        jQuery('#label_donation_goal_detail_tr').show();
    } else {
        jQuery('#label_donation_goal_detail_tr').hide();
    }
    if (jQuery('#chk_donation_target_amount_click').is(":checked")) {
        jQuery('#label_donation_goal_target_amount_lbl_tr').show();
    } else {
        jQuery('#label_donation_goal_target_amount_lbl_tr').hide();
    }
    if (jQuery('#chk_donation_goal_start_date_click').is(":checked")) {
        jQuery('#label_donation_goal_start_date_tr').show();
    } else {
        jQuery('#label_donation_goal_start_date_tr').hide();
    }
    if (jQuery('#chk_donation_goal_end_date_click').is(":checked")) {
        jQuery('#label_donation_goal_end_date_tr').show();
    } else {
        jQuery('#label_donation_goal_end_date_tr').hide();
    }
    if (jQuery('#chk_donation_goal_display_paypal_donation_button_click').is(":checked")) {
        jQuery('#label_donation_goal_display_paypal_donation_button_tr').show();
    } else {
        jQuery('#label_donation_goal_display_paypal_donation_button_tr').hide();
    }

    jQuery(document).on('click', '#chk_donation_goal_detail_click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#label_donation_goal_detail_tr').show();
        } else {
            jQuery('#label_donation_goal_detail_tr').hide();
        }
    });
    jQuery(document).on('click', '#chk_donation_target_amount_click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#label_donation_goal_target_amount_lbl_tr').show();
        } else {
            jQuery('#label_donation_goal_target_amount_lbl_tr').hide();
        }
    });
    jQuery(document).on('click', '#chk_donation_goal_start_date_click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#label_donation_goal_start_date_tr').show();
        } else {
            jQuery('#label_donation_goal_start_date_tr').hide();
        }
    });
    jQuery(document).on('click', '#chk_donation_goal_end_date_click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#label_donation_goal_end_date_tr').show();
        } else {
            jQuery('#label_donation_goal_end_date_tr').hide();
        }
    });
    jQuery(document).on('click', '#chk_donation_goal_display_paypal_donation_button_click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#label_donation_goal_display_paypal_donation_button_tr').show();
        } else {
            jQuery('#label_donation_goal_display_paypal_donation_button_tr').hide();
        }
    });
    jQuery(document).on('keydown', '.donation_button_target_amount', function (e) {
        //jQuery(".donation_button_target_amount").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 || (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) || (e.keyCode >= 35 && e.keyCode <= 40)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    jQuery(document).on('click', '#donation_button_twilio_test_sms_button', function () {
        var donation_button_twilio_sms_test_mobile_number = jQuery("#donation_button_twilio_sms_test_mobile_number").val();
        var donation_button_twilio_sms_test_message = jQuery("#donation_button_twilio_sms_test_message").val();
        var data = {
            action: 'donation_button_twilio_send_test_sms',
            donation_button_twilio_sms_test_mobile_number: donation_button_twilio_sms_test_mobile_number,
            donation_button_twilio_sms_test_message: donation_button_twilio_sms_test_message
        };
        $.post(donation_button_twilio_test_sms_button_params.ajax_url, data, function (response) {
            response = JSON.parse(response);
            if (typeof (response.success) !== 'undefined') {
                if (response.success.length > 0) {
                    alert(response.success);
                } else {
                    alert(response.error);
                }
            } else {
                alert(response.error);
            }
        });
    });
    jQuery(document).on('click', '.donation_popup_container_button', function () {
        jQuery('.donation_popup_container').trigger('click');
        m7_resize_thickbox();
    });

    jQuery(window).resize(function () {
        m7_resize_thickbox();
    });

    function m7_resize_thickbox() {
        var TB_HEIGHT = 'auto';
        var TB_WIDTH = jQuery('#TB_window').width();
        jQuery(document).find('#TB_window').width(TB_WIDTH).height(TB_HEIGHT).css('margin-left', -TB_WIDTH / 2);
        jQuery(document).find('#TB_ajaxContent').css({'width': '', 'height': ''});
    }


    //Add table tr First tab
    jQuery(document).on('change', '#donation_payment_tab_price_shortcode_price', function () {
        var image_url = jQuery('.DONATION_PAYMENT_SITE_URL').val();
        if ("1" == jQuery('#donation_payment_tab_price_shortcode_price').val()) {
            jQuery('.donation-payment-div-option-create-price').html('');
            var string = '<div class="wrap" style="margin:0px;"><table class="widefat" id="donation_button_create_price_shortcode_1"><tr><td><input style="height: 38px;width: 100%;" type = "text" name = "os0" id = "os0" class = "donation-payment-field-style" placeholder = "Value"></td></tr></table></div>';
            jQuery('.donation-payment-div-option-create-price').append(string);
        } else if ("2" == jQuery('#donation_payment_tab_price_shortcode_price').val()) {
            jQuery('.donation-payment-div-option-create-price').html('');
            var string = '<div class="wrap" style="margin:0px; height: 115px;overflow: auto;"><table style="box-shadow: inset 0 0 6px green;" id="donation_payment_option_table" class="widefat"><tr><td colspan="2"><input style="height: 38px;width: 100%;" type = "text" name ="donation_payment_lable" id = "donation_payment_lable" class = "donation-payment-field-style" placeholder = "Enter Lable Name"></td></tr><tr id="option_tr_0" data-tr="0"><td><input style="height: 38px;width: 90%;" type = "text" name = "on0" id = "on0" class = "donation-payment-field-style" placeholder = "Key"></td><td><input style="height: 38px;width: 90%;" type = "text" name = "os0" id = "os0" class = "donation-payment-field-style" placeholder = "Value"><span id="donation-payment-add-icon" class="donation-payment-add-remove-icon donation-add-remove-icon-paypal"><img src="' + image_url + 'images/add.png"</span></td></tr></table></div>';
            jQuery('.donation-payment-div-option-create-price').append(string);
        } else {
            jQuery('.donation-payment-div-option-create-price').html('');
        }
    });
    jQuery(document).on('click', '#donation-payment-add-icon', function () {
        var image_url = jQuery('.DONATION_PAYMENT_SITE_URL').val();
        var last_tr_id = jQuery('#donation_payment_option_table tr:last').attr('data-tr');
        if (last_tr_id < 4)
        {
            var id = parseInt(last_tr_id) + 1;
            var str_row = '<tr id="option_tr_' + id + '" data-tr="' + id + '"><td><input style="height: 38px;width: 90%;" type = "text" name = "on' + id + '" id = "on' + id + '" class = "donation-payment-field-style" placeholder = "Key"></td><td><input style="height: 38px;width: 90%;" type = "text" name = "os' + id + '" id = "os' + id + '" class = "donation-payment-field-style" placeholder = "Value"><span id="donation-payment-remove-icon' + id + '" class="donation-payment-add-remove-icon donation-add-remove-icon-paypal" data-value="' + id + '"><img src="' + image_url + 'images/remove.png"</span></td></tr>';
            jQuery("#option_tr_" + last_tr_id).after(str_row);
        }
    });
    jQuery(document).on('click', '.donation-payment-add-remove-icon', function () {
        var id = jQuery(this).attr("data-value");
        jQuery('#option_tr_' + id).remove();
        donation_reset_name_with_id(id);
    });
    function donation_reset_name_with_id(id) {
        var new_id = parseInt(id) + 1;
        var last_tr_id = jQuery('#donation_payment_option_table tr:last').attr('data-tr');
        for (var i = new_id; i <= last_tr_id; i++) {
            var cla_data = parseInt(i) - 1;
            jQuery('#option_tr_' + i).attr('data-tr', cla_data);
            jQuery('#option_tr_' + i).attr('id', 'option_tr_' + cla_data);
            jQuery('#on' + i).attr('name', 'on' + cla_data);
            jQuery('#on' + i).attr('id', 'on' + cla_data);
            jQuery('#os' + i).attr('name', 'os' + cla_data);
            jQuery('#os' + i).attr('id', 'os' + cla_data);
            jQuery('#donation-payment-remove-icon' + i).attr('data-value', +cla_data);
            jQuery('#donation-payment-remove-icon' + i).attr('id', 'donation-payment-remove-icon' + cla_data);
        }
    }

    //Add table tr second Tab    

    jQuery(document).on('click', '.donation-payment-custom-add', function () {
        var image_url = jQuery('.DONATION_PAYMENT_SITE_URL').val();
        var table_current_id = jQuery(this).closest('table').attr('id')
        var table_data_custom_id = jQuery(this).closest('table').attr('data-custom')
        var last_tr_id = jQuery('#' + table_current_id + ' tr:last').attr('data-tr');
        if (last_tr_id < 4)
        {
            var id = parseInt(last_tr_id) + 1;
            var str_row = '<tr id="donation-payment-table-option-' + id + '" data-tr="' + id + '"><td><input style="height: 38px;width: 90%;" type = "text" name = "on' + table_data_custom_id + id + '" id = "on' + table_data_custom_id + id + '" class = "donation-payment-field-style" placeholder = "Key"></td><td><input style="height: 38px;width: 90%;" type = "text" name = "os' + table_data_custom_id + id + '" id = "os' + table_data_custom_id + id + '" class = "donation-payment-field-style" placeholder = "Value"><span id="donation-payment-remove-tr-' + id + '" class="donation-payment-custom-remove donation-add-remove-icon-paypal" data-value="' + id + '"><img src="' + image_url + 'images/remove.png"</span></td></tr>';
            jQuery("#" + table_current_id + " #donation-payment-table-option-" + last_tr_id).after(str_row);

        }
    });
    jQuery(document).on('click', '.donation-payment-custom-remove', function () {
        var id = jQuery(this).attr("data-value");
        var table_current_id = jQuery(this).closest('table').attr('id');
        var table_value = jQuery(this).closest('table').attr('data-custom')
        jQuery("#" + table_current_id + " #donation-payment-table-option-" + id).remove();
        donation_second_tab_reset_name_with_id(id, table_current_id, table_value);
    });

    function donation_second_tab_reset_name_with_id(id, table_current_id, table_value) {

        var new_id = parseInt(id) + 1;
        var last_tr_id = jQuery("#" + table_current_id + " tr:last").attr('data-tr');
        for (var i = new_id; i <= last_tr_id; i++) {

            var cla_data = parseInt(i) - 1;
            jQuery('#' + table_current_id + ' #donation-payment-table-option-' + i).attr('data-tr', cla_data);
            jQuery('#' + table_current_id + ' #donation-payment-table-option-' + i).attr('id', 'donation-payment-table-option-' + cla_data);
            jQuery('#' + table_current_id + ' #on' + table_value + i).attr('name', 'on' + table_value + cla_data);
            jQuery('#' + table_current_id + ' #on' + table_value + i).attr('id', 'on' + table_value + cla_data);
            jQuery('#' + table_current_id + ' #os' + table_value + i).attr('name', 'os' + table_value + cla_data);
            jQuery('#' + table_current_id + ' #os' + table_value + i).attr('id', 'os' + table_value + cla_data);
            jQuery('#' + table_current_id + ' #donation-payment-remove-tr-' + i).attr('data-value', +cla_data);
            jQuery('#' + table_current_id + ' #donation-payment-remove-tr-' + i).attr('id', 'donation-payment-remove-tr-' + cla_data);
        }
    }

    //Add New Custom Table     
    jQuery(document).on('click', '#donation_payment_add_new_custom_button', function () {
        var image_url = jQuery('.DONATION_PAYMENT_SITE_URL').val();
        var number_of_table = jQuery('.DONATION_PAYMENT_NUMBER_OF_TABLE').val();

        if (number_of_table < 4) {
            var id = parseInt(number_of_table) + 1;
            var str_row = '<table style="box-shadow: inset 0 0 6px red;" id="donation-payment-table-' + id + '" class="widefat" data-custom="' + id + '"><tr><td colspan="2"><input class="donation_payment_remove_new_custom_button" type="button" id="donation_payment_remove_new_custom_button" name="donation_payment_remove_new_custom_button" value="Remove Custom Option"></td></tr><tr><td colspan="2"><input style="height: 38px;width: 100%;" type = "text" name ="donation_payment_custom_lable' + id + '" id = "donation_payment_custom_lable' + id + '" class = "donation-payment-field-style" placeholder = "Enter Custom Lable Name"></td></tr><tr id="donation-payment-table-option-0" data-tr="0"><td><input style="height: 38px;width: 90%;" type = "text" name = "on' + id + '0" id = "on' + id + '0" class = "donation-payment-field-style" placeholder = "Key"></td><td><input style="height: 38px;width: 90%;" type = "text" name = "os' + id + '0" id = "os' + id + '0" class = "donation-payment-field-style" placeholder = "Value"><span class="donation-payment-custom-add donation-add-remove-icon-paypal"><img src="' + image_url + 'images/add.png"></span></td></tr></table>';
            jQuery("#donation-payment-table-" + number_of_table).after(str_row);
            jQuery(".DONATION_PAYMENT_NUMBER_OF_TABLE").val(id);
        }
    });

    //Remove Custom Table
    jQuery(document).on('click', '#donation_payment_remove_new_custom_button', function () {
        var number_of_table = jQuery('.DONATION_PAYMENT_NUMBER_OF_TABLE').val();
        var new_value = jQuery(this).closest('table').attr('data-custom');
        var new_id = parseInt(new_value) + 1;
        for (var i = new_id; i <= number_of_table; i++)
        {
            var cla_data = parseInt(i) - 1;
            var table_current_id = jQuery("#donation-payment-table-" + i).closest('table').attr('id');
            jQuery('#' + table_current_id).attr('data-custom', cla_data);
            var last_tr_id = jQuery("#" + table_current_id + " tr:last").attr('data-tr');
            for (var j = 0; j <= last_tr_id; j++)
            {
                jQuery('#' + table_current_id + ' #donation-payment-table-option-' + j).attr('data-tr', j);
                jQuery('#' + table_current_id + ' #donation-payment-table-option-' + j).attr('id', 'donation-payment-table-option-' + j);
                jQuery('#' + table_current_id + ' #on' + i + j).attr('name', 'on' + cla_data + j);
                jQuery('#' + table_current_id + ' #on' + i + j).attr('id', 'on' + cla_data + j);
                jQuery('#' + table_current_id + ' #os' + i + j).attr('name', 'os' + cla_data + j);
                jQuery('#' + table_current_id + ' #os' + i + j).attr('id', 'os' + cla_data + j);
                jQuery('#' + table_current_id + ' #donation-payment-remove-tr-' + j).attr('data-value', +j);
                jQuery('#' + table_current_id + ' #donation-payment-remove-tr-' + j).attr('id', 'donation-payment-remove-tr-' + j);
            }

            jQuery('#' + table_current_id + ' #donation_payment_custom_lable' + i).attr('id', 'donation_payment_custom_lable' + cla_data);
            jQuery('#donation-payment-table-' + i).attr('id', 'donation-payment-table-' + cla_data);

        }
        var table_current_id = jQuery(this).closest('table').attr('id');
        jQuery("#" + table_current_id).remove();
        var id = parseInt(number_of_table) - 1;
        jQuery(".DONATION_PAYMENT_NUMBER_OF_TABLE").val(id);
    });

    // Insert ShortCode

    jQuery(document).on('click', '#donation_payment_insert', function () {
        var button_align = donation_button_align_shortcode();
        var tab_lable_string = donation_create_lable_shortcode();
        var tab_0_string = donation_enable_border_tab_0();
        var tab_1_string = donation_create_price_shortcode_tab_1();
        var tab_2_string = donation_create_price_shortcode_tab_2();
        window.send_to_editor('[paypal_donation_button' + button_align + tab_0_string + tab_1_string + tab_2_string + tab_lable_string + ']');
    });
    jQuery(document).on('change', '#donation_payment_enable_border', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#donation_payment_enable_border').val('1');
            jQuery('#donation_payment_table_border').show();
        } else {
            jQuery('#donation_payment_enable_border').val('0');
            jQuery('#donation_payment_table_border').hide();
        }
    });

    function donation_button_align_shortcode() {
        var button_align = "";
        var get_align = jQuery('#donation_set_button_align').val();
        if (get_align != 'align') {
            button_align = ' align="' + get_align + '"';
        }
        return button_align;
    }

    function donation_enable_border_tab_0() {
        var enable_string = "";
        var enable_check_box = jQuery('#donation_payment_enable_border').val();

        if (enable_check_box == '1') {
            var get_border = jQuery('#donation_payment_table_border').val();
            if (get_border != '0') {
                enable_string = ' border="' + get_border + '"';
            }
        }

        return enable_string;
    }
    function donation_create_lable_shortcode() {

        var lable_string = "";
        var str = "";
        var lable_value = jQuery('#donation_payment_lable').val();
        var table_count = jQuery('.DONATION_PAYMENT_NUMBER_OF_TABLE').val();
        if (typeof lable_value != 'undefined' && lable_value !== null) {
            if (lable_value.toString().length > 0) {
                var get_madatory_option_tab_1 = Donation_payment_set_lable_with_taxt_box_value_tab_1();
                if (get_madatory_option_tab_1 == true) {
                    if (table_count == '0') {
                        var table_enable_true_false = donation_enable_table_0();
                        if (table_enable_true_false == true) {
                            str += lable_value + ', ';
                        } else {
                            str += lable_value + ' ';
                        }
                    } else {
                        str += lable_value + ', ';
                    }
                }
            }
        }
        if (table_count >= '0') {
            for (var i = 0; i <= table_count; i++) {
                var lable = jQuery('#donation_payment_custom_lable' + i).val();
                var get_madatory_option = donation_set_lable_with_taxt_box_value(i);
                if (get_madatory_option == true && lable.toString().length > 0) {
                    var join_str = ', ';
                    if (i == table_count) {
                        join_str = '';
                    }
                    str += lable + join_str;
                }
            }
        }
        if (str.toString().length > 2) {
            str = str.match(/[^*]+[^,{\s+}?]/g);
            lable_string = ' lable_name=" ' + str + ' "';
        }
        return lable_string;
    }

    function donation_enable_table_0() {
        var result = false;
        var first_lable = jQuery('#donation-payment-table-0 #donation_payment_custom_lable0').val();
        var pccg_last_tr = jQuery('#donation-payment-table-0 tr:last').attr('data-tr');
        if (first_lable.toString().length > 0) {
            for (var i = 0; i <= pccg_last_tr; i++) {
                var first_on = jQuery('#donation-payment-table-0 #on0' + i).val();
                var first_os = jQuery('#donation-payment-table-0 #os0' + i).val();
                if (first_on.toString().length > 0 && first_os.toString().length > 0) {
                    return true;
                }
            }
        }
        return result;
    }

    function Donation_payment_set_lable_with_taxt_box_value_tab_1() {

        var return_str = false;
        var last_tr_id = jQuery("#donation_payment_option_table tr:last").attr('data-tr');
        for (var j = 0; j <= last_tr_id; j++) {
            var key = jQuery('#donation_payment_option_table #on' + j).val();
            var value = jQuery('#donation_payment_option_table #os' + j).val();
            if ((typeof key != 'undefined' && key.toString().length > 0) && (typeof value != 'undefined' && value.toString().length > 0)) {
                return_str = true;
                return true;
            }
        }
        return return_str;
    }

    function donation_set_lable_with_taxt_box_value(i) {

        var return_str = false;
        var last_tr_id = jQuery("#donation-payment-table-" + i + " tr:last").attr('data-tr');
        for (var j = 0; j <= last_tr_id; j++) {
            var key = jQuery('#donation-payment-table-' + i + ' #on' + i + j).val();
            var value = jQuery('#donation-payment-table-' + i + ' #os' + i + j).val();
            if ((typeof key != 'undefined' && key.toString().length > 0) && (typeof value != 'undefined' && value.toString().length > 0)) {
                return_str = true;
                return true;
            }
        }
        return return_str;
    }

    function donation_create_price_shortcode_tab_1() {
        var result_string = "";
        var str = "";
        var select_method = jQuery('#donation_payment_tab_price_shortcode_price').val();
        if ('1' == select_method) {
            str = jQuery('#donation_button_create_price_shortcode_1 #os0').val();
            if (str.toString().length > 0) {
                result_string = ' price="' + str + '"';
            }
        } else if ('2' == select_method) {
            var last_tr_id = jQuery('#donation_payment_option_table tr:last').attr('data-tr');
            result_string = donation_loop_option_table(last_tr_id);
        }

        return result_string;
    }
    function donation_loop_option_table(last_tr_id) {
        var string = "";
        var str = "";
        var donation_count_loop = 0;
        var lable_value = jQuery('#donation_payment_lable').val();
        if (lable_value.toString().length > 0) {
            lable_value = "LABLE_0";
            for (var i = 0; i <= last_tr_id; i++) {
                var join_str = " | ";
                var key = "";
                var value = "";
                key = jQuery('#on' + i).val();
                value = jQuery('#os' + i).val();
                if (key.toString().length > 0 && value.toString().length > 0) {
                    if (donation_count_loop == '0')
                    {
                        join_str = '';
                    }
                    str += join_str + "value='" + key + "' price='" + value + "'";
                    string = ' ' + lable_value + '=" ' + str + ' "';
                    donation_count_loop = parseInt(donation_count_loop) + 1;
                }
            }
        }
        return string;
    }
    function donation_create_price_shortcode_tab_2() {
        var result_string = "";
        var table_count = jQuery('.DONATION_PAYMENT_NUMBER_OF_TABLE').val();
        result_string = donation_loop_option_table_tab_2(table_count);
        return result_string;
    }
    function donation_loop_option_table_tab_2(table_count) {
        var string = "";
        var str = "";
        for (var i = 0; i <= table_count; i++) {
            str = "";
            var donation_count_loop = 0;
            var last_tr_id = jQuery('#donation-payment-table-' + i + ' tr:last').attr('data-tr');
            if (last_tr_id.toString().length > 0) {
                for (var j = 0; j <= last_tr_id; j++) {
                    var join_str = " | ";
                    var key = "";
                    var value = "";

                    key = jQuery('#donation-payment-table-' + i + ' #on' + i + j).val();
                    value = jQuery('#donation-payment-table-' + i + ' #os' + i + j).val();

                    if (key.toString().length > 0 && value.toString().length > 0) {
                        if (donation_count_loop == '0')
                        {
                            join_str = '';
                        }
                        str += join_str + "value='" + key + "' price='" + value + "'";
                        donation_count_loop = parseInt(donation_count_loop) + 1;
                    }
                }
                var lable_value = jQuery('#donation_payment_custom_lable' + i).val();
                if (str.toString().length == 0 || lable_value.toString().length == 0) {

                } else {
                    lable_value = "LABLE" + i;
                    if (lable_value.toString().length > 0) {
                        string += ' ' + lable_value + '=" ' + str + ' "';
                    }
                }
            }
        }
        return string;
    }
});