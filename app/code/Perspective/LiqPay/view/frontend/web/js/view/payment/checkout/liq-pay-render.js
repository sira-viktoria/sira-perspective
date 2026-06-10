/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
 */
define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'mage/url',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function (Component, $, url) {
        'use strict';

        return Component.extend({
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Perspective_LiqPay/payment/checkout/liq-pay'
            },

            getCode: function() {
                return 'perspective_liqpay';
            },

            isActive: function() {
                return true;
            },

            validate: function() {
                let $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },

            afterPlaceOrder: function () {
                $.post(url.build('liqpay/checkout/form'), {
                    'random_string': this._generateRandomString(30)
                }).done(function(data) {
                    if (!data.status) {
                        return
                    }
                    if (data.status === 'success') {
                        if (data.content) {
                            let html = '<div id="liqPaySubmitFrom" style="display: none;">' + data.content + '</div>';
                            $('body').append(html);
                            $('#liqPaySubmitFrom form:first').submit();
                        }
                    } else {
                        if (data.redirect) {
                            window.location = data.redirect;
                        }
                    }
                });
            },

            _generateRandomString: function(length) {
                if (!length) {
                    length = 10;
                }
                let text = '';
                let possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                for (let i = 0; i < length; ++i) {
                    text += possible.charAt(Math.floor(Math.random() * possible.length));
                }
                return text;
            }
        });
    }
);
