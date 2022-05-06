
define(['jquery'], function ($) {
    'use strict';

   var stockstatusconfigRenderer = {
        configurableStatus: null,
        spanElement: null,
        options: {},
        defaultContents: [],

        init: function (options) {
            this.options = options;
            this.spanElement = $('.stock').first();
            this.dropdowns   = $('select.super-attribute-select, select.swatch-select');

            this._initialization();
        },
         /*
         * configure status
         */
        onConfigure: function (key) {
            var keyCheck = '',
                selectedKey = '';

            this.dropdowns  = $('select.super-attribute-select, select.swatch-select, .swatch-attribute-options:has(.swatch-option)');
            this._hideStockAlertMessage();
            if (null == this.configurableStatus && this.spanElement.length) {
                this.configurableStatus = this.spanElement.html();
            }

            this.settingsForKey = $('select.super-attribute-select, div.swatch-option.selected, select.swatch-select');
            if (this.settingsForKey.length) {
                for (var i = 0; i < this.settingsForKey.length; i++) {
                    if (parseInt(this.settingsForKey[i].value) > 0) {
                        selectedKey += this.settingsForKey[i].value + ',';
                    }

                    if (parseInt($(this.settingsForKey[i]).attr('data-option-id')) > 0) {
                        selectedKey += $(this.settingsForKey[i]).attr('data-option-id') + ',';
                    }
                }
            }
            var trimSelectedKey = selectedKey.substr(0, selectedKey.length - 1);
            var countKeys = selectedKey.split(",").length - 1;

            if (this.options[trimSelectedKey] !== null) {
                this._reloadContent(trimSelectedKey);
            } else {
                this._reloadDefaultContent(trimSelectedKey);
            }

            /*add status to dropdown*/
            var settings = this.dropdowns;
            for (var i = 0; i < settings.length; i++) {
                if (!settings[i].options) {
                    continue;
                }
                for (var x = 0; x < settings[i].options.length; x++) {
                    if (!settings[i].options[x].value) continue;

                    if (countKeys === i + 1) {
                        var keyCheckParts = trimSelectedKey.split(',');
                        keyCheckParts[keyCheckParts.length - 1] = settings[i].options[x].value;
                        keyCheck = keyCheckParts.join(',');

                    } else {
                        if (countKeys < i + 1) {
                            keyCheck = selectedKey + settings[i].options[x].value;
                        }
                    }

                    if ('undefined' !== typeof(this.options[keyCheck]) && this.options[keyCheck]) {
                        var status = this.options[keyCheck]['custom_stock_status_text'];
                        if (status) {
                            status = status.replace(/<(?:.|\n)*?>/gm, ''); // replace html tags
                            if (settings[i].options[x].textContent.indexOf(status) === -1) {
                                if ('undefined' == typeof(this.defaultContents[i + '-' + x])) {
                                    this.defaultContents[i + '-' + x] = settings[i].options[x].textContent;
                                }
                                settings[i].options[x].textContent = this.defaultContents[i + '-' + x] + ' (' + status + ')';
                            }
                        }
                    }
                }
            }

        },


         _initialization: function () {
             var product = this;
            $(document).on('configurable.initialized', function() {
                product.onConfigure();
            });

            $('body').on( {
                    'click': function(){setTimeout(function() { product.onConfigure(); }, 300);}
                },
                'div.swatch-option, select.super-attribute-select, select.swatch-select'
            );

            $('body').on( {
                    'change': function(){setTimeout(function() { product.onConfigure(); }, 300);}
                },
                'select.super-attribute-select, select.swatch-select'
            );
        },


        _reloadContent: function (key) {

            if (this.options.changeConfigurableProductStatus != null
                && this.options.changeConfigurableProductStatus
                && this.spanElement.length
                && !this.spanElement.hasClass('order-observed')
            ) {
                if (this.options[key] && this.options[key]['custom_stock_status']) {
                    if (this.options[key]['custom_stock_status_icon_only'] == 1) {
                        this.spanElement.html(this.options[key]['custom_stock_status_icon']);
                    } else {
                        var stockStatus;
                        if (this.options[key]['custom_stock_status_icon'] != "") {
                            stockStatus = this.options[key]['custom_stock_status_icon'] + this.options[key]['custom_stock_status'];
                        } else {
                            stockStatus = this.options[key]['custom_stock_status'];
                        }
                        this.spanElement.html(stockStatus);
                    }
                } else {
                    this.spanElement.html(this.configurableStatus);
                }
            }

            if ('undefined' !== typeof(this.options[key])
                && this.options[key]
                && 0 == this.options[key]['is_in_stock']
            ) {
                $('.box-tocart').each(function (index,elem) {
                    $(elem).hide();
                });
                if (this.options[key]['stockalertmessage']) {
                    this.showStockAlertMessage(this.options[key]['stockalertmessage']);
                }
            } else {
                $('.box-tocart').each(function (index,elem) {
                    $(elem).show();
                });
            }
        },

        /**
         * Show stock alert Message
         */
        showStockAlertMessage: function (code) {
            $('<div/>', {
                class: 'mageants-config-stockalert',
                title: 'Become a Googler',
                rel: 'external',
                html: code
            }).appendTo('.product-add-form');

            $('#form-validate-stock').mage('validation');
        },

        /**
         * remove stock alert Message
         */
        _hideStockAlertMessage: function () {
            $('.mageants-config-stockalert').remove();
        },

        _reloadDefaultContent: function (key) {
            if (this.spanElement.length
                && !this.spanElement.hasClass('order-observed')
            ) {
                this.spanElement.html(this.configurableStatus);
            }
            $('.box-tocart').show();
        }
    };

    return stockstatusconfigRenderer;
});
