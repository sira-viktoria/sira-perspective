/**
 * LiqPay Extension for Magento 2.
 *
 * @author PerspectiveTeam<order@perspectiveteam.com>
 * © Perspective. All rights reserved
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
                type: 'perspective_liqpay',
                component: 'Perspective_LiqPay/js/view/payment/checkout/liq-pay-render'
            },
        );
        return Component.extend({});
    }
);
