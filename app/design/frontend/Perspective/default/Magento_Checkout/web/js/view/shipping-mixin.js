define([
    'jquery'
], function ($) {
    'use strict';

    return function (originalShipping) {
        return originalShipping.extend({
            initialize: function () {
                this._super()
                this.isFilteringrates = false
                this.rates.subscribe(this.filterRates.bind(this))
            },

            filterRates: function (rates) {

                if (this.isFilteringrates) {
                    return
                }
                console.log('Before filtering', rates);
                this.isFilteringrates = true;

                const filteredRates= rates.filter(rate => rate.amount <= 1000);
                this.rates(filteredRates);

                console.log('After filtering', rates);
                this.isFilteringrates = false;
            }
        });
    };
});
