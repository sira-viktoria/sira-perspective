define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        $.ajax({
            url: config.url,
            data: { current_product_id: config.product_id },
            type: 'GET',
            success: function (response) {
                $(element).html(response);
            }
        });
    };
});
