/**
 * @file
 * Custom JS for ratings details component.
 *
 * Library: uwmbase/ratings-details.
 */

(function (Drupal, $) {

  Drupal.behaviors.uwmRatingsDetails = {
    attach: function (context) {
      var details = $('.ratings-details__details');

      function scrollToShowingInfo() {
        let yPx = $('#ratings-details__showing-info').offset().top;
        $('html,body').animate({
          scrollTop: yPx
        }, 350);
      }

      // Add pagination.
      $('#ratings-details__details-section').pagination({
        dataSource: function (done) {
          var ds = [];
          for (var i = 1; i <= details.length; i++) {
            ds.push(i);
          }
          done(ds);
        },
        pageSize: 10,
        pageRange: 2,
        autoHidePrevious: true,
        autoHideNext: true,
        showFirstOnEllipsisShow: true,
        showLastOnEllipsisShow: true,
        prevText: 'Previous',
        nextText: 'Next',
        hideWhenLessThanOnePage: true,
        className: 'pager',
        ulClassName: 'pager__items',
        activeClassName: 'is-active',
        callback: function (data, pagination) {
          $('.ratings-details__details--active').removeClass('ratings-details__details--active');
          data.forEach(function (i) {
            $(details[i - 1]).addClass('ratings-details__details--active');
          })

          // Update the "showing x-y" text.
          if (typeof pagination != 'undefined') {
            let firstItem = ((pagination.pageNumber - 1) * pagination.pageSize) + 1;
            let lastItem = (firstItem + 9 > details.length) ? details.length : firstItem + 9;
            $('#ratings-details__showing-info-text').html('Showing ' + firstItem + '-' + lastItem);
          }
        },

        // If there are fewer than 11 details items, display all of them.
        beforeInit: function () {
          if (details.length < 11) {
            details.addClass('ratings-details__details--active');
          }
        },

        // Add extra classs to the page links, scroll on click, remove the
        // anchor tag from the any ellipses, and add span wrappers and icons.
        afterRender: function () {
          $('.pager__items li').addClass('pager__item')
            .not('.paginationjs-ellipsis, .is-active').click(function () {
              scrollToShowingInfo();
          });

          // Get CTA classes and data attributes and add them to the
          // pagination links.
          var dummyAnchor = $('#paginationjs').find('.cta-parent-res-provider');
          $('.paginationjs-pages').find('.pager__item').not('.is-active').find('a')
            .addClass(dummyAnchor.attr('class').replace(' d-none', ''))
            .attr('data-context-title', dummyAnchor.attr('data-context-title'))
            .attr('data-context-npi', dummyAnchor.attr('data-context-npi'));

          $('.paginationjs-prev').addClass('pager__item--text pager__item--previous').find('a').contents().before('<i class="fa fa-angle-left arrow-linked" aria-hidden="true"></i>').wrap('<span class="link-text"></span>');
          $('.paginationjs-next').addClass('pager__item--text pager__item--next').find('a').contents().after('<i class="fa fa-angle-right arrow-linked" aria-hidden="true"></i>').wrap('<span class="link-text"></span>');
          $('.paginationjs-ellipsis').addClass('pager__item--text pager__item--ellipsis').find('a').contents().unwrap();

          $('.paginationjs-page')
            .first().addClass('pager__item-first').end()
            .last().addClass('pager__item-last');

          var activePage = $('.paginationjs-page.is-active');
          var lastPageNum = $('.pager__item-last').attr('data-num');

          // Add offset classes.
          activePage
            .addClass('current-offset-0')
            .prevAll('.paginationjs-page:not(:eq(-1))')
            .addClass(function (index) {
              return 'current-offset-' + (index + 1);
            })
            .end()
            .nextAll('.paginationjs-page:not(:eq(-1))')
            .addClass(function (index) {
              return 'current-offset-' + (index + 1);
            }
          );

          switch (activePage.attr('data-num')) {
            // If the active page is fourth from either end, prevent hiding
            // of the second page from the end at narrow widths.
            case "4":
              $('.pager__items').find('[data-num="2"]').addClass('d-block');
              break;

            case (lastPageNum - 3).toString():
              $('.pager__items').find('[data-num="' + (lastPageNum - 1) + '"]').addClass('d-block');
              break;

            // If the active page is fifth from either end, add an ellipsis.
            case "5":
              $('.paginationjs-page').first().after('<li class="paginationjs-ellipsis disabled pager__item pager__item--text pager__item--ellipsis d-sm-none">...</li>');
              break;

            case (lastPageNum - 4).toString():
              $('.paginationjs-page').last().before('<li class="paginationjs-ellipsis disabled pager__item pager__item--text pager__item--ellipsis d-sm-none">...</li>');
              break;
          }
        }
      });
    }
  };

})(Drupal, jQuery);
