define([
    'jquery',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Customer/js/customer-data'
], function ($, cartCache, defaultTotal, customerData) {

    $(document).ready(function(){

        $(document).on('change', 'input[name$="[qty]"]', function(){
            let form = $('form#form-validate');
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                showLoader: true,
                success: function (res) {
                    //cart form reload (item subtotal reload)
                    let parsedResponse = $.parseHTML(res);
                    let result = $(parsedResponse).find("#form-validate");
                    $("#form-validate").replaceWith(result);

                    // recollect totals
                    cartCache.set('totals',null);
                    defaultTotal.estimateTotals();

                },
                error: function (xhr) {
                    console.error('AJAX cart update error:', xhr.responseText);
                }
            });
        });

        $(document).on('change', '#coupon_code', function(){
            let form = $('#discount-coupon-form');
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                showLoader: true,
                success: function (res) {
                    let parsedResponse = $.parseHTML(res);
                    let couponForm = $(parsedResponse).find("#discount-coupon-form");
                    $("#discount-coupon-form").replaceWith(couponForm);

                    let cartForm = $(parsedResponse).find("#form-validate");
                    $("#form-validate").replaceWith(cartForm);

                    cartCache.set('totals', null);
                    defaultTotal.estimateTotals();

                },
                error: function (xhr) {
                    console.error('AJAX coupon apply error:', xhr.responseText);
                }
            });
        });
    });
});
