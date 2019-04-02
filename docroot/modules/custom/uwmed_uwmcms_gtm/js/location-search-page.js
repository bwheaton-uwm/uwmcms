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

      var searchForm = $('#views-exposed-form-uwm-locations-geo-search-page-1', context);

      // Add event tracking to the location search submit link.
      searchForm.siblings('.submit-wrapper').find('a.btn-cta.submit').one('click', function (event) {
        var searchLocationTerm = searchForm.find('input[name=l]').val();
        searchLocationTerm = searchLocationTerm === "" ? "null" : searchLocationTerm.toLowerCase();

        dataLayer.push({
          'event': 'search initiation',
          'searchType': 'location',
          'searchLocation': 'location search page',
          'searchTerm': searchForm.find('input[name=s]').val(),
          'locationTerm': searchLocationTerm
        });
      });

      // Get values for search result tracking. Only need to get these once when
      // the page loads since the page reloads when a new search is performed.
      var searchTerm = searchForm.find('input[name=s]').val();
      var searchLocationTerm = searchForm.find('input[name=l]').val();
      searchLocationTerm = searchLocationTerm === "" ? "null" : searchLocationTerm.toLowerCase();

      // Add phone number click tracking to location card phone numbers.
      $('.view-uwm-locations-geo-search .clinic-card a[href^="tel:"]', context).each(function (index) {
        $(this).one('click', function (event) {
          var clinicLink = $(this).parents('.clinic-card').find('a[rel="bookmark"]').first().attr('href');
          dataLayer.push({
            'event': 'phone number click',
            'searchType': 'location',
            'searchTerm': searchTerm,
            'linkUrl': clinicLink
          });
        });
      });

      // Add event tracking to the location search results links.
      // There are several links within clinic cards to their full page;
      // all should have rel="bookmark" to easily track them here.
      $('.view-uwm-locations-geo-search .clinic-card a[rel="bookmark"]', context).each(function (index) {
        $(this).on('click', function (event) {
          var uxElement = '';

          if ($(this).parents(".clinic-card__image").length > 0) {
            uxElement = 'image';
          }
          else if ($(this).parent().hasClass('clinic-card__name')) {
            uxElement = 'title';
          }
          else if ($(this).parent().hasClass('see-more')) {
            uxElement = 'services';
          }
          else if ($(this).parent().hasClass('clinic-card__cta')) {
            uxElement = 'linktodetail';
          }

          dataLayer.push({
            'event': 'search result click',
            'searchType': 'location',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href'),
            'uxElement': uxElement,
            'locationTerm': searchLocationTerm
          });
        });
      });

      $('.clinic-card__street-address-link', context).each(function (index) {
        $(this).on('click', function (event) {
          dataLayer.push({
            'event': 'search result click',
            'searchType': 'location',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href').replace('#directions-tab', ''),
            'uxElement': 'address',
            'locationTerm': searchLocationTerm
          });
        })
      });
    }
  };

})(jQuery, Drupal);
