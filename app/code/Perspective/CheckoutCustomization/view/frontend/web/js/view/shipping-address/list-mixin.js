define([  'Magento_Customer/js/model/address-list'], function (addressList) {
    'use strict';

    return function (targetComponent) {
        return targetComponent.extend({

            initChildren: function () {
                if(addressList().length > 4){
                    this.createRendererComponent(addressList()[0],this);
                }else{
                    this._super();
                }
                return this;
            }
        });
    };
});
