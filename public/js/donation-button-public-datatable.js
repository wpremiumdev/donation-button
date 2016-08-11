jQuery(function ($) {
    jQuery(document).ready(function () {
        jQuery('#example').DataTable({
            "responsive": true,
            "sPaginationType": "full_numbers",
            "bLengthChange": false,
            "fnDrawCallback": function () {
                if (this.fnSettings().fnRecordsDisplay() > 10) {
                    jQuery('#example_paginate').css("display", "block");
                } else {
                    jQuery('#example_paginate').css("display", "none");
                }
            }
        });
        if (jQuery('input[name="cmd"]').length > 0) {
            var cmdarray = ["_xclick", "_cart", "_oe-gift-certificate", "_xclick-subscriptions", "_xclick-auto-billing", "_xclick-payment-plan", "_donations", "_s-xclick"];
            if (cmdarray.indexOf(jQuery('input[name="cmd"]').val()) > -1) {
                if (jQuery('input[name="bn"]').length > 0) {
                    jQuery('input[name="bn"]').val("mbjtechnolabs_SP");
                } else {
                    jQuery('input[name="cmd"]').after("<input type='hidden' name='bn' value='mbjtechnolabs_SP' />");
                }
            }
        }
    });
});