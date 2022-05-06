/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magestore_Webpos/js/grid/filters/elements/ui-select-text'
], function (uiSelectText) {
    return uiSelectText.extend({
        textField: ['location_id', 'staff_id']
    });
});
