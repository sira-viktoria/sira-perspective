define([
    'jquery',
    'mage/translate'
], function($) {
    'use strict';

    return function(targetWidget) {
        $.validator.addMethod(
            'cyrillic-validator',
            function(value, element) {
                if (this.optional(element)) {
                    return true;
                }
                return /[\u0400-\u04FF]/.test(value);
            },
            $.mage.__('The Cyrillic characters are not allowed.')
        );

        $.validator.addMethod(
            'dob-age-validator',
            function (value, element) {
                if (!value) return false;

                let dobParts = value.split('/');
                let dob = new Date(dobParts[2], dobParts[0] - 1, dobParts[1]);
                let today = new Date();

                let age = today.getFullYear() - dob.getFullYear();
                let m = today.getMonth() - dob.getMonth();

                if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                if (age >= 18) {
                    $(element).css('border-color', 'green');
                    return true;
                } else {
                    $(element).css('border-color', 'red');
                    return false;
                }
            },
            $.mage.__('Restricted for users under 18.')
        );

        $.validator.addMethod(
            'ua-phone-validator',
            function(value, element) {
                if (this.optional(element)) {
                    return true;
                }

                let phoneRegex = /^\+38\(\d{3}\)\d{2}-\d{3}-\d{2}$/;

                if (!phoneRegex.test(value)) {
                    return false;
                }

                let operatorCode = '0' + value.substring(5, 7);

                let validOperators = [
                    '039', '067', '068', '096', '097', '098',
                    '050', '066', '095', '099',
                    '063', '073', '093',
                    '091', '092', '094'
                ];

                return validOperators.includes(operatorCode);
            },
            $.mage.__('Please enter a valid phone number. Ex.: +38(050)12-345-67')
        );

        return targetWidget;
    }
});
