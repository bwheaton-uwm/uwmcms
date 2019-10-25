/**
 * @file
 * Custom JS for provider modal appointment flow component.
 *
 * Library: uwmbase/modal-appointment.
 *
 * @see https://getbootstrap.com/docs/4.1/components/modal/
 */

(function (Drupal, $) {

  Drupal.behaviors.uwmModalAppointment = {
    attach: function (context) {

      var $modal = $('.uwm-modal-appointment.modal', context);

      if (!$modal.length) {
        return;
      }

      // The context is the type of page on which the appointment modal is being
      // used. If we're on a provider's bio page, any elements with provider-
      // specific data (e.g. the Epic ID) can stay static throughout modal
      // interaction. If the page has multiple provider cards, these values will
      // be updated for the selected provider when the modal opens.
      // Possible values:
      // 'provider_page' - provider bio (node) page
      // 'search_providers' - provider card on search results
      // 'clinic_page_tab' - provider card on clinic page tab.
      var modalContext = $modal.attr('data-modal-appt-context') || null;

      // Initialize provider-specific scheduling variables here, so they are
      // accessible throughout handlers and functions. Values are set when the
      // modal is shown.
      var epicID = null;
      var openScheduling = null;
      var visitTypeIDs = null;
      var openMultipleTypes = null;
      var directScheduling = null;

      // TODO: get these from however we store them.
      var visitTypeNames = {
        '9000': 'New Patient Appointment',
        '4466': 'New Pregnancy Care',
        '103619': 'New Wellness Appointment'
      };

      var visitTypeDescriptions = {
        '9000': 'Initial appointment to establish care',
        '4466': 'First visit for a new pregnancy',
        '103619': 'Care for prevention and checkups'
      };

      var $steps = $modal.find('.appointment-flow__step');
      var $stepVisitedBefore = $modal.find('[data-step="visited-before"]');
      var $stepEcareAccount = $modal.find('[data-step="ecare-account"]');
      var $stepVisitType = $modal.find('[data-step="visit-type"]');
      var $stepOpenSchedWidget = $modal.find('[data-step="open-schedule"]');

      var $iframeOpenSched = $stepOpenSchedWidget.length ? $stepOpenSchedWidget.find('iframe.appointment-flow__open-scheduling-embed') : null;
      // TODO: temporary link while we are working to enable widget in iframe.
      var $linkOpenSched = $stepOpenSchedWidget.length ? $stepOpenSchedWidget.find('a.appointment-flow__open-scheduling-link') : null;
      var $linkDirectSched = $stepEcareAccount.length ? $stepEcareAccount.find('a.appointment-flow__direct-scheduling-link') : null;

      // Track steps the user has visited, for use by Back links.
      var stepPath = [];

      /*
       * Set modal to given step.
       */
      function setStep($step, storeStep) {

        var $prev = $steps.filter('.active');
        if ($prev.length) {

          // If moving forward, store the previous step for use by Back links.
          if (storeStep) {
            stepPath.push($prev.attr('data-step'));
          }

          $prev.removeClass('active');

        }

        $step.addClass('active');

      }

      function stepForward($step) {

        setStep($step, true);

      }

      function stepBack() {

        if (stepPath.length > 0) {

          setStep($steps.filter('[data-step="' + stepPath.pop() + '"]'), false);

        }

      }

      /*
       * Update link 'href' and iframe 'src' url values to insert a value for a
       * relevant query string key, based on provider and user selections.
       */
      function setUrlAttrQueryStringVal($elem, attrName, key, val) {

        // In the original markup, the url query string contains the key and '='
        // part, but not the value, for parameters we may set in the flow:
        // e.g. "&key=". Store this original "base" url, so we can reset and set
        // our key/value parameter pairs multiple times as needed.
        var url = $elem.attr(attrName);
        var baseAttr = $elem.data('base-' + attrName);

        // Store the current key/value pairs in easily accessible way.
        var query = $elem.data('url-query') || {};
        query[key] = val;
        $elem.data('url-query', query);

        if (!baseAttr) {
          // On first time setting a key/val, store the base url value for
          // reference in resetting.
          $elem.data('base-' + attrName, url);
        }
        else {
          // If setting key/val that's already been set within current flow
          // (e.g. user stepped back), reset first. Otherwise our string
          // replacement will insert the new value alongside the existing one.
          resetUrlAttr($elem, attrName);
          url = $elem.attr(attrName);
        }

        // Update url with current key/value parameters.
        for (var k in query) {
          if (query.hasOwnProperty(k)) {
            var v = query[k];
            url = url.replace(k + '=', k + '=' + v);
          }
        }

        $elem.attr(attrName, url);

      }

      /*
       * Update link 'href' and iframe 'src' url values to restore the original
       * "base" url string, in which relevant keys in the query string have no
       * value.
       */
      function resetUrlAttr($elem, attrName) {

        $elem.attr(attrName, $elem.data('base-' + attrName));

      }

      /*
       * Modal initialization
       */
      // On modal open, initialize everything.
      // "This event fires immediately when the show instance method is called."
      // (Bootstrap doc)
      // Note: no step is shown on page load, because the first step differs by
      // appointment logic. Also, presumably this handler does not necessarily
      // finish running before the modal shows, so we don't want a step to be
      // visible before our adjustments.
      $modal.on('show.bs.modal', function (e) {

        // 1. Retrieve scheduling values specific to the selected provider from
        // data attributes on the button that opened the modal.
        // @see https://getbootstrap.com/docs/4.1/components/modal/#varying-modal-content
        var $modalBtn = $(e.relatedTarget);

        epicID = $modalBtn.attr('data-provider-epic-id') || null;

        // Booleans are passed via attributes as "1" (true) or "0" (false).
        // Convert string value to number first, because `"0"` converts to true.
        // We also must check explicitly if the attribute is present; with the
        // `||` shortcut, a legitimate `false` value would result in `null`.
        if (typeof $modalBtn.attr('data-provider-open-scheduling') !== 'undefined') {
          openScheduling = Boolean(Number($modalBtn.attr('data-provider-open-scheduling')));
        }

        // Multiple visit type IDs are delimited by '|'. Check if the attribute
        // value string is non-empty, because splitting an empty string results
        // in an array with one element that is the empty string, which is
        // inaccurate.
        var visitTypeIDsAttr = $modalBtn.attr('data-provider-visit-type-ids');
        if (typeof visitTypeIDsAttr === 'string' && visitTypeIDsAttr.length > 0) {
          // Note: by splitting a string, the result values are (numeric)
          // strings. Both the number and equivalent numeric string work as
          // object keys to access the value, so we can safely use these to get
          // the visit type name and description.
          visitTypeIDs = visitTypeIDsAttr.split('|');
        }

        openMultipleTypes = (visitTypeIDs ? visitTypeIDs.length > 1 : false);

        if (typeof $modalBtn.attr('data-provider-direct-scheduling') !== 'undefined') {
          directScheduling = Boolean(Number($modalBtn.attr('data-provider-direct-scheduling')));
        }

        /*
        console.log('modalContext:', modalContext, ', epicID:', epicID, ', openScheduling:', openScheduling, ', visitTypeIDs:', visitTypeIDs, ', openMultipleTypes:', openMultipleTypes, ', directScheduling:', directScheduling);
        */

        // 2. If not on a provider page, update elements for the provider
        // selected by user.
        if (modalContext !== 'provider_page') {

          if (openScheduling) {

            // On appointment type step, add button for each of provider's
            // available types.
            // Note: since these are added after page load, the click event
            // handler is on the step container, which catches and filters
            // events bubbled from the buttons. See $stepVisitType init below.
            if (openMultipleTypes && $stepVisitType.length) {

              var $btnContainer = $stepVisitType.find('.appointment-flow__step-buttons');
              var $btnTemplate = $stepVisitType.find('a[data-btn-visit-type-id="template"]');

              for (var i = 0; i < visitTypeIDs.length; i++) {

                var visitTypeID = visitTypeIDs[i];

                // TODO: handle missing name/description bad data?
                if (visitTypeNames.hasOwnProperty(visitTypeID)) {

                  var $btn = $btnTemplate.clone();
                  $btn.attr('data-btn-visit-type-id', visitTypeID);
                  $btn.find('.appointment-flow__step-button-name').text(visitTypeNames[visitTypeID]);

                  if (visitTypeDescriptions.hasOwnProperty(visitTypeID)) {
                    $btn.find('.appointment-flow__step-button-desc').text(visitTypeDescriptions[visitTypeID]);
                  }

                  $btnContainer.append($btn);

                }

              }

            }

            // Update open scheduling widget with provider's Epic ID.
            // TODO: bad to have iframe with a "broken" src at any point?
            if ($stepOpenSchedWidget.length) {
              setUrlAttrQueryStringVal($iframeOpenSched, 'src', 'id', epicID);
              setUrlAttrQueryStringVal($linkOpenSched, 'href', 'id', epicID);
            }

          }

          if (directScheduling && $stepEcareAccount.length) {

            // Update direct scheduling link with provider's Epic ID.
            setUrlAttrQueryStringVal($linkDirectSched, 'href', 'selProvId', epicID);

          }

        }

        // 3. Set the initial step to display.
        var $firstStep = null;

        // - Open, 1 type / No direct.
        if (openScheduling && !openMultipleTypes && !directScheduling) {

          $firstStep = $stepOpenSchedWidget;

        }
        // - Open, multiple types / No direct.
        else if (openScheduling && openMultipleTypes && !directScheduling) {

          $firstStep = $stepVisitType;

        }
        // - Open, 1 type / Direct
        // - Open, multiple types / Direct.
        else if (openScheduling && directScheduling) {

          $firstStep = $stepVisitedBefore;

        }

        if ($firstStep) {

          // Denote the first step because it varies by appointment logic.
          // (This class handles hiding the Back link on the first step.)
          $firstStep.addClass('appointment-flow__step--first');

          // Actually move to the first step!
          stepForward($firstStep);

        }

      });

      /*
       * Step button actions
       */

      if ($stepVisitedBefore.length) {

        // "Have you visited this doctor before?" => No.
        $stepVisitedBefore.find('a[data-btn="no"]').click(function (e) {

          e.preventDefault();

          if (!openMultipleTypes) {
            stepForward($stepOpenSchedWidget);
          }
          else if (openMultipleTypes) {
            stepForward($stepVisitType);
          }

        });

        // "Have you visited this doctor before?" => Yes.
        $stepVisitedBefore.find('a[data-btn="yes"]').click(function (e) {

          e.preventDefault();

          stepForward($stepEcareAccount);

        });

      }

      if ($stepEcareAccount.length) {

        // "Do you have an eCare (patient) account?" => No.
        $stepEcareAccount.find('a[data-btn="no"]').click(function (e) {

          e.preventDefault();

          if (!openMultipleTypes) {
            stepForward($stepOpenSchedWidget);
          }
          else if (openMultipleTypes) {
            stepForward($stepVisitType);
          }

        });

      }

      if ($stepVisitType.length) {

        // Set 'click' handler for visit type buttons.
        // Use a delegated handler - attach the event to the step container
        // and filter to events bubbling from the descendant buttons. Thus we
        // handle buttons that exist at page load (on provider page) and those
        // added at the time of modal opening (on other pages with multiple
        // providers, which pass the provider data to the modal).
        // @see https://api.jquery.com/on/
        $stepVisitType.on('click', 'a[data-btn-visit-type-id][data-btn-visit-type-id!="template"]', function (e) {

          e.preventDefault();

          var visitTypeID = $(this).attr('data-btn-visit-type-id');

          // Update open scheduling widget with the chosen visit type ID.
          // TODO: bad to have iframe with a "broken" src?
          setUrlAttrQueryStringVal($iframeOpenSched, 'src', 'vt', visitTypeID);
          setUrlAttrQueryStringVal($linkOpenSched, 'href', 'vt', visitTypeID);

          stepForward($stepOpenSchedWidget);

        });

      }

      /*
       * Back link action
       */
      $steps.find('.appointment-flow__step-back a').click(function (e) {

        e.preventDefault();

        stepBack();

      });

      /*
       * Modal close
       */
      // On modal close, reset everything as needed.
      // "This event is fired when the modal has finished being hidden from the
      // user (will wait for CSS transitions to complete)." (Bootstrap doc)
      $modal.on('hidden.bs.modal', function (e) {

        // Hide whichever step the user was on.
        var $prev = $steps.filter('.active');
        if ($prev.length) {
          $prev.removeClass('active');
        }

        // Remove steps state.
        stepPath = [];

        // Remove the first-step indicator class.
        $steps.removeClass('appointment-flow__step--first');

        // If not on a provider bio page, remove provider-specific values.
        if (modalContext !== 'provider_page') {

          // Remove visit type buttons.
          if ($stepVisitType.length) {

            $stepVisitType.find('a[data-btn-visit-type-id]').not('[data-btn-visit-type-id="template"]').remove();

          }

          // Remove provider's Epic ID and visit type from open scheduling
          // widget embed.
          if ($stepOpenSchedWidget.length) {

            resetUrlAttr($iframeOpenSched, 'src');
            resetUrlAttr($linkOpenSched, 'href');

          }

          // Remove provider's Epic ID from direct scheduling link.
          if ($stepEcareAccount.length) {

            resetUrlAttr($linkDirectSched, 'href');

          }

        }

      });

    }
  };

})(Drupal, jQuery);
