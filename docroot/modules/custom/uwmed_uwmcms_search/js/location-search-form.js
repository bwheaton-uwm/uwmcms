/**
 * @file
 * Behaviors for adding custom behavior to the location search form.
 */

(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.locationSearchForm = {
    attach: function (context, settings) {

      var locationButton = $('.use-my-location__button');
      var locationStatus = locationButton.nextAll('.use-my-location__status');

      locationButton.click(function (event) {
        if (!navigator.geolocation) {
          error();
        }
        else {
          locationStatus.text('Getting your location …');
          navigator.geolocation.getCurrentPosition(success, error);
        }
      });

      function success(position) {
        var coordinates = position.coords.latitude + ',' + position.coords.longitude;
        locationButton.nextAll('.use-my-location__coordinates').val(coordinates);
        locationButton.addClass('use-my-location__button--active');
        locationStatus.text('');

        // Add the coordinates to the temporary field being used for testing.
        $('#edit-d-value').val(coordinates);
      }

      function error() {
        locationStatus.text("Your location could not be determined");
      }
    }
  };

})(jQuery, Drupal);
