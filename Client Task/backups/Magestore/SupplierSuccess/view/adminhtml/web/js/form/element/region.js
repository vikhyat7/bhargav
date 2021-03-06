/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        initFilter: function () {
            var filter = this.filterBy;
            var country = registry.get(this.parentName + '.' + 'country_id');

            this.filter(country.value(), filter.field);

            /*
             * Clear wrong data from previous version
             *
             * https://github.com/Magestore/SupplierSuccess/issues/12
             */
            if (0 === this.options().length) {
                this.value('');
            }

            this.setLinks({
                filter: filter.target
            }, 'imports');

            return this;
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var country = registry.get(this.parentName + '.' + 'country_id'),
                options = country.indexedOptions,
                option;

            if (!value) {
                return;
            }

            this.filter(value, this.filterBy.field);
        },

        /**
         * @inheritDoc
         */
        clear: function () {
            /*
             * Fix clear options (set default depends on country)
             *
             * https://github.com/Magestore/SupplierSuccess/issues/12
             */
            var filter = this.filterBy;
            var country = registry.get(this.parentName + '.' + 'country_id');
            this.filter(country.value(), filter.field);
            return this._super();
        },

        /**
         * Filters 'initialOptions' property by 'field' and 'value' passed,
         * calls 'setOptions' passing the result to it
         *
         * @param {*} value
         * @param {String} field
         */
        filter: function (value, field) {

            var country = registry.get(this.parentName + '.' + 'country_id'),
                option = country.indexedOptions[value];

            this._super(value, field);

            if (option && option['is_region_visible'] === false) {
                // hide select and corresponding text input field if region must not be shown for selected country
                this.setVisible(false);

                if (this.customEntry) {
                    this.toggleInput(false);
                }
            }
        }
    });
});

