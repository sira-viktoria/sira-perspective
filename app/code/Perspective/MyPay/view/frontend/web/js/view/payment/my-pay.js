/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'perspectivemypay',
                component: 'Perspective_MyPay/js/view/payment/my-pay-render'
            },
        );
        return Component.extend({});
    }
);
