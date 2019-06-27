/**
 * @file
 * clinic-page-hours.js
 *
 * On clinic pages, use JS to display whether the clinic is currently open
 * or closed, based on timestamps provided in data attributes. This bypasses
 * server-side caching issues.
 *
 * This uses the Moment.js library to format timestamps.
 * @see https://momentjs.com/docs/#/displaying/
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.showCurrentHours = {

    attach: function (context) {

      var $elm = $('[data-uwm-opens-at]', context);

      $elm.each(function (a, b) {

        var $this = $(b);
        var data = $this.data('uwm-opens-at');

        if (!!data.opens_next) {
          var markup = hoursMarkup(data);
          $this.html(markup);
        }

      });

    }

  };

  function uwf() {

    var s = arguments[0],
      i = arguments.length;

    while (i--) {
      s = s.replace(new RegExp('\\{' + i + '\\}', 'gm'), arguments[i]);
    }
    return s;

  }

  function hoursMarkup(data) {

    var now = moment().format('X') * 1;

    // If current time is past the close time for today, use open/close time
    // for the next open day.
    var opens = now > data.closes_today ? data.opens_next : data.opens_today;
    var closes = now > data.closes_today ? data.closes_next : data.closes_today;

    // Day name of today:
    var dayToday = moment().format('dddd');

    // Day name of next open day:
    var dayOpens = moment.unix(opens).format('dddd');

    var markup = '';

    // Closed now:
    if (now < opens) {

      // Opens within the next hour:
      // "Opens soon: 8:00 am - 5:00 pm".
      if (now + 3600 > opens) {

        markup = uwf('<strong>Opens soon:</strong> {1} - {2}',
          moment.unix(opens).format('h:mm a'),
          moment.unix(closes).format('h:mm a')
        );

      }

      // Opens later today:
      // "Closed: Opens today at 8:00 am".
      else if (dayOpens === dayToday) {

        markup = uwf('<strong>Closed:</strong> Opens today at {1}',
          moment.unix(opens).format('h:mm a')
        );

      }

      // Opens a future day:
      // "Closed: Opens Monday at 8:00 am".
      else {

        markup = uwf('<strong>Closed:</strong> Opens {1} at {2}',
          moment.unix(opens).format('dddd'),
          moment.unix(opens).format('h:mm a')
        );

      }

    }

    // Open now:
    if (now > opens && now < closes) {

      // Closes soon:
      // "Closes soon: 5:00 pm".
      if (now + 3600 > closes) {

        markup = uwf('<strong>Closes soon:</strong> {1}',
          moment.unix(closes).format('h:mm a')
        );

      }

      // Open now:
      // "Open: Closes at 5:00 pm".
      else {

        markup = uwf('<strong>Open:</strong> Closes at {1}',
          moment.unix(closes).format('h:mm a')
        );

      }

    }

    return markup;

  }

})(jQuery, Drupal);
