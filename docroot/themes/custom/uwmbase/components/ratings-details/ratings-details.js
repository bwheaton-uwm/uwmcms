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
        },
        // Add extra classs to the page links.
        afterRender: function () {
          $('.paginationjs-page').addClass('pager__item');
          $('.paginationjs-prev').addClass('pager__item pager__item--text pager__item--previous');
          $('.paginationjs-next').addClass('pager__item pager__item--text pager__item--next');
          $('.paginationjs-ellipsis').addClass('pager__item pager__item--text pager__item--ellipsis');
        }
      })
    }
  };

})(Drupal, jQuery);
