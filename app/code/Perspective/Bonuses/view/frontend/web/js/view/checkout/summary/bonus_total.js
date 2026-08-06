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
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'Perspective_Bonuses/checkout/summary/bonus_total',
                config: {}
            },
            totals: quote.getTotals(),
            isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,

            isDisplayed: function() {
                // return true;
                let messages = '';
                if (this.totals()) {
                    messages = totals.getSegment('bonus_total').title;
                }
                return messages !== '';
            },

            getValue: function() {
                let price = 0;
                if (this.totals()) {
                    price = totals.getSegment('bonus_total').value;
                }
                return this.getFormattedPrice(price);
            },
            getPureValue: function() {
                let price = 0;
                if (this.totals()) {
                    price = totals.getSegment('bonus_total').value;
                }
                return price;
            },

            getBonusMessages: function() {
                let messages = [];
                if (this.totals()) {
                    messages = totals.getSegment('bonus_total').title;
                }
                return messages.split('||');
            },
        });
    }
);
