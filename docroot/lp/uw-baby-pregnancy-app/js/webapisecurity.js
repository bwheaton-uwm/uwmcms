// stringifyHours.js
// webapisecurity.js

(function ($) {
    $(document).ready(function () {
        var token = $('#ctlSecurity').html();
        $.ajaxSetup({
            xhrFields: {
                withCredentials: true
            },
            beforeSend: function (req) {
                if (token) {
                    req.setRequestHeader('Authorization', 'Bearer ' + token);
                    return true;
                }
            }
        });
    });
}(jQuery));

