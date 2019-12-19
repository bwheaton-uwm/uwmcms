/**
 * @file
 * JS provided by Epic to initialize the eCare open scheduling embedded widget.
 *
 * The denoted section within this code is not written by UWM; should not be
 * modified unless per communication with Epic team.
 *
 * Library: uwmbase/ecare-open-schedule-embedded-widget.
 *
 * @see uwm-appointment-flow-provider.html.twig
 */

(function (Drupal, $) {

  Drupal.behaviors.uwmEcareEmbeddedWidget = {
    attach: function (context) {

      // Only initialize if the embedded widget is present in an iframe.
      if (!$('iframe#openSchedulingFrame').length) {
        return;
      }

      // Ensure that the global constructor function we need to use - defined in
      // eCare-hosted JS - exists, to avoid error.
      // @see https://ecare.uwmedicine.org/prod01/Content/EmbeddedWidgetController.js
      if (typeof EmbeddedWidgetController !== 'undefined') {

        /**
         * Begin: Epic-provided JS.
         */
        var EWC = new EmbeddedWidgetController({
          // TODO: TEST ECARE URL - DO NOT MERGE TO MASTER.
          // Replace with the hostname of your Open Scheduling site.
          'hostname': 'https://tstecare18.epic.medical.washington.edu',

          // Must equal media query in EpicWP.css + any left/right margin of the host page. Should also change in EmbeddedWidget.css.
          'matchMediaString': '(max-width: 747px)',

          // Show a button on top of the widget that lets the user see the slots in fullscreen.
          'showToggleBtn': true,

          // The toggle button's help text for screen reader.
          'toggleBtnExpandHelpText': 'Expand to see the slots in fullscreen',
          'toggleBtnCollapseHelpText': 'Exit fullscreen',
        });
        /**
         * End: Epic-provided JS.
         */

      }
      else {

        // Give a warning of missing JS dependency.
        // TODO: can we handle this more gracefully?
        if (console) {
          console.warn('`EmbeddedWidgetController` not defined - issue with eCare EmbeddedWidgetController.js');
        }

      }

    }
  };

})(Drupal, jQuery);
