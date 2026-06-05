define([], function () {
    'use strict';
    let mixin = {
        isFullMode: function () {
            return this.getTotals();
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
