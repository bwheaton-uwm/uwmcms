/**
 * @file
 * Script to geocode text in search form address field.
 *
 * Script to take the address a user has typed in our location search form,
 * and to query Google's Geocode API for the best possible location match. We
 * then use the latitude/ longitude for a Drupal locations search.
 *
 * We use GTM (Google Tag Manager) to store a JSON array of
 * search terms and replacement values, for the address form field.
 *
 * If the GTM JSON array is available, it should look like:
 *
 * @code
 * const uwdm_gtm_search_location_keywords_replacements = [
 {
    "search_keywords": "Ravenna",
    "replacement_keywords": "Ravenna, Seattle, WA",
    "match_full_text_only": "TRUE"
  }, {
    "search_keywords": "Ballard",
    "replacement_keywords": "Ballard, Seattle, WA",
    "match_full_text_only": "TRUE"
  }
 ];
 *
 * @endcode
 */

(function ($, Drupal) {

  /**
   * Preferred bounding box for results.
   *
   * @see https://developers.google.com/maps/documentation/geocoding/intro#Viewports
   * @see https://developers.google.com/maps/documentation/javascript/reference/coordinates#LatLngBoundsLiteral
   *
   * @type {{lat: number, lng: number}[]}
   */
  const GOOGLE_FILTER_BOUNDING_BOX = [{
    lat: 46.709241,
    lng: -123.422571
  }, {
    lat: 48.254976,
    lng: -119.381319
  }];

  /**
   * Limit results to these criteria.
   *
   * @see https://developers.google.com/maps/documentation/geocoding/intro#ComponentFiltering
   * @type {string}
   */
    // GOOGLE_FILTER_COMPONENTS = 'administrative_area_level_1:WA|country:US'.
  const GOOGLE_FILTER_COMPONENTS = '';

  /**
   * Text to set in input when user clicks to use their current location.
   *
   * @type {string}
   */
  const currentLocationText = 'Current location';

  /**
   * Text to set temporarily in input while browser is finding current location.
   *
   * @type {string}
   */
  const locationLoadingText = 'Retrieving your location...';

  /**
   * Attach behaviors once Drupal readies page.
   *
   * @type {{attach(*, *): void}}
   */
  Drupal.behaviors.uwmGeocodeInputInit = {

    attach(context, settings) {

      const $form = $('section.content-topper form[id*="uwm-locations-geo-search"]', context);

      if (!$form.length) {
        return;
      }

      const $wrapper = $form.parent('.filters-wrap');
      const $addressContainer = $form.find('.location-address-keywords');
      const $addressInput = $addressContainer.find('input[name=l]');
      const $currentLocationDropdown = $addressContainer.find('.field-suffix .dropdown');
      const $currentLocationDropdownMenu = $addressContainer.find('.field-suffix .dropdown-menu');
      const $currentLocationDropdownToggle = $addressContainer.find('.field-suffix .toggle-uml-dropdown');
      const $useMyLocationLink = $addressContainer.find('.dropdown a');
      const $latlngHiddenInput = $form.find('input[name=latlng]');

      // Set CSS classes on load to indicate if geocoded or current-location
      // search is active.
      if ($latlngHiddenInput.length && $latlngHiddenInput.val().length > 0) {
        $("body").addClass("search-with-geocoding");

        if ($addressInput.length && $addressInput.val() === currentLocationText) {
          $("body").addClass("search-with-current-location");
        }
      }

      /**
       * Open the "Use my location" dropdown, if hidden.
       */
      const openDropdown = function () {
        if ($currentLocationDropdownMenu.is(':hidden')) {
          $currentLocationDropdown.addClass('uwm-display-dropdown');
          $addressContainer.addClass('active');
          $currentLocationDropdownToggle.attr('aria-expanded', 'true');
        }
      };

      /**
       * Close the "Use my location" dropdown, if open.
       */
      const closeDropdown = function () {
        if ($currentLocationDropdownMenu.is(':visible')) {
          $currentLocationDropdown.removeClass('uwm-display-dropdown');
          $addressContainer.removeClass('active');
          $currentLocationDropdownToggle.attr('aria-expanded', 'false');
        }
      };

      // On current-location icon click, toggle the dropdown.
      // Note: this operates regardless of whether the value is empty (as other
      // show/hide logic does), so that the icon is a consistent user control.
      $currentLocationDropdownToggle.on('click', e => {
        e.preventDefault();

        if ($currentLocationDropdownMenu.is(':hidden')) {
          // Previously, this set focus on the address input, which both opened
          // the dropdown and conveniently placed focus for user to type. But
          // now the focus handler opens the dropdown conditionally, so call it
          // directly here.
          openDropdown();
        }
        else {
          closeDropdown();
        }
      });

      // On address input focus, open dropdown - if value is empty. (This
      // corresponds with handling on the `input` event below.)
      $addressInput.on('focus', e => {
        if ($addressInput.val().length === 0) {
          openDropdown();
        }
      });

      // If user types in address input, presume this means they opted to search
      // by an entered value, not current location - so close dropdown; if user
      // clears value, re-open dropdown.
      // Use `input` event to track value change - i.e. upon character entered
      // (vs. `change` which tracks value "committed"). This event is supported
      // in relevant browsers for 'text' input type.
      // @see https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/input_event
      $addressInput.on('input', e => {

        // Check at a slight delay, with a flag to avoid overlapping checks.
        if (!$addressInput.data('checkingTyped')) {
          $addressInput.data('checkingTyped', true);

          setTimeout(() => {
            if ($addressInput.val().length === 0) {
              openDropdown();
            }
            else {
              closeDropdown();
            }

            $addressInput.data('checkingTyped', false);
          }, 300);
        }

      });

      // On address input blur, call geocoding.
      // It will bypass if current location was selected, or if empty.
      $addressInput.on('blur', e => {
        getGeocodeResponse($addressInput.val());
      });

      // On Use-my-location dropdown link click, request user location via
      // browser and close dropdown.
      $useMyLocationLink.on('click', e => {
        e.preventDefault();
        closeDropdown();
        getNavigatorUserLocation();
      });

      // On Use-my-location link blur, close the dropdown.
      $useMyLocationLink.on('blur', e => {
        closeDropdown();
      });

      // On any 'focusin' event within the form wrapper, if the element being
      // focused is not within the address container, close dropdown.
      $wrapper.on('focusin', e => {

        if ($addressContainer.find($(e.target)).length === 0) {
          closeDropdown();
        }

      });

      // Geolocation functions:
      /**
       *
       * @param queryString
       */
      const getGeocodeResponse = function (queryString) {

        // When user clicks "Use my location" link, it populates the input with
        // "Current location" (if successful). Do not geocode this text.
        if (queryString === currentLocationText) {
          return;
        }

        if (!queryString) {
          clearUserLocation();
          return;
        }

        // In ./components/search/search.js we listen for ajax requests before
        // submitting the search form. We aren't using $.ajax here, but let's
        // flag an open XHR, to prevent submitting the form before
        // lat/ long are added to the hidden search field.
        $.active += 1;

        const geocoder = new google.maps.Geocoder();

        geocoder.geocode({
          'address': queryString,
          'bounds': google.maps.LatLngBounds(
            new google.maps.LatLng(GOOGLE_FILTER_BOUNDING_BOX[0].lat, GOOGLE_FILTER_BOUNDING_BOX[0].lng),
            new google.maps.LatLng(GOOGLE_FILTER_BOUNDING_BOX[1].lat, GOOGLE_FILTER_BOUNDING_BOX[1].lng)
          )

        }, (results, status) => {

          if (status == 'OK') {

            const item = results[0];
            if (item.geometry && item.geometry.location) {
              handleGeocodeSuccess(null, item.geometry.location.lat(), item.geometry.location.lng());
            }
            else {
              handleGeocodeError();
            }

          }
          else {
            handleGeocodeError();
          }

          // Flag that the XHR has completed.
          $.active -= 1;
          $(document).trigger("ajaxComplete");

        });

      };

      /**
       * Request user's location via browser.
       */
      const getNavigatorUserLocation = function () {

        if (!navigator.geolocation) {

          // TODO? Consider setting a distinct error message here for user.
          handleGeocodeError();

        }
        else {
          $addressInput.val(locationLoadingText);
          $addressInput.prop('disabled', true);

          // Per suggestion, set enableHighAccuracy to false, to increase
          // likelihood that IE/Windows will allow it (re: privacy settings).
          // @see https://stackoverflow.com/questions/43206442/geolocation-current-position-api-not-working-in-ie11-5-windows10
          const options = {};

          if (navigator.userAgent.indexOf('MSIE ') !== -1 || navigator.userAgent.indexOf('Trident/') !== -1) {
            options.enableHighAccuracy = false;
          }

          navigator.geolocation.getCurrentPosition((position) => {

            handleGeocodeSuccess(currentLocationText, position.coords.latitude, position.coords.longitude);
            $("body").addClass("search-with-current-location");

          }, (err) => {

            // TODO? Consider setting a distinct error message here for user.
            // `err.message` contains a human-readable message, e.g. "This site
            // does not have permissiont o use the Geolocation API.
            handleGeocodeError();
          }, options);
        }

      };

      /**
       * Update UI and form values upon successful lat/lng retrieval.
       *
       * @param updateInputText
       * @param lat
       * @param lng
       *
       * @return {*}
       */
      const handleGeocodeSuccess = function (updateInputText, lat, lng) {

        clearUserLocation();

        $addressInput.prop('disabled', false);

        if (updateInputText) {
          $addressInput.val(updateInputText);
        }

        if (lat && lng) {
          $latlngHiddenInput.val(`${  lat  },${  lng  }`);
          $("body").addClass("search-with-geocoding");
        }

      };

      /**
       * Update UI upon geocoding error.
       *
       * @return {*}
       */
      const handleGeocodeError = function () {

        clearUserLocation();
        setUserMessage('No matches found. Try again.');

      };

      /**
       * Clear hidden lat/lng value and status message.
       */
      const clearUserLocation = function () {

        if ($addressInput.val() === locationLoadingText) {
          $addressInput.val('');
        }
        $addressInput.prop('disabled', false);

        $latlngHiddenInput.val('');
        $("body").removeClass("search-with-geocoding");
        $("body").removeClass("search-with-current-location");
        setUserMessage('');

      };

      /**
       * Tweak user input text to be geocoded.
       *
       * @return {string}
       */
      const getCleanedKeywordSearch = function () {

        let returnValue = $addressInput.val().trim();

        // Get the JSON, UWM list of search and replace terms. These are keywords
        // we can use, repacing what the user typed with something that matches
        // better on the Google geocoding API.
        const srt = (typeof uwdm_gtm_search_location_keywords_replacements === 'undefined')
          ? {} : uwdm_gtm_search_location_keywords_replacements;

        if (srt && srt.length) {

          srt.forEach((item) => {

            if (item.search_keywords && item.replacement_keywords) {

              const searchWord = item.search_keywords.toLowerCase();
              if (returnValue.toLowerCase() === searchWord) {
                returnValue = item.replacement_keywords;
              }

            }

          });

        }

        return returnValue;

      };

      /**
       * Update the status message below address input.
       *
       * @param message
       */
      const setUserMessage = function (message) {

        $addressContainer.find('.status-message').text(message);

      };

    }

  };

})
(jQuery, Drupal);
