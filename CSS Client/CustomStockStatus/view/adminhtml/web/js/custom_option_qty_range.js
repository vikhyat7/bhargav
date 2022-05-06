define(['jquery'], function ($) {
    var customOptionRangeStatus = {
        customOptionStatusRangeCount : 0,
        addCustomOptionRange: function ( ruleId,from, to, status, rule) {
            var allCustomStatus = window.customStockStatus;
            var allCustomRules = window.customStockRule;

            ruleId   = 'undefined' != typeof(ruleId)? ruleId :'';
            from   = 'undefined' != typeof(from)? from :'';
            status = 'undefined' != typeof(status)? status :'';
            to     = 'undefined' != typeof(to)? to :'';
            rule   = 'undefined' != typeof(rule)? rule :'';

            var rangeTable = $('#custom_option_range_status_table_body');
            var deleteval = '<input id="deleteRule'+ruleId+'" class="input-text" type="hidden" size="11" name="custom_status_rule_range[' + this.customOptionStatusRangeCount +  '][delete]" value="" />';
            jQuery(deleteval).appendTo(rangeTable);
            var tableRow = jQuery('<tr/>', { id: 'custom_status_rule_range_row_' + this.customOptionStatusRangeCount , class : "custom_status_rule_range_row"}).appendTo(rangeTable);
           
            var fromText = '<input class="input-text" type="hidden" size="11" name="custom_status_rule_range[' + this.customOptionStatusRangeCount +  '][id]" value="' + ruleId + '" />';
                fromText += '<input class="input-text" type="text" size="11" name="custom_status_rule_range[' + this.customOptionStatusRangeCount +  '][from]" value="' + from + '" />';
            
            var tableCell = jQuery('<td/>', {
               html: fromText
            }).appendTo(tableRow);

            var tableCell = jQuery('<td/>', {
                html: '<input class="input-text" type="text" size="11" name="custom_status_rule_range[' + this.customOptionStatusRangeCount +  '][to]" value="' + to + '" />'
            }).appendTo(tableRow);

            var Tabletext = '<select name="custom_status_rule_range[' + this.customOptionStatusRangeCount +  '][option_id]">';

            $.each( allCustomStatus, function( key, item ) {
                selected = (status == item.value)? 'selected="selected"' : '';
                Tabletext += '<option value="' + item.value + '"' + selected + '>' + item.label + '</option>';
            });

            var tableCell = jQuery('<td/>', {
                html: Tabletext
            }).appendTo(tableRow);

            if (allCustomRules && allCustomRules.length > 1) {
                Tabletext = '<select style="min-width:110px;" name="custom_status_rule_range[' + this.customOptionStatusRangeCount +  '][rule_id]">';

                $.each( allCustomRules, function( key, item ) {
                    selected = (rule == item.value)? 'selected="selected"' : '';
                    Tabletext += '<option value="' + item.value + '"' + selected + '>' + item.label + '</option>';
                });

                var tableCell = jQuery('<td/>', {
                    html: Tabletext
                }).appendTo(tableRow);
            }
            
            var tableCell = jQuery('<td/>', {
                html: '<button id="'+ ruleId +'" class="custom-range-status-delete action- scalable delete"><span>Delete</span></button>'
            }).appendTo(tableRow);

            this.customOptionStatusRangeCount++;
        },

        removeCurrentOptionRow: function (element) {
            var count = $(element).attr('id');
            $('#deleteRule'+count).val(count);
            $(element).parents(".custom_status_rule_range_row").remove();
        },
    };

    return customOptionRangeStatus;
});
