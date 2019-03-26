/**
 * @file
 * Scripting for clinic-type nodes.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.clinicExtraTabLinks = {
    attach: function (context, settings) {

      var $clinicTabs = $('.clinic-tabs .nav-tabs a[data-toggle="tab"]');
      var $mainTab = $('.clinic-tabs .nav-tabs a[href="#main-tab"]');
      var $directionsTab = $('.clinic-tabs .nav-tabs a[href="#directions-tab"]');

      // Helper that triggers showing a tab (if not already active), and setting
      // an element within the tab to which to scroll.
      var showTabAndScroll = function ($tab, $scrollTo, setHash) {
        $tab.data('scrollTo', $scrollTo);

        if (!$tab.parent('li').hasClass('active')) {
          $tab.trigger('click');
        }
        else {
          // If a tab is already active, triggering the click on it does not
          // result in the 'shown.bs.tab' event. But we want to run our handler
          // (for scrolling) nonetheless.
          $tab.triggerHandler('shown.bs.tab');
        }

        // Set a hash if requested, to add a history entry.
        if (setHash !== undefined) {
          window.location.hash = setHash;
        }
      };

      // Tab-shown handler: scroll to element within tab, if specified via
      // 'scrollTo' data attr.
      // @see https://getbootstrap.com/docs/3.3/javascript/#tabs-methods
      $clinicTabs.on('shown.bs.tab', function () {
        var $tab = $(this);
        var $scrollTo = $tab.data('scrollTo');

        if ($scrollTo && $scrollTo.length > 0) {
          $('html, body').animate({
            'scrollTop': $scrollTo.offset().top
          }, 700);

          // Remove the scrollTo element, so it's only used when our custom code
          // sets it before a tab is shown.
          $tab.data('scrollTo', null);
        }
      });

      // On page load with the Directions tab hash, scroll to top of tabs.
      // (Bootstrap tab history shows the tab based on hash, but does not scroll
      // to it.)
      if (context === document && window.location.hash === '#directions-tab') {
        // The tabs are in a container positioned with a negative top margin.
        // There seems to be a race condition - just after page load, the top
        // offset of the tabs is computed as if without the margin-top.
        // Give an extra bit of time to hopefully compute the final position.
        // (If this is not enough, the position is at the tab content, which
        // is a reasonable fallback.)
        window.setTimeout(function () {
          showTabAndScroll($directionsTab, $directionsTab);
        }, 150);
      }

      // On Directions jump link click, scroll to top of tabs.
      $('section.clinic-header a[href="#directions-jump"]', context).click(function (e) {
        e.preventDefault();
        showTabAndScroll($directionsTab, $directionsTab);
      });

      // On Clinic Overview jump link click, scroll to overview section within
      // main Services/Resources tab.
      $('section.clinic-header a[href="#clinic-overview-jump"]', context).click(function (e) {
        e.preventDefault();
        showTabAndScroll($mainTab, $('#clinic-overview-jump'), '#clinic-overview-jump');
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
