var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Perspective_CheckoutCustomization/js/model/checkout-data-resolver': true
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Perspective_CheckoutCustomization/js/view/summary/abstract-total-mixin': true
            }
        }
    }
};
