jQuery(function ($) {
    var progress = jQuery('.donation-button-bar-percentage[data-percentage]');
    var percentage = Math.ceil(progress.attr('data-percentage'));
    jQuery({countNum: 0}).animate({countNum: percentage}, {
        duration: 300,
        easing: 'linear',
        step: function () {
            var pct = '';
            pct = Math.floor(percentage) + '%';
            progress.text(pct) && progress.siblings().children().css('width', pct);
        }
    });
    var donation_button_progress_background = jQuery('#donation_button_progress_background_color').val();
    var donation_button_bar_percentage_background = jQuery('#donation_button_bar_percentage_background_color').val();
    var donation_button_bar_background = jQuery('#donation_button_bar_background_color').val();
    var donation_button_bar_and_font = jQuery('#donation_button_bar_and_font_color').val();
    var donation_button_preview_table_color = jQuery('#donation_button_preview_table_color_color').val();
    jQuery('.donation-button-bar-main-container').css('background', donation_button_progress_background);
    jQuery('.donation-button-bar-percentage').css('background', donation_button_bar_percentage_background);
    jQuery('.donation-button-bar-container').css('background-color', donation_button_bar_percentage_background);
    jQuery('.donation-button-bar').css('background-color', donation_button_bar_background);
    jQuery('.donation-button-bar-main-container').css('color', donation_button_bar_and_font);
    jQuery('.donation_button_table_tbody_backgroud_color tr th').css('color', donation_button_preview_table_color);
    jQuery('.donation_button_table_tbody_backgroud_color tr td').css('color', donation_button_preview_table_color);
});