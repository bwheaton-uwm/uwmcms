/**
 * @file
 * Behaviors for adding GTM event tracking to the header search form.
 */

(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.uwmedGtmHeaderSearchForm = {
    attach: function (context, settings) {
      if (typeof dataLayer == "undefined") {
        return;
      }

      // Add event tracking to the header search form.
      $('.header-search-form', context).one('submit', function (event) {
        var searchTerm = $(this).find('input[name=s]').val();
        dataLayer.push({
          'event': 'search initiation',
          'searchType': 'site',
          'searchLocation': 'header',
          'searchTerm': searchTerm
        });
      });
    }
  };

})(jQuery, Drupal);
