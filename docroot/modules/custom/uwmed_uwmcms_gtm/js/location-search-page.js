/**
 * @file
 * Behaviors for adding GTM event tracking to the location search page.
 */

(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.uwmedGtmLocationSearchPage = {
    attach: function (context, settings) {
      if (typeof dataLayer == "undefined") {
        return;
      }

      var $context = $(context);

      var searchForm = $('#views-exposed-form-uwm-locations-search-page-1', $context);

      // Add event tracking to the location search submit link.
      searchForm.siblings('.submit-wrapper').find('a.btn-cta.submit').on('click', function (event) {
        dataLayer.push({
          'event': 'search initiation',
          'searchType': 'location',
          'searchLocation': 'location search page',
          'searchTerm': searchForm.find('input[name=s]').val()
        });
      });

      // Add event tracking to the location search results links.
      // There are several links within clinic cards to their full page;
      // all should have rel="bookmark" to easily track them here.
      $('.view-uwm-locations-search .clinic-card a[rel="bookmark"]', $context).each(function (index) {
        var searchTerm = searchForm.find('input[name=s]').val();

        $(this).on('click', function (event) {
          dataLayer.push({
            'event': 'search result click',
            'searchType': 'location',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href')
          });
        });
      });
    }
  };

})(jQuery, Drupal);
