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

      var searchForm = $('#views-exposed-form-uwm-locations-search-page-1');

      // Add event tracking to the location search submit link.
      searchForm.siblings('.submit-wrapper').find('a.btn-cta.submit').one('click', function (event) {
        dataLayer.push({
          'event': 'search initiation',
          'searchType': 'location',
          'searchLocation': 'location search page',
          'searchTerm': searchForm.find('input[name=s]').val()
        });
      });

      // Add event tracking to the location search results links.
      $('.view-uwm-locations-search').find('.reader-url, .view-more').each(function (index) {
        var searchTerm = searchForm.find('input[name=s]').val();
        $(this).one('click', function (event) {
          dataLayer.push({
            'event': 'result click',
            'searchType': 'location',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href')
          });
        });
      });
    }
  };

})(jQuery, Drupal);
