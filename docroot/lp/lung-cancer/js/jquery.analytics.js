/*!
 * jQuery Analytics
 * Original author: Chris Goddard - Odd Dog Media
 * Further changes, comments: @cgoddard
 * Licensed under the MIT license
 */

/*
 *	<a href="#" data-track="click"  [ data-ga-category="click tracking" data-ga-action="action" data-ga-label="label" data-ga-value="1" ]>Click Tracking</a>
 *	<a href="#" data-track="hover" [ data-ga-time="1000" data-ga-category="hover tracking" data-ga-action="action" data-ga-label="label" data-ga-value="1" ]>Hover Tracking</a>
 */

/**
 * Protect window.console method calls, e.g. console is not defined on IE
 * unless dev tools are open, and IE doesn't define console.debug
 */
(function () {
    if (!window.console) {
        window.console = {};
    }
    // union of Chrome, FF, IE, and Safari console methods
    var m = [
      "log", "info", "warn", "error", "debug", "trace", "dir", "group",
      "groupCollapsed", "groupEnd", "time", "timeEnd", "profile", "profileEnd",
      "dirxml", "assert", "count", "markTimeline", "timeStamp", "clear"
    ];
    // define undefined methods as noops to prevent errors
    for (var i = 0; i < m.length; i++) {
        if (!window.console[m[i]]) {
            window.console[m[i]] = function () { };
        }
    }
})();

// Make settings optional:
var oddAnalyticsOptions = (typeof oddAnalyticsOptions) ? oddAnalyticsOptions : {};
(function (document, window, $, options, undefined) {
    // Define working object
    var analytics = {};
    // Defaults object
    var defaults = {
        scrollElementToTrack: 'body',
        debug: false,
        formcategory: 'form_submission',
        formprefix: 'form_'
    };
    // Combine defaults object with options parameter
    analytics.settings = $.extend({}, defaults, options);
    // define utility function
    function define(variable, defaultvalue) {
        return (variable) ? variable : ((defaultvalue) ? defaultvalue : null);
    }
    function debug() {
        console.log('Analytics Event:', arguments);
    }
    // set universal and classic variables
    analytics.settings.universal = (typeof ga != 'undefined') ? true : false;
    analytics.settings.classic = (typeof _gaq != 'undefined') ? true : false;
    analytics.send = {
        pageview: function (url, path, title) {
            var location = define(url, window.location.href);
            var page = define(path, window.location.pathname + window.location.search + window.location.hash);
            var pagetitle = define(title, document.title);
            if (analytics.settings.universal) ga('send', 'pageview', location, page, pagetitle);
            if (analytics.settings.classic) _gaq.push(['_trackPageview', page]);
            if (analytics.settings.debug) debug(arguments);
            return true;
        },
        event: function (category, action, label, value, interaction) {

            var noninteraction = define(noninteraction, false);
            if (analytics.settings.universal) ga('send', 'event', category, action, label, {
                'nonInteraction': noninteraction
            });
            if (analytics.settings.classic) _gaq.push(['_trackEvent', category, action, label, noninteraction]);
            if (analytics.settings.debug) debug(arguments);
            return true;
        },
        social: function (network, action, target, source) {
            if (!network || !action) return;
            var target = define(target, window.location.href);
            var source = define(source, window.location.pathname + window.location.search + window.location.hash);
            if (analytics.settings.universal) ga('send', 'social', network, action, target, {
                'page': source
            });
            if (analytics.settings.classic) _gaq.push('_trackSocial', network, action, target, source);
            if (analytics.settings.debug) debug(arguments);
            return true;
        },
        ecommerce: { /* V2 */
        },
        custom: function (index, name, value, scope) { /* V2 */
        }
    };
    // jQuery plugin
    $.extend($.fn, {
        trackevent: function (category, action, opt_label, opt_value, opt_noninteraction) {

            console.log($(this).attr('href'));

            var category = (category ? category : ($(this).data('ga-category') ? $(this).data('ga-category') : window.location.href));
            var action = (action ? action : ($(this).data('ga-action') ? $(this).data('ga-action') : $(this).attr('href')));
            var label = (opt_label ? opt_label : ($(this).data('ga-label') ? $(this).data('ga-label') : $(this).text()));
            var value = (opt_value ? opt_value : ($(this).data('ga-value') ? $(this).data('ga-value') : null));
            var nonint = (opt_noninteraction ? opt_noninteraction : ($(this).data('ga-nonint') ? $(this).data('ga-nonint') : false));

            console.log([category, action, label, value, nonint]);

            analytics.send.event(category, action, label, value, nonint);
        }
    });
    // Scroll tracking
    var element_to_track = analytics.settings.scrollElementToTrack,
		scroll_reach = 0,
		temp_scroll_reach = 0,
		scroll_reach_pixels = 0,
		temp_scroll_reach_pixels = 0;
    $(window).bind({
        scroll: function () {
            temp_scroll_reach = Math.floor(100 * ($(window).scrollTop() + $(window).height() - $(element_to_track).position().top) / $(element_to_track).height());
            temp_scroll_reach_pixels = Math.floor($(window).scrollTop() + $(window).height());
            if (temp_scroll_reach > scroll_reach) {
                scroll_reach = temp_scroll_reach;
                scroll_reach_pixels = temp_scroll_reach_pixels;
            }
        },
        beforeunload: function () {
            //Rel 63 - Sreepada
            //analytics.send.event('Scroll Depth', 'Percentage', '', scroll_reach);
            //analytics.send.event('Scroll Depth', 'Pixels', '', scroll_reach_pixels);
        }
    });
    // HTML based
    $("[data-track]").each(function () {
        var action = $(this).data('track');
        if (action === 'hover') {
            var period;
            var hoverTime;
            var element_hover = $(this);
            $(this).on({
                mouseenter: function () {
                    period = ($(this).data('ga-time') ? $(this).data('ga-time') : 0);
                    hoverTime = setTimeout(function () {
                        element_hover.trackevent();
                    }, period);
                },
                mouseleave: function () {
                    clearTimeout(hoverTime);
                }
            });
        } else if (action === 'scrollto') {
            var element_scroll = $(this);
            var top = element_scroll.offset().top;
            var ran = false;
            $(window).on('scroll', function () {
                if (($(window).scrollTop() + $(window).height() > top) && (!ran)) {
                    element_scroll.trackevent();
                    if (element_scroll.data('ga-multiple') != 'true') {
                        ran = true;
                    }
                }
            });
        } else {
            $(this).on(action, function () {
                $(this).trackevent();
            });
        }
    });
    // Gravity Forms	
    $(document).bind({
        gform_confirmation_loaded: function (event, form_id) {
            analytics.send.event(analytics.settings.formcategory, analytics.settings.formprefix + form_id, window.location.href);
        }
    });
    window.oddAnalytics = analytics;
})(document, window, jQuery, oddAnalyticsOptions);