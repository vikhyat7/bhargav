/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function (registry, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            skipValidation: false,
            imports: {
                update: '${ $.parentName }.change_qty:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var changeQty = registry.get(this.parentName + '.' + 'change_qty');
            var oldQty = registry.get(this.parentName + '.' + 'total_qty');
            this.value(parseFloat(changeQty.value()) + parseFloat(oldQty.value()));
        }
    });
});

