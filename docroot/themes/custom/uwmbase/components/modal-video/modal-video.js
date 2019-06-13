/**
 * @file
 * Custom JS for modal video component.
 *
 * Library: uwmbase/modal-video.
 */

(function (Drupal, $) {
    $(document).ready(function () {
        // Stop playing the video when the modal closes by resetting the
        // iframe src.
        $(".uwm-modal-video .modal").on('hidden.bs.modal', function (e) {
            var $this = $(this);
            var $iframe = $this.find('iframe');
            $iframe.attr("src", $iframe.attr("src"));
        });
    });
})(Drupal, jQuery);
