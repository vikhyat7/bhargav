/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magestore_BarcodeSuccess/js/alert',
    'uiRegistry'
], function ($, _, Form, Alert, registry) {
    'use strict';

    return Form.extend({
        defaults: {
            listens: {
                responseData: 'processResponseData'
            }
        },

        /**
         * Process response data
         *
         * @param {Object} data
         */
        processResponseData: function (response) {
            var self = this;
            if(response.success && response.html){
                self.printTransaction(response.html);
            }
            if(response.error && response.messages){
                Alert('Error',response.messages);
            }
        },

        /**
         *
         * @param html
         */
        printTransaction: function(html){
            var print_window = window.open('', 'print_window', 'status=1,width=500,height=500');
            if(print_window){
                print_window.document.open();
                print_window.document.write(html);
                print_window.document.close();
                print_window.addEventListener('load',function(){
                    print_window.print();
                });
            }else{
                Alert('Message','Your browser has blocked the automatic popup, please change your browser settings or print the receipt manually');
            }
        }
    });
});
