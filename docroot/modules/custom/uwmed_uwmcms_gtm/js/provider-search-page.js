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

      var searchInitiationEvent = {
        'event': 'search initiation',
        'searchType': 'provider',
        'searchLocation': 'provider search page',
        'searchTerm': searchForm.find('input[name=s]').val()
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

        if (acceptingPatientsChecked.length > 0) {
          dataLayer.push(searchInitiationEvent);
          dataLayer.push(acceptNewPatientsFilterEvent);
        }
        else {
          dataLayer.push(searchInitiationEvent);
        }
      });

      // Add event tracking to the provider search results links.
      $('.view-uwm-providers-search').find('.reader-url, .view-more', context).each(function (index) {
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
