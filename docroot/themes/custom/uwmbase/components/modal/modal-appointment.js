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

      // Determine context of appointment modal. If we're on a provider's bio
      // page, some things can stay static based on their data.
      // Expected values:
      // Provider bio (node) page: 'provider_page'
      // Provider card on search results: 'search_providers'
      // Provider card on clinic page tab: 'clinic_page_tab'.
      var modalContext = $modal.attr('data-modal-appt-context') || null;

      // TODO: data.
      var epicID = 12345;
      var openSched = true;
      var visitTypeIDs = [ 12, 34, 56, 78, 90 ];
      var openMultipleTypes = (visitTypeIDs.length > 1);
      var directSched = true;

      var visitTypeNames = {
        '12': "New Appointment 12",
        '34': "New Appointment 34",
        '56': "New Appointment 56",
        '78': "New Appointment 78",
        '90': "New Appointment 90",
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

      // TODO: Is a var here a good way to store this?
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
       * Set 'click' handler for visit type buttons.
       *
       * Provide this as a function so we can run it both on page load and when
       * the modal is updated with a provider's visit types.
       */
      function stepVisitTypeButtonsSetClick() {

        if ($stepVisitType.length && $stepOpenSchedWidget.length) {

          $stepVisitType.find('a[data-btn-visit-type-id]').not('[data-btn-visit-type-id="template"]').click(function (e) {

            e.preventDefault();

            var visitTypeID = $(this).attr('data-btn-visit-type-id');

            // Update open scheduling widget with the chosen visit type ID.
            // TODO: bad to have iframe with a "broken" src?
            setUrlAttrQueryStringVal($iframeOpenSched, 'src', 'vt', visitTypeID);
            setUrlAttrQueryStringVal($linkOpenSched, 'href', 'vt', visitTypeID);

            stepForward($stepOpenSchedWidget);

          });

        }

      }

      /*
       * Modal initialization
       */
      // On modal open, initialize everything.
      // "This event fires immediately when the show instance method is called."
      // (Bootstrap doc)
      // Note: no step is shown by default, because the first step differs by
      // appointment logic. Also, presumably this handler does not necessarily
      // finish running before the modal shows, so we don't want a step to be
      // visible before our adjustments.
      $modal.on('show.bs.modal', function (e) {

        // TODO: use context.
        if (true /*modalContext !== 'provider_page'*/ && openSched) {

          // Update appointment type step with provider's available types.
          if (openMultipleTypes && $stepVisitType.length) {

            var $btnContainer = $stepVisitType.find('.appointment-flow__step-buttons');
            var $btnTemplate = $stepVisitType.find('a[data-btn-visit-type-id="template"]');

            for (var i = 0; i < visitTypeIDs.length; i++) {

              var visitTypeID = visitTypeIDs[i];

              // TODO: handle missing name bad data?
              if (visitTypeNames.hasOwnProperty(visitTypeID)) {

                var $btn = $btnTemplate.clone();
                $btn.attr('data-btn-visit-type-id', visitTypeID);
                $btn.text(visitTypeNames[visitTypeID]);

                $btnContainer.append($btn);

              }

            }

            // Attach click handlers to buttons.
            stepVisitTypeButtonsSetClick();

          }

          // Update open scheduling widget with provider's Epic ID.
          // TODO: bad to have iframe with a "broken" src at any point?
          if ($stepOpenSchedWidget.length) {
            setUrlAttrQueryStringVal($iframeOpenSched, 'src', 'id', epicID);
            setUrlAttrQueryStringVal($linkOpenSched, 'href', 'id', epicID);
          }

        }

        if (true /*modalContext !== 'provider_page'*/ && directSched && $stepEcareAccount.length) {

          // Update direct scheduling link with provider's Epic ID.
          setUrlAttrQueryStringVal($linkDirectSched, 'href', 'selProvId', epicID);

        }

        // Set the initial step:
        // - Open, 1 type / No direct.
        if (openSched && !openMultipleTypes && !directSched) {

          stepForward($stepOpenSchedWidget);

        }
        // - Open, multiple types / No direct.
        else if (openSched && openMultipleTypes && !directSched) {

          stepForward($stepVisitType);

        }
        // - Open, 1 type / Direct
        // - Open, multiple types / Direct.
        else if (openSched && directSched) {

          stepForward($stepVisitedBefore);

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

        // On a provider page, we know and have the buttons at page load,
        // because they are rendered in template.
        // Otherwise, this is handled upon opening the modal.
        if (modalContext === 'provider_page') {
          stepVisitTypeButtonsSetClick();
        }

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

        // Hide previous step.
        var $prev = $steps.filter('.active');
        if ($prev.length) {
          $prev.removeClass('active');
        }

        // Remove steps state.
        stepPath = [];

        // If not on a provider bio page, remove provider-specific values.
        // TODO: use context.
        if (true /*modalContext !== 'provider_page'*/) {

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
