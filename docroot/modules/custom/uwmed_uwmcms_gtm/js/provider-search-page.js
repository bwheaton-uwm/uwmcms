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

      var searchForm = $('#views-exposed-form-uwm-providers-search-page-1', context);
      var searchTerm = searchForm.find('input[name=s]').val();

      var searchInitiationEvent = {
        'event': 'search initiation',
        'searchType': 'provider',
        'searchLocation': 'provider search page'
      };

      var acceptNewPatientsFilterEvent = {
        'event': 'accept new patients filter',
        'searchType': 'provider',
        'searchLocation': 'provider search page'
      };

      // Add event tracking to the provider search submit link.
      searchForm.siblings('.submit-wrapper').find('a.btn-cta.submit:visible').one('click', function (event) {
        var acceptingPatientsChecked = searchForm.siblings(".dm-facet-id-provider_facet_accepting_patients")
        .find("input[value='accepting-patients:1']:checked");

        searchInitiationEvent.searchTerm = searchForm.find('input[name=s]').val();

        if (acceptingPatientsChecked.length > 0) {
          dataLayer.push(searchInitiationEvent);
          dataLayer.push(acceptNewPatientsFilterEvent);
        }
        else {
          dataLayer.push(searchInitiationEvent);
        }
      });

      // Add event tracking to the provider search results links (image, title, cta).
      $('.view-uwm-providers-search', context).find('.view-mode-provider_card a[rel="bookmark"], .view-mode-provider_card .provider-card__image a').each(function (index) {
        $(this).one('click', function (event) {
          dataLayer.push({
            'event': 'search result click',
            'searchType': 'provider',
            'searchTerm': searchTerm,
            'linkURL': $(this).attr('href'),
            'uxElement': 'linktodetail'
          });
        });
      });

      // Add event tracking to the provider search locations links.
      $('.view-uwm-providers-search .provider-card__locations a', context).each(function (index) {
        $(this).one('click', function (event) {
          var providerLink = $(this).parents('.view-mode-provider_card').find('a[rel="bookmark"]').first().attr('href');
          dataLayer.push({
            'event': 'search result click',
            'searchType': 'provider',
            'searchTerm': searchTerm,
            'linkURL': providerLink,
            'uxElement': 'linktodetail'
          });
        });
      });

      // Add phone number click tracking to provider card phone numbers.
      $('.view-uwm-providers-search .view-mode-provider_card a[href^="tel:"]', context).each(function (index) {
        $(this).one('click', function (event) {
          var providerLink = $(this).parents('.view-mode-provider_card').find('a[rel="bookmark"]').first().attr('href');
          dataLayer.push({
            'event': 'phone number click',
            'searchType': 'provider',
            'searchTerm': searchTerm,
            'linkURL': providerLink
          });
        });
      });
    }
  };

})(jQuery, Drupal);
