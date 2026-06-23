define([
    'uiComponent',
    'ko',
    'mage/storage',
    'Magento_Checkout/js/model/totals',
    'mage/url'
], function (Component, ko, storage, totals, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perspective_WeatherInsurance/view/checkout/totals/weather-insurance-total'
        },

        isInsuranceSelected: ko.observable(false),
        insurancePrice: ko.observable(0),
        isVisible: ko.observable(false),

        initialize: function () {
            this._super();

            var config = window.checkoutConfig.weatherInsurance;
            if (config) {
                this.isVisible(config.isVisible);
                this.insurancePrice(config.insurancePrice);
            }

            return this;
        },

        /**
         * Метод, який ГАРАНТОВАНО викликається при кліку на чекбокс
         */
        toggleInsurance: function (data, event) {
            // Оскільки це подія click, Knockout змінює значення автоматично,
            // але нам потрібно взяти поточний стан чекбоксу після кліку
            var currentStatus = event.target.checked;
            this.isInsuranceSelected(currentStatus);

            // Формуємо надійний URL через baseUrl з глобального конфігу чекауту
            var baseUrl = window.checkoutConfig.checkoutUrl.replace('checkout/', '');
            // var serviceUrl = baseUrl + 'shippinginsurance/insurance/save';
            var serviceUrl = urlBuilder.build('perspective/ajax/saveToQuote');

            totals.isLoading(true);

            storage.post(

                serviceUrl,
                JSON.stringify({ insurance_checkbox_state: currentStatus }),
                false
            ).done(function (response) {
                if (response.success) {
                    // Коли додамо Total Collector, цей виклик оновить блок цін (Summary)
                    totals.isLoading(false);
                } else {
                    totals.isLoading(false);
                    console.error('Помилка сервера:', response.message);
                }
            }).fail(function (xhr) {
                totals.isLoading(false);
                console.error('Критична помилка AJAX-запиту:', xhr);
            });

            // Повертаємо true, щоб Knockout дозволив чекбоксу візуально переключитися
            return true;
        },

        getFormattedPrice: function () {
            return '+' + this.insurancePrice().toFixed(2) + ' ₴';
        }
    });
});
