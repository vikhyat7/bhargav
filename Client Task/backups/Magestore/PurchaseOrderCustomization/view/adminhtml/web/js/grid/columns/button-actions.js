/*
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/grid/columns/actions'
], function (_, registry, Actions) {
    'use strict';

    return Actions.extend({
        defaults: {
            bodyTmpl: 'Magestore_PurchaseOrderCustomization/grid/cells/buttonActions',
        },

        /**
         * Creates action callback for multiple actions.
         *
         * @private
         * @param {Object} action - Actions' object.
         * @returns {Function} Callback function.
         */
        _getCallbacks: function (action) {
            var callback = action.callback,
                callbacks = [],
                tmpCallback;

            _.each(callback, function (cb) {
                var params = _.compact([cb.target]);
                if(cb.params && _.isArray(cb.params)){
                    _.each(cb.params, function (arg) {
                        params.push(arg);
                    });
                }else{
                    params = _.compact([cb.target, cb.params]);
                }
                tmpCallback = {
                    action: registry.async(cb.provider),
                    args: params
                };
                callbacks.push(tmpCallback);
            });

            return function () {
                _.each(callbacks, function (cb) {
                    cb.action.apply(cb.action, cb.args);
                });
            };
        },
    });
});
