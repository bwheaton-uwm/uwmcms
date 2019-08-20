/**
 * @file
 * Custom JS for modal video component.
 *
 * Library: uwmbase/modal-video.
 *
 * @see https://getbootstrap.com/docs/4.1/components/modal/
 */

(function (Drupal, $) {

  Drupal.behaviors.uwmModalVideo = {
    attach: function (context) {

    $('.uwm-modal-video .modal', context)
      // "This event fires immediately when the show instance method is called."
      // (Bootstrap doc)
      // TODO: The following code could be a partial lazy-load implementation.
      // The concern is whether GTM YouTube video trigger will apply to iframes
      // added dynamically in the page...
      /*
      .on('show.bs.modal', function (e) {

        var $modal = $(this);
        var $iframe = $modal.find('iframe');

        var dataSrc = $iframe.attr('data-src');
        var src = $iframe.attr('src');

        if (!src && dataSrc) {
          $iframe.attr('src', dataSrc);
        }

      })
      */

      // "This event is fired when the modal has finished being hidden from the
      // user (will wait for CSS transitions to complete)." (Bootstrap doc)
      .on('hidden.bs.modal', function (e) {

        var $modal = $(this);

        // Reset iframe src to ensure video stops playing.
        // (Note: setting 'src' to empty breaks YouTube's JS.)
        //
        // TODO: this is a workaround.
        // - It would be better to use YT IFrame JS API to stop the video.
        // - This also prevents using auto-play. When the iframe src is reset,
        //   the video starts over auto-playing.
        // @see https://developers.google.com/youtube/iframe_api_reference
        // @see https://www.wrike.com/open.htm?id=385720578
        var $iframe = $modal.find('iframe');
        $iframe.attr('src', $iframe.attr('src'));

      });

    }
  };

})(Drupal, jQuery);
