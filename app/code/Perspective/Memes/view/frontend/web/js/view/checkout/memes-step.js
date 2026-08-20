define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
    'Perspective_Memes/js/view/meme-picker'
], function (
    ko,
    Component,
    stepNavigator,
    memePickerInit
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perspective_Memes/checkout/memes-select'
        },

        isVisible: ko.observable(false),

        initialize: function () {
            this._super();

            let self = this;

            stepNavigator.registerStep(
                'memes',
                null,
                'Memes',
                self.isVisible,
                self.navigate.bind(self),
                15
            );

            let config = window.checkoutConfig.memesData || {};

            self.isVisible.subscribe(function (visible) {
                if (!visible) {
                    return;
                }

                if (!config.items || !config.items.length) {
                    return;
                }

                let element = document.getElementById('react-meme-picker');

                if (element) {
                    memePickerInit({
                        memes: config,
                        quoteId: window.checkoutConfig.quoteData.entity_id
                    }, element);
                }
            });

            return this;
        },

        navigate: function () {
            this.isVisible(true);
        },

        navigateToNextStep: function () {
            stepNavigator.next();
        },

        navigateBack: function () {
            stepNavigator.navigateTo('shipping');
        }
    });
});
