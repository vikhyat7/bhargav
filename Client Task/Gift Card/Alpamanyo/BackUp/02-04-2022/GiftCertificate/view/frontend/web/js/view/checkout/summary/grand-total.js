/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'Magento_Tax/checkout/summary/grand-total'
            },
            totals: quote.getTotals(),
            isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,
            isDisplayed: function() {
                return this.isFullMode();
            },
            getValue: function() {
                var price = 0;
                if (this.totals()) {
                    /*if(totals.getSegment('grand_total').value!==0){
                        var giftvalue = Math.abs(totals.getSegment('giftcertificate').value);
                        if(totals.getSegment('grand_total').value <  giftvalue){
                            price = totals.getSegment('grand_total').value;     
                        }
                        else{
                            if ((parseFloat(totals.getSegment('grand_total').value) - parseFloat(totals.getSegment('giftcertificate').value)) <= parseFloat(totals.getSegment('subtotal').value)) 
                            {
                                price = totals.getSegment('grand_total').value; 
                            }
                            else{
                                price = parseFloat(totals.getSegment('grand_total').value) + parseFloat(totals.getSegment('giftcertificate').value); 
                            }    
                        }
                        
                    }
                    else{*/
                        price = totals.getSegment('grand_total').value; 
                    //}   
                }
                return this.getFormattedPrice(price);
            },
            getBaseValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = this.totals().base_grand_total;
                }
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            },
            getGrandTotalExclTax: function() {
                var totals = this.totals();
                if (!totals) {
                    return 0;
                }
                return this.getFormattedPrice(totals.grand_total);
            },
            isBaseGrandTotalDisplayNeeded: function() {
                var totals = this.totals();
                if (!totals) {
                    return false;
                }
                return totals.base_currency_code != totals.quote_currency_code;
            }
        });
    }
);
