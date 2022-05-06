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
        'use strict';
        $.widget(
            'mageants.maintenancemode', {
                _create: function () {
                    var self         = this,
                        email        = $("#email_address"),
                        submitButton = $('.form.form-create-account .action.submit'),
                        emailData    = self.options.data.emails;

                    submitButton.attr('disabled', 'disabled');

                    email.focusout(function () {
                        if (emailData.includes(email.val())) {
                            $('.mageants-error').remove();
                            submitButton.attr('disabled', 'disabled');
                            email.parent('div.control').append('<div for="email_address" generated="true" class="mage-error mageants-error" id="email_address-error">This email address is already subscribed!</div>');
                        } else {
                            $('.mageants-error').remove();
                            submitButton.removeAttr("disabled");
                        }
                    });
                }
            }
        );

        return $.mageants.maintenancemode;
    }
);
