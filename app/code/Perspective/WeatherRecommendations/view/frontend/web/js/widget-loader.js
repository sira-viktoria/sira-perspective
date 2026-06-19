define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        $.ajax({
            url: config.url,
            type: 'GET',
            cache: false,
            success: function (response) {
                $(element).html(response);
            }
        });
    };
});
