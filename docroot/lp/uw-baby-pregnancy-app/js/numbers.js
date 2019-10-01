/*
* Numbers.js Number Replacer
* Odd Dog Media - Chris Goddard
* Version 1.1 - www.uwmedicine.org
*/

/*
* ENTER YOUR CAMPAIGN INFORMATION HERE:
*/

var campaigns = new Array(); // campaign names and numbers - DON'T TOUCH!
// Format: campaigns[#]= new campaign(name, bing, google, facebook, twitter);

campaigns[0] = new campaign("sportsmedicine", "206.520.6001", "206.520.6002", "206.520.6003", "206.520.6004");
campaigns[1] = new campaign("urgentcare", "206.520.6005", "206.520.6006", "206.520.6007", "206.520.6008");
campaigns[2] = new campaign("pediatrics", "206.520.6009", "206.520.6010", "206.520.6011", "206.520.6012");


/**
* DONT CHANGE BELOW THIS
**/

function campaign(name, bing, google, facebook, twitter) // Create a campaign
{
    this.name = name;
    this.bing = bing;
    this.google = google;
    this.facebook = facebook;
    this.twitter = twitter;
}

/*
* Purl (A JavaScript URL parser) v2.3.1
* Developed and maintanined by Mark Perkins, mark@allmarkedup.com
* Source repository: https://github.com/allmarkedup/jQuery-URL-Parser
* Licensed under an MIT-style license. See https://github.com/allmarkedup/jQuery-URL-Parser/blob/master/LICENSE for details.
*/
; (function (factory) { if (typeof define === 'function' && define.amd) { define(factory); } else { window.purl = factory(); } })(function () {
    var tag2attr = { a: 'href', img: 'src', form: 'action', base: 'href', script: 'src', iframe: 'src', link: 'href' }, key = ['source', 'protocol', 'authority', 'userInfo', 'user', 'password', 'host', 'port', 'relative', 'path', 'directory', 'file', 'query', 'fragment'], aliases = { 'anchor': 'fragment' }, parser = { strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/, loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ }, isint = /^[0-9]+$/; function parseUri(url, strictMode) {
        var str = decodeURI(url), res = parser[strictMode || false ? 'strict' : 'loose'].exec(str), uri = { attr: {}, param: {}, seg: {} }, i = 14; while (i--) { uri.attr[key[i]] = res[i] || ''; }
        uri.param['query'] = parseString(uri.attr['query']); uri.param['fragment'] = parseString(uri.attr['fragment']); uri.seg['path'] = uri.attr.path.replace(/^\/+|\/+$/g, '').split('/'); uri.seg['fragment'] = uri.attr.fragment.replace(/^\/+|\/+$/g, '').split('/'); uri.attr['base'] = uri.attr.host ? (uri.attr.protocol ? uri.attr.protocol + '://' + uri.attr.host : uri.attr.host) + (uri.attr.port ? ':' + uri.attr.port : '') : ''; return uri;
    }
    function getAttrName(elm) { var tn = elm.tagName; if (typeof tn !== 'undefined') return tag2attr[tn.toLowerCase()]; return tn; }
    function promote(parent, key) { if (parent[key].length === 0) return parent[key] = {}; var t = {}; for (var i in parent[key]) t[i] = parent[key][i]; parent[key] = t; return t; }
    function parse(parts, parent, key, val) { var part = parts.shift(); if (!part) { if (isArray(parent[key])) { parent[key].push(val); } else if ('object' == typeof parent[key]) { parent[key] = val; } else if ('undefined' == typeof parent[key]) { parent[key] = val; } else { parent[key] = [parent[key], val]; } } else { var obj = parent[key] = parent[key] || []; if (']' == part) { if (isArray(obj)) { if ('' !== val) obj.push(val); } else if ('object' == typeof obj) { obj[keys(obj).length] = val; } else { obj = parent[key] = [parent[key], val]; } } else if (~part.indexOf(']')) { part = part.substr(0, part.length - 1); if (!isint.test(part) && isArray(obj)) obj = promote(parent, key); parse(parts, obj, part, val); } else { if (!isint.test(part) && isArray(obj)) obj = promote(parent, key); parse(parts, obj, part, val); } } }
    function merge(parent, key, val) {
        if (~key.indexOf(']')) { var parts = key.split('['); parse(parts, parent, 'base', val); } else {
            if (!isint.test(key) && isArray(parent.base)) { var t = {}; for (var k in parent.base) t[k] = parent.base[k]; parent.base = t; }
            if (key !== '') { set(parent.base, key, val); }
        }
        return parent;
    }
    function parseString(str) {
        return reduce(String(str).split(/&|;/), function (ret, pair) {
            try { pair = decodeURIComponent(pair.replace(/\+/g, ' ')); } catch (e) { }
            var eql = pair.indexOf('='), brace = lastBraceInKey(pair), key = pair.substr(0, brace || eql), val = pair.substr(brace || eql, pair.length); val = val.substr(val.indexOf('=') + 1, val.length); if (key === '') { key = pair; val = ''; }
            return merge(ret, key, val);
        }, { base: {} }).base;
    }
    function set(obj, key, val) { var v = obj[key]; if (typeof v === 'undefined') { obj[key] = val; } else if (isArray(v)) { v.push(val); } else { obj[key] = [v, val]; } }
    function lastBraceInKey(str) { var len = str.length, brace, c; for (var i = 0; i < len; ++i) { c = str[i]; if (']' == c) brace = false; if ('[' == c) brace = true; if ('=' == c && !brace) return i; } }
    function reduce(obj, accumulator) {
        var i = 0, l = obj.length >> 0, curr = arguments[2]; while (i < l) { if (i in obj) curr = accumulator.call(undefined, curr, obj[i], i, obj); ++i; }
        return curr;
    }
    function isArray(vArg) { return Object.prototype.toString.call(vArg) === "[object Array]"; }
    function keys(obj) {
        var key_array = []; for (var prop in obj) { if (obj.hasOwnProperty(prop)) key_array.push(prop); }
        return key_array;
    }
    function purl(url, strictMode) {
        if (arguments.length === 1 && url === true) { strictMode = true; url = undefined; }
        strictMode = strictMode || false; url = url || window.location.toString(); return { data: parseUri(url, strictMode), attr: function (attr) { attr = aliases[attr] || attr; return typeof attr !== 'undefined' ? this.data.attr[attr] : this.data.attr; }, param: function (param) { return typeof param !== 'undefined' ? this.data.param.query[param] : this.data.param.query; }, fparam: function (param) { return typeof param !== 'undefined' ? this.data.param.fragment[param] : this.data.param.fragment; }, segment: function (seg) { if (typeof seg === 'undefined') { return this.data.seg.path; } else { seg = seg < 0 ? this.data.seg.path.length + seg : seg - 1; return this.data.seg.path[seg]; } }, fsegment: function (seg) { if (typeof seg === 'undefined') { return this.data.seg.fragment; } else { seg = seg < 0 ? this.data.seg.fragment.length + seg : seg - 1; return this.data.seg.fragment[seg]; } } };
    }
    purl.jQuery = function ($) {
        if ($ != null) {
            $.fn.url = function (strictMode) {
                var url = ''; if (this.length) { url = $(this).attr(getAttrName(this[0])) || ''; }
                return purl(url, strictMode);
            }; $.url = purl;
        }
    }; purl.jQuery(window.jQuery); return purl;
});
/*!
* jQuery Cookie Plugin v1.3.1
* https://github.com/carhartl/jquery-cookie
*
* Copyright 2013 Klaus Hartl
* Released under the MIT license
*/
(function ($, document, undefined) {
    var pluses = /\+/g; function raw(s) { return s; }
    function decoded(s) { return unRfc2068(decodeURIComponent(s.replace(pluses, ' '))); }
    function unRfc2068(value) {
        if (value.indexOf('"') === 0) { value = value.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\'); }
        return value;
    }
    function fromJSON(value) { return config.json ? JSON.parse(value) : value; }
    var config = $.cookie = function (key, value, options) {
        if (value !== undefined) {
            options = $.extend({}, config.defaults, options); if (value === null) { options.expires = -1; }
            if (typeof options.expires === 'number') { var days = options.expires, t = options.expires = new Date(); t.setDate(t.getDate() + days); }
            value = config.json ? JSON.stringify(value) : String(value); return (document.cookie = [encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value), options.expires ? '; expires=' + options.expires.toUTCString() : '', options.path ? '; path=' + options.path : '', options.domain ? '; domain=' + options.domain : '', options.secure ? '; secure' : ''].join(''));
        }
        var decode = config.raw ? raw : decoded; var cookies = document.cookie.split('; '); var result = key ? null : {}; for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('='); var name = decode(parts.shift()); var cookie = decode(parts.join('=')); if (key && key === name) { result = fromJSON(cookie); break; }
            if (!key) { result[name] = fromJSON(cookie); }
        }
        return result;
    }; config.defaults = {}; $.removeCookie = function (key, options) {
        if ($.cookie(key) !== null) { $.cookie(key, null, options); return true; }
        return false;
    };
})(jQuery, document);



/* Plugin */
// leaving these in window namespace on assumption that they are to be used by other reporting
// if not, alter sourceFind to return locally scoped values.
var campaignid;
var campaignsource;

(function ($) {

    function sourceFind() {
        var cookie = $.cookie('adCookie'); // Read adCookie
        var urlCampaign = $.url().param('campaign');
        var urlSource = $.url().param('source');
        if (urlCampaign) { // if there's a campaign URL
            campaignid = urlCampaign; // get "campaign" parameter
            campaignsource = urlSource; // get "source" parameter
            $.cookie('adCookie', urlCampaign + "/" + urlSource, {
                expires: 180,
                path: '/'
            });
        } else { // if there's no campaign URL
            if (cookie) { // if there's a adCookie cookie
                var cookieArray = cookie.split("/"); // Split cookie value
                campaignid = cookieArray[0]; // Get campaign
                campaignsource = cookieArray[1]; // Get source
            }
        }
    }

    function tracking() { // Where is person from? Check against campaign names and trigger appropriate replacer
        sourceFind(); // Run sourceFind
        if (campaignid) { // If there's a campaign ID
            $.each(campaigns, function () {
                if (this.name == campaignid) {
                    campaignsource && this[campaignsource] && $("span.apptphone").text(this[campaignsource]);
                    return false;
                }
            });
        }
    }

    $(document).ready(function () {
        tracking();
    });
}(jQuery));
