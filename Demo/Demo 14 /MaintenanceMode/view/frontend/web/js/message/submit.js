/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

define(
    [
        'jquery'
    ], function ($) {
        "use strict";

        var form = $('.form.subscribe');

        form.submit(function (e) {
            var url = form.attr('action'),
                email = $("#newsletter").val();

            if (form.validation('isValid')) {
                e.preventDefault();
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'POST',
                    showLoader: true,
                    data: {email: email},
                    error: function (res) {
                        $('#mageants-notice-msg').html(res.responseJSON);
                    }
                });
            }
        });
    }
);
