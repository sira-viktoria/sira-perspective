/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'mage/url'
], function (Component, ko, cartCache, defaultTotal, $, quote, priceUtils, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perspective_WeatherInsurance/view/weather-insurance-checkbox'
        },

        isChecked: ko.observable(false),
        isVisible: ko.observable(false),

        initialize: function () {
            this._super();
            /**
             * @type {{isCheckboxVisible: boolean, isDefaultChecked: boolean, checkboxLabel: string, insurancePrice: number, checkboxDescription: string}}
             */
            let config = window.checkoutConfig.weatherInsurance; //get data from custom config provider

            this.isVisible(config.isVisible);
            this.isChecked(config.isDefaultChecked);

            this.checkboxLabelText = this.formatLabel(config.textLabel, config.insurancePrice);
            this.checkboxDescriptionText = config.description;
            this.temperature = config.temperature;

            this.isChecked.subscribe(function(value) {
                console.log('Insurance checkbox value changed:', value);
                $.ajax({
                    url: urlBuilder.build('perspective/ajax/saveToQuote'),
                    type: 'POST',
                    data: {
                        insurance_price: config.insurancePrice,
                        insurance_checkbox_state: value ? 1 : 0
                    },
                    success: function (response) {
                        cartCache.set('totals', null);
                        defaultTotal.estimateTotals();

                        console.log('AJAX insurance success:', response);
                    },
                    error: function (xhr) {
                        console.error('AJAX insurance error:', xhr.responseText);
                    }
                });
            });
            return this;
        },

        /**
         * Format label like this "Add delivery insurance ($10,000.00)"
         */
        formatLabel: function(checkboxLabel, insurancePrice) {
            let priceFormatted = priceUtils.formatPrice(
                insurancePrice,
                quote.getPriceFormat()
            );
            return checkboxLabel + ' (' + priceFormatted + ')';
        }
    });
});
