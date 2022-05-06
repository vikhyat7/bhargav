/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

define(
    [
        'jquery'
    ], function ($) {
        "use strict";

        function zeroPad (num) {
            return String(num).padStart(2, '0');
        }

        $.widget(
            'mageants.maCountdownTimer',
            {
                _create: function () {
                    this.setCountdown(
                        $(this.options.timer_id),
                        this.options.timezone,
                        this.options.endTime,
                        this.options.currentTime,
                        this.options.baseUrl,
                        this.options.autoSwitch
                    );
                },

                setCountdown: function (element, timezone, endTime, currentTime, baseUrl, autoSwitch) {

                    // Set the date we're counting down to
                    var countDownHandler,
                        timeNow     = new Date(currentTime).getTime(),
                        timeEnd     = new Date(endTime).getTime(),
                        daysSpan    = element.find('.ma-countdown-days'),
                        hoursSpan   = element.find('.ma-countdown-hours'),
                        minutesSpan = element.find('.ma-countdown-minutes'),
                        secondsSpan = element.find('.ma-countdown-seconds'),
                        dataSpan    = element.find('.ma-countdown-data');

                    if (timeEnd < timeNow) {
                        dataSpan.text(zeroPad(0));
                    }
                    // Update the count down every 1 second
                    countDownHandler = setInterval(
                        function () {
                            // Get from date and time
                            var days, hours, minutes, seconds,
                                newDate    = new Date(),
                                formatDate = newDate.toLocaleString('en-US', {timeZone: timezone}),
                                now        = Date.parse(formatDate),
                                distance   = -1;

                            if (timeEnd > now) {
                                distance = timeEnd - now;
                            }

                            // Time calculations for days, hours, minutes and seconds
                            days    = Math.floor(distance / (1000 * 60 * 60 * 24));
                            hours   = Math.floor(distance % (1000 * 60 * 60 * 24) / (1000 * 60 * 60));
                            minutes = Math.floor(distance % (1000 * 60 * 60) / (1000 * 60));
                            seconds = Math.floor(distance % (1000 * 60) / 1000);

                            // Output the result in element
                            daysSpan.text(zeroPad(days));
                            hoursSpan.text(zeroPad(hours));
                            minutesSpan.text(zeroPad(minutes));
                            secondsSpan.text(zeroPad(seconds));

                            // If the count down is over, hide countdown
                            if (distance < 0) {
                                clearInterval(countDownHandler);
                                dataSpan.text(zeroPad(0));
                                if (autoSwitch !== 0) {
                                    window.location.href = baseUrl;
                                }
                            }
                        },
                        1000
                    );
                }
            }
        );

        return $.mageants.maCountdownTimer;
    }
);
