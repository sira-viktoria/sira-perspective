define([
    'jquery',
    'mage/utils/wrapper'
],function ($, Wrapper) {
    "use strict";

    return function (origRules)
    {
        origRules.getObservableFields = Wrapper.wrap(
            origRules.getObservableFields,
            function (originalAction)
            {
                let fields = originalAction();
                fields.push('street');

                return fields;
            }
        );

        return origRules;
    };
});
