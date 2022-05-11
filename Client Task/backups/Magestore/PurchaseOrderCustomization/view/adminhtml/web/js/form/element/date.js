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
  'Magento_Ui/js/form/element/date',
  'moment',
  'jquery',
  'mageUtils'
], function(_, registry, Actions, moment, $, utils) {
  'use strict';


  return Actions.extend({
    defaults: {
      options: {},

      storeTimeZone: 'UTC',

      validationParams: {
        dateFormat: '${ $.outputDateFormat }',
      },

      /**
       * Format of date that comes from the
       * server (ICU Date Format).
       *
       * Used only in date picker mode
       * (this.options.showsTime == false).
       *
       * @type {String}
       */
      inputDateFormat: 'y-MM-dd',

      /**
       * Format of date that comes from the
       * server (ICU Date Format).
       *
       * Used only in date/time picker mode
       * (this.options.showsTime == false).
       *
       * @type {String}
       */
      inputDateTimeFormat: 'y-MM-dd h:mm',

      /**
       * Format of date that should be sent to the
       * server (ICU Date Format).
       *
       * Used only in date picker mode
       * (this.options.showsTime == false).
       *
       * @type {String}
       */
      outputDateFormat: 'DD/MM/YYYY',

      /**
       * Format of date that should be sent to the
       * server (ICU Date Format).
       *
       * Used only in datetime picker mode with disabled ISO format.
       * (this.options.showsTime == true, this.options.outputDateTimeToISO ==
       * false)
       *
       * @type {String}
       */
      outputDateTimeFormat: '',

      /**
       * Converts output date/time to ISO string
       *
       * Used only in datetime picker mode
       * (this.options.showsTime == false)
       */
      outputDateTimeToISO: true,

      /**
       * Date/time format that is used to display date in
       * the input field.
       *
       * @type {String}
       */
      pickerDateTimeFormat: 'DD/MM/YYYY',

      pickerDefaultDateFormat: 'DD/MM/YYYY', // ICU Date Format
      pickerDefaultTimeFormat: 'h:mm a', // ICU Time Format

      elementTmpl: 'ui/form/element/date',

      /**
       * Format needed by moment timezone for conversion
       */
      timezoneFormat: 'YYYY-MM-DD HH:mm',

      listens: {
        // 'value': 'onUpdateValueFromGrid',
        'shiftedValue': 'onShiftedValueChange',
        'gridValue': 'onUpdateValueFromGrid',
      },

      /**
       * Date/time value shifted to corresponding timezone
       * according to this.storeTimeZone property. This value
       * will be sent to the server.
       *
       * @type {String}
       */
      shiftedValue: '',
      gridValue: '',
    },

    onUpdateValueFromGrid: function(value) {
      var shiftedValue = moment(value, this.inputDateFormat);
      if (shiftedValue._isValid) {
        shiftedValue = shiftedValue.format('DD/MM/YYYY');
        var name = this.dataScope;
        name = name.replace('data.', '');
        // $('input[name=' + this.index + ']').val(shiftedValue);
        $('input[name=' + name + ']').val(shiftedValue);
        this.shiftedValue(shiftedValue);
      } else {
        this.value(value);
      }
    },

    /**
     * Prepares and converts all date/time formats to be compatible
     * with moment.js library.
     */
    prepareDateTimeFormats: function () {
      this.options.dateFormat = 'd/M/yy';
      this.pickerDateTimeFormat = this.options.dateFormat;

      if (this.options.showsTime) {
        this.pickerDateTimeFormat += ' ' + this.options.timeFormat;
      }

      this.pickerDateTimeFormat = utils.convertToMomentFormat(this.pickerDateTimeFormat);

      if (this.options.dateFormat) {
        this.outputDateFormat = this.options.dateFormat;
      }

      this.inputDateFormat = utils.convertToMomentFormat(this.inputDateFormat);
      this.outputDateFormat = utils.convertToMomentFormat(this.outputDateFormat);

      this.validationParams.dateFormat = this.outputDateFormat;
    }

  });
});