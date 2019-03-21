/**
 * @file
 * Behaviors for adding GTM event tracking to the site search page.
 */

(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.uwmedGtmSiteSearchPage = {
    attach: function (context, settings) {
      if (typeof dataLayer == "undefined") {
        return;
      }

      var searchForm = $('#views-exposed-form-uwm-general-search-page-1', context);

      // Add event tracking to the site search submit link.
      searchForm.siblings('.submit-wrapper').find('a.btn-cta.submit').one('click', function (event) {
        dataLayer.push({
          'event': 'search initiation',
          'searchType': 'site',
          'searchLocation': 'site search page',
          'searchTerm': searchForm.find('input[name=s]').val()
        });
      });

      // Add event tracking to the site search results links.
      $('.view-uwm-general-search').find('.reader-url, .view-more', context).each(function (index) {
        var searchTerm = searchForm.find('input[name=s]').val();
        $(this).one('click', function (event) {
          dataLayer.push({
            'event': 'search result click',
            'searchType': 'site',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href')
          });
        });
      });
    }
  };

})(jQuery, Drupal);
