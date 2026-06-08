/*global define*/
define([
    'Magento_Ui/js/form/form'
], function(Component) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();
            return this;
        },

        /**
         * Form submit handler
         */
        onSubmit: function() {
            // trigger form validation
            //TODO:: add logic
            this.source.set('params.invalid', false);
            this.source.trigger('customCheckoutForm.data.validate');

            if (!this.source.get('params.invalid')) {
                let formData = this.source.get('customCheckoutForm');
            }
        }
    });
});
