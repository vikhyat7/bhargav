/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*jshint browser:true jquery:true*/
/*global alert*/
define([
        'jquery',
        'rjsResolver'
    ], function ($, resolver) {
        'use strict';

        var containerId = '#html-body';

        return {

            /**
             * Start full page loader action
             */
            startLoader: function () {
                $(containerId).trigger('processStart');
            },

            /**
             * Stop full page loader action
             *
             * @param {Boolean} forceStop
             */
            stopLoader: function (forceStop) {
                var $elem = $(containerId),
                    stop = $elem.trigger.bind($elem, 'processStop');

                forceStop ? stop() : resolver(stop);
            }
        };
    }
);
