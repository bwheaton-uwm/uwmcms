/**
 * @file
 * Checks URL has and activates needed tabs, then scrolls to page anchor.
 *
 * Specifications for HTML have a browser immediately scroll to a page
 * element id that matches a URL hash.  So we make our script
 * run only if we have a URL has that contains "---".
 *
 * @see https://mathiasbynens.be/notes/css-escapes
 *
 * The "#string-id---" hash is split into parts separated by the "---" character,
 * and we trigger a click on each anchor part.  That allows making a
 * Bootstrap active or opening a modal, then scrolling to the last anchor part.
 *
 * For example, for the anchor here we trigger clicks on "tab-id-1" and
 * "sub-tab-id-22", then scroll to "anchor-id-333"
 *
 * <a href="#tab-id-1---sub-tab-id-33---anchor-id-333">My link</a>
 */

(function ($, Drupal) {

  Drupal.behaviors.uwdmScrollToAnchor = {

    attach(context, settings) {

      /**
       * Our main function to scroll to an anchor.
       */
      const init = function () {

        const anchorsArray = getValidAnchorsArray();
        if (anchorsArray.length > 0) {
          activateNestedAnchorPath(anchorsArray);
          scrollToAnchorPart(anchorsArray.pop(), anchorsArray.length);
        }

      };

      /**
       * Returns an array of anchor ids, if URL hash contains "---".
       *
       * @return {*}
       */
      const getValidAnchorsArray = function () {

        if (window.location.hash.indexOf('---') > 0) {

          return window.location.hash.substr(1).split('---').filter((el) => el != null && el != '' && el != '#');
        }
        return [];

      };

      /**
       * Scrolls to a given element id.
       *
       * @param anchorId
       * @param n
       */
      const scrollToAnchorPart = function (anchorId, n = 1) {

        const $target = $(`[id = ${ anchorId }]`);
        if ($target.length > 0) {

          // Our element will have a "0" offset if hidden on the page. Wait
          // until animations have completed before scrolling to the element.
          setTimeout(() => {
            let yPx = $target.offset().top;
            $('html,body').animate({
              scrollTop: yPx
            }, 350);

          }, 600 * n);

        }

      };

      /**
       * Activates each anchor part, giving focus or activating tabs.
       *
       * @param anchorsArray
       */
      const activateNestedAnchorPath = function (anchorsArray) {

        anchorsArray.forEach((anchor, i) => {

          let $elm = $(`[id = ${ anchor }]`);
          if ($elm.length > 0) {
            $elm.trigger('click');
          }

        });

      };

      // Run on initial page load by checking `context`:
      if (context === document) {
        init();
      }

      // Add listener for anchor links clicked:
      window.addEventListener('hashchange', () => {
        init();
      });

    }

  };

})(jQuery, Drupal);
