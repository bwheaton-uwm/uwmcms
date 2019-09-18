jQuery(document).ready(function () {
    //add proper path into breadCrumb
    var ulBreadCrumb = jQuery("#ul_breadcrumb > li");
    var ulFirstElm = ulBreadCrumb.first();

    var requestURL = jQuery(location).attr("href").toLowerCase();

    var reqLen = requestURL.length;

    if (requestURL.indexOf("/locations/") >= 0) {

        var StartIndex = requestURL.indexOf("/locations/");
        var EndIndex = StartIndex + 10;

        if (EndIndex < reqLen - 1) {
            jQuery(ulFirstElm).after('<li><a href="/locations/">Locations</a></li>');
        }
    }
    else if (requestURL.indexOf("/services/") >= 0) {
        var StartIndex = requestURL.indexOf("/services/");
        var EndIndex = StartIndex + 9;

        if (EndIndex < reqLen - 1) {
            jQuery(ulFirstElm).after('<li><a href="/services/">Services</a></li>');
        }
    }
    else if (requestURL.indexOf("/search") >= 0)
    {
        var StartIndex = requestURL.indexOf("/search");
        var EndIndex = StartIndex + 6;

        if (EndIndex < reqLen && requestURL.indexOf("/locations") < 0 && requestURL.indexOf("/providers") < 0)
        {
            jQuery(ulFirstElm).after('<li><a href="/search/">Search</a></li>');
        }
    }
});
