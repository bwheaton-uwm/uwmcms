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
      var acceptingNew = null;
      var acceptingReturning = null;
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
      var $stepCallInsteadAll = $modal.find('[data-step="call-instead-all"]');
      var $stepCallInsteadReturning = $modal.find('[data-step="call-instead-returning"]');
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

        // Booleans for:
        // - Accepting new patients (manual status)
        // - Accepting returning patients (manual status)
        // - Open scheduling enabled (determined by Epic)
        // - Direct scheduling enabled (determined by Epic)
        // Booleans attributes are "1" (true) or "0" (false).
        var booleanAttrs = [
          'data-provider-accepting-new',
          'data-provider-accepting-returning',
          'data-provider-open-scheduling',
          'data-provider-direct-scheduling'
        ];
        for (var i = 0; i < booleanAttrs.length; i++) {

          if (typeof $modalBtn.attr(booleanAttrs[i]) !== 'undefined') {
            var value = Boolean(Number($modalBtn.attr(booleanAttrs[i])));

            switch (booleanAttrs[i]) {
              case 'data-provider-accepting-new':
                acceptingNew = value;
                break;

              case 'data-provider-accepting-returning':
                acceptingReturning = value;
                break;

              case 'data-provider-open-scheduling':
                openScheduling = value;
              break;

              case 'data-provider-direct-scheduling':
                directScheduling = value;
                break;
            }
          }

        }

        // Provider's Epic ID.
        epicID = $modalBtn.attr('data-provider-epic-id') || null;

        // Visit type IDs for open scheduling - delimited by '|'.
        var visitTypeIDsAttr = $modalBtn.attr('data-provider-visit-type-ids');
        if (typeof visitTypeIDsAttr === 'string' && visitTypeIDsAttr.length > 0) {
          // Note: by splitting a string, the result values are (numeric)
          // strings. Both the number and equivalent numeric string work as
          // object keys to access the value, so we can safely use these to get
          // the visit type name and description.
          visitTypeIDs = visitTypeIDsAttr.split('|');
        }

        // Multiple visit types?
        openMultipleTypes = (visitTypeIDs ? visitTypeIDs.length > 1 : false);

        /*
        console.log('modalContext:', modalContext, 'acceptingNew:', acceptingNew, ', acceptingReturning:', acceptingReturning, ', epicID:', epicID, ', openScheduling:', openScheduling, ', visitTypeIDs:', visitTypeIDs, ', openMultipleTypes:', openMultipleTypes, ', directScheduling:', directScheduling);
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
        // -------------------------------------------------------------------
        // Note: realistically, if provider is accepting new, they should also
        // be accepting returning. However, respect the data, and do not offer
        // the direct scheduling option if set as not accepting returning.
        // (This should be logged as likely bad data.)
        // ------------------------------------------------------------------.
        var $firstStep = null;

        if (acceptingNew && openScheduling) {

          if (acceptingReturning && directScheduling) {

            // Open and direct scheduling available.
            // Start by asking if user is within the time frame to be a
            // returning patient.
            $firstStep = $stepVisitedBefore;

          }
          else {

            // Only open scheduling available.
            if (openMultipleTypes) {

              // If multiple visit types, start by selecting visit type.
              $firstStep = $stepVisitType;

            }
            else {

              // Only one visit type; go straight to open scheduling widget.
              $firstStep = $stepOpenSchedWidget;

            }

          }

        }
        else if (acceptingReturning && directScheduling) {

          // Only direct scheduling available.
          // Start by asking if user is within the time frame to be a
          // returning patient.
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

        // "Have you visited this provider in the last three years?" => No.
        $stepVisitedBefore.find('a[data-btn="no"]').click(function (e) {

          e.preventDefault();

          if (acceptingNew) {
            if (openScheduling) {

              // Offer open scheduling when it's available.
              if (openMultipleTypes) {
                stepForward($stepVisitType);
              }
              else {
                stepForward($stepOpenSchedWidget);
              }

            }
            else {

              // Show message that only returning patients within 3 years can
              // book online; anyone may call.
              stepForward($stepCallInsteadAll);

            }
          }
          else {

            // Show message that only returning patients within 3 years can
            // book online; returning patients may call.
            stepForward($stepCallInsteadReturning);

          }

        });

        // "Have you visited this provider in the last three years?" => Yes.
        // If only direct scheduling is available, this button links directly
        // to eCare (via the template), because user is coming from the
        // returning patients link that already specifies eCare requirement.
        // If open scheduling is available too, it moves to eCare account step.
        //
        // Note: We set this handler at time of attach behaviors. It would be
        // ideal not to set it if the button is just a link - but we don't get
        // the provider scheduling variables until the modal opens. So we can
        // only check for open scheduling when the handler runs, not when we
        // first set it.
        $stepVisitedBefore.find('a[data-btn="yes"]').click(function (e) {

          if (acceptingNew && openScheduling) {

            e.preventDefault();

            stepForward($stepEcareAccount);

          }

        });

      }

      if ($stepEcareAccount.length) {

        // "Do you have an eCare (patient) account?" => No.
        $stepEcareAccount.find('a[data-btn="no"]').click(function (e) {

          e.preventDefault();

          if (openMultipleTypes) {
            stepForward($stepVisitType);
          }
          else {
            stepForward($stepOpenSchedWidget);
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
