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

      var searchFormSelector = '#views-exposed-form-uwm-locations-geo-search-page-1';
      var searchFormInContext = $(searchFormSelector, context);
      var searchFormInPage = $(searchFormSelector);

      // Add event tracking to the location search submit link.
      // In this case, form should be within context, since we are attaching to
      // one of its elements.
      if (searchFormInContext.length > 0) {
        searchFormInContext.siblings('.submit-wrapper').find('a.btn-cta.submit').one('click', function (event) {
          var searchTerm = searchFormInContext.find('input[name=s]').val();
          searchTerm = searchTerm === "" ? "null" : searchTerm;
          var searchLocationTerm = searchFormInContext.find('input[name=l]').val();
          searchLocationTerm = searchLocationTerm === "" ? "null" : searchLocationTerm.toLowerCase();

          dataLayer.push({
            'event': 'search initiation',
            'searchType': 'location',
            'searchLocation': 'location search page',
            'searchTerm': searchTerm,
            'locationTerm': searchLocationTerm
          });
        });
      }

      // Get values for search result tracking. Only need to get these once when
      // the page loads since the page reloads when a new search is performed.
      // In this case, we'll be attaching to card elements in context (below),
      // but the form just needs to be on the page.
      var searchTerm = "null";
      var searchLocationTerm = "null";
      if (searchFormInPage.length > 0) {
        searchTerm = searchFormInPage.find('input[name=s]').val();
        searchTerm = searchTerm === "" ? "null" : searchTerm;
        searchLocationTerm = searchFormInPage.find('input[name=l]').val();
        searchLocationTerm = searchLocationTerm === "" ? "null" : searchLocationTerm.toLowerCase();
      }

      // Add event tracking to the location search results links.
      // There are several links within clinic cards to their full page;
      // all should have rel="bookmark" to easily track them here.
      $('.view-uwm-locations-geo-search .clinic-card a[rel="bookmark"]', context).each(function (index) {
        $(this).on('click', function (event) {
          var featuredClinic = $(this).parents('.view-display-id-featured_block').length > 0;
          var eventType = featuredClinic ? 'featured list click' : 'search result click';
          dataLayer.push({
            'event': eventType,
            'searchType': 'location',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href'),
            'uxElement': 'linktodetail',
            'locationTerm': searchLocationTerm
          });
        });
      });

      $('.clinic-card__street-address-link', context).each(function (index) {
        $(this).on('click', function (event) {
          var featuredClinic = $(this).parents('.view-display-id-featured_block').length > 0;
          var eventType = featuredClinic ? 'featured list click' : 'search result click';
          dataLayer.push({
            'event': eventType,
            'searchType': 'location',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href').replace('#directions-tab', ''),
            'uxElement': 'linktodetail',
            'locationTerm': searchLocationTerm
          });
        })
      });
    }
  };

})(jQuery, Drupal);
