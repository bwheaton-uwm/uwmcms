/**
 * @file
 * Shows the clinic wait-times.
 *
 * Fetches results from ClockwiseMd.com and appends
 * a formatted wait-time and wait-link to our container tag.
 *
 * To use, you need a container tag that has a
 * 'data-uwm-clockwise-wait-time' attribute with the value of the
 * clinic's clockwise ID (field_res_clockwise_id).
 *
 * Two elements can be included in that container with the classes:
 * 'js-wait-link' and 'js-wait-text'.
 *
 * Once the callback from ClockwiseMD.com is received, the
 * 'js-wait-link' element will get an href attribute pointing to the clinic's
 * 'get in line' functionality on ClockwiseMD.com. The 'js-wait-text' element
 * will get HTML that includes the current wait time or 'closed'.
 *
 * @example
 * {{ attach_library('uwmbase/clockwise-wait-times') }}
 * <div data-uwm-clockwise-wait-time="[clockwise-id]">
 *   <p class="js-wait-link"><a href="">Get in line</a></p>
 *   <p class="js-wait-text">&nbsp;</p>
 * </div>
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Map of clockwise API options.
   */
  const options = {
    timeBuffer: 0,
    displayStyle: 'plain',
    refreshInterval: (1000 * 60 * 5),
  };

  /**
   * Behavior helpers.
   */

  function startClockwiseRepeatingCheck(id) {

    if (!id || typeof window.Clockwise.CurrentWait !== 'function') {
      return;
    }

    // Execute global ClockWise Md callback function:
    window.Clockwise.CurrentWait(id, 'html'); // ('json' : 'html')

    setTimeout(function () {
      startClockwiseRepeatingCheck(id);
    }, options.refreshInterval);
  }

  function getClockwiseWaitUri(id) {

    return 'https://www.clockwisemd.com/hospitals/' +
        id + '/appointments/new';

  }

  function cleanNumber(value) {

    if (/^(-|\+)?([0-9]+|Infinity)$/.test(value)) {
      return Number(value);
    }
    return 0;

  }

  function makeHoursAndMinutes(minutes, shortFormat) {

    minutes = cleanNumber(minutes);
    let remainder = cleanNumber(minutes % 60),
        hours = (minutes - remainder) / 60;

    // 45 mins.
    if (minutes < 60) {
      if (shortFormat) {
        return minutes + ' mins';
      }
      return minutes + ' minute wait';
    }

    // 1 hr.
    if (minutes === 60) {
      if (shortFormat) {
        return '1 hr';
      }
      return '1 hour';
    }

    // 1 hr 45 mins.
    if (minutes > 60 && minutes < 120) {
      if (shortFormat) {
        return hours + ' hr ' + remainder + ' mins';
      }
      return hours + ' hour ' + remainder + ' minute wait';
    }

    // 4 hrs.
    if (minutes > 60 && remainder === 0) {
      if (shortFormat) {
        return hours + ' hrs';
      }
      return hours + ' hours';
    }

    // 2 hrs 25 mins.
    if (shortFormat) {
      return hours + ' hrs ' + remainder + ' mins';
    }
    return hours + ' hours ' + remainder + ' minute wait';

  }

  function getClockwiseWaitTime(cwResult) {

    let formattedTime = 'Please call';

    let tt = cleanNumber($(cwResult).text()),
        tp = cleanNumber(0);

    if (options.displayStyle === 'plain') {

      if (!cwResult) {
        formattedTime = 'Please call for wait-times';
      }
      else if (cwResult.indexOf('n/a') > -1) {
        formattedTime = 'Please call for wait-times';
      }
      else if (cwResult.indexOf('closed') > -1) {
        formattedTime = 'Closed';
      }
      else if (tt === 0) {
        formattedTime = makeHoursAndMinutes(tt);
      }
      else if (tt >= 0 && tp > 0) {
        formattedTime = makeHoursAndMinutes(tt) +
            ' - ' + makeHoursAndMinutes((tt + tp));
      }
      else if (tt >= 0) {
        formattedTime = makeHoursAndMinutes(tt);
      }
      else {
        formattedTime = 'Please call for wait-times';
      }

      return formattedTime;

    }

  }

  Drupal.behaviors.uwmShowClockwiseWaitTime = {

    attach: function (context) {

      window.Clockwise = window.Clockwise || {};
      window.Clockwise.Waits = window.Clockwise.Waits || {};

      $('[data-uwm-clockwise-wait-time]', context).each(function () {

        let id = $(this).attr('data-uwm-clockwise-wait-time');

        if (id > 0) {

          $(this).attr('data-uwm-clockwise-wait-time', id)
              .attr('data-uwm-clockwise-refresh-start', new Date().toLocaleString());
          startClockwiseRepeatingCheck(id);

        }

      });

      // ClockwiseMd.com triggers a jsonp event. When it fires,
      // update element's wait string:
      $('body').on('clockwise_waits_loaded', function (e, data) {

        let id = cleanNumber(data);
        let $elm = $('[data-uwm-clockwise-wait-time=' + id + ']');

        if ($elm.length && window.Clockwise.Waits[id] !== 'undefined') {

          let snippet = getClockwiseWaitTime(
              window.Clockwise.Waits[id].toLowerCase());

          if (snippet.length > 0) {

            $elm.find('.js-wait-text').html(snippet);
            $elm.find('.js-wait-link, .js-wait-link a').attr('href', getClockwiseWaitUri(id));
            $elm.attr('data-uwm-clockwise-refresh-end', new Date().toLocaleString())
                .removeClass('fade-out invisible hidden');

          }

        }

      });

    }

  };

})(jQuery, Drupal);
