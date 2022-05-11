define([
    'underscore',
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magestore_PurchaseOrderCustomization/ui/grid/price_difference'
        },
        getPriceColor: function (row) {
            if (row.price_difference) {
                if(this.convertCurrencyStringToFloat(row.price_difference) !== 0){
                    return 'red';
                }
            }
        },
        convertCurrencyStringToFloat(price) {
            return Number(price.replace(/[^0-9\.-]+/g,""));
        }
    });
});