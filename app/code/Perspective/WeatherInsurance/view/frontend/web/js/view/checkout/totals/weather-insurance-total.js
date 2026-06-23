/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Perspective_WeatherInsurance/checkout/totals/view/weather-insurance-total',
                config: {}
            },
            totals: quote.getTotals(),

            /**
             * @returns {boolean}
             */
            isDisplayed: function() {
                let price = this.getPrice();
                return price > 0;
            },

            /**
             * @returns {number}
             */
            getPrice: function() {
                let price = 0;
                if (this.totals()) {
                    price = totals.getSegment('perspective_weather_insurance_total').value;
                }
                return price;
            },

            /**
             * @returns {*}
             */
            getValue: function() {
                let price = this.getPrice();
                return this.getFormattedPrice(price);
            }
        });
    }
);
