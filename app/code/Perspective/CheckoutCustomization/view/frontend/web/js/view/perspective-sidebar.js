define([
    'uiComponent',
    'ko'
], function (Component, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perspective_CheckoutCustomization/perspective-sidebar'
        },
        initialize: function () {
            this._super();
            return this;
        },
        getCustomMessage: function () {
            return 'Thank you for shopping with us! Perspective Team.';
        }
    });
});
