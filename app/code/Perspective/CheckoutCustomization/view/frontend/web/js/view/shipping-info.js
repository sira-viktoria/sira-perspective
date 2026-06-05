define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote'

], function (Component, ko, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perspective_CheckoutCustomization/shipping-info'
        },

        initObservable: function () {
            let self = this._super();

            this.isVisible = ko.computed(function() {
                let method = quote.shippingMethod();
                return !!(method && method['carrier_code'] !== undefined);
            }, this);

            this.additionalText = ko.computed(function() {
                let method = quote.shippingMethod();
                if(method && method['carrier_code'] !== undefined && method['carrier_title'] !== undefined) {
                        return 'You have selected the ' + method['carrier_title'] + ' delivery method';
                }

                return '';
            }, this);

            return this;
        }
    });
});
