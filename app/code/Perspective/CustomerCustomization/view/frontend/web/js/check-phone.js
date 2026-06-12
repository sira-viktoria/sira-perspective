define([
    'jquery',
    'mage/url'
], function ($, urlBuilder) {
    'use strict';

    return function (config) {
        const phoneInput = $('#additional_phone');

        if ($('#phone-error-msg').length === 0) {
            phoneInput.after('<div id="phone-error-msg" style="color: #e02b27; font-size: 1.2rem; margin-top: 5px; display: none;"></div>');
        }
        const errorMsg = $('#phone-error-msg');

        phoneInput.on('blur', function () {
            const phoneVal = $(this).val().trim();
            if (!phoneVal) {
                resetStyles();
                return;
            }
            $.ajax({
                url: urlBuilder.build('perspective/ajax/checkAdditionalPhone'),
                data: { additional_phone: phoneVal },
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    phoneInput.css('opacity', '0.6');
                },
                success: function (response) {
                    phoneInput.css('opacity', '1');
                    if (response.unique) {
                        let phoneRegex = /^\+38\(\d{3}\)\d{2}-\d{3}-\d{2}$/;

                        if (!phoneRegex.test(phoneInput.val())) {
                            phoneInput.css('border', '2px solid #e02b27');
                        } else {
                            phoneInput.css('border', '2px solid #00b050');
                            errorMsg.hide();
                        }
                    } else {
                        phoneInput.css('border', '2px solid #e02b27');
                        errorMsg.text(response.message).show();
                    }
                },
                error: function () {
                    phoneInput.css('opacity', '1');
                }
            });
        });

        function resetStyles() {
            phoneInput.css('border', '');
            errorMsg.hide();
        }
    };
});
