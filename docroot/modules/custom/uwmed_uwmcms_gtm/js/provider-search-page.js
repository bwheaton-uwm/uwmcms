/**
 * @file
 * Behaviors for adding GTM event tracking to the provider search page.
 */

(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.uwmedGtmProviderSearchPage = {
    attach: function (context, settings) {
      if (typeof dataLayer == "undefined") {
        return;
      }

      var searchForm = $('#views-exposed-form-uwm-search-providers-page-1');

      // Add event tracking to the provider search submit link.
      searchForm.siblings('.submit-wrapper').find('a.btn-cta.submit').one('click', function (event) {
        dataLayer.push({
          'event': 'search initiation',
          'searchType': 'provider',
          'searchLocation': 'provider search page',
          'searchTerm': searchForm.find('input[name=s]').val()
        });
      });

      // Add event tracking to the provider search results links.
      $('.view-uwm-search-providers').find('.reader-url, .view-more').each(function (index) {
        var searchTerm = searchForm.find('input[name=s]').val();
        $(this).one('click', function (event) {
          dataLayer.push({
            'event': 'search result click',
            'searchType': 'provider',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href')
          });
        });
      });
    }
  };

})(jQuery, Drupal);
