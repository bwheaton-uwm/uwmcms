// tests.site.www.home.homepage.js
module.exports = {
    'disabled': false,
    '@tags': [
        'home',
        'header',
        'general'
    ],

    'Home Page Verify T1 Header - elements and search bar': function (browser) {
        var header = browser.page.uwmHeader();
        var testUrl = 'https://www.uwmedicine.org/';
        var testUrlValidation = 'body.is-path-frontpage';
        var searchText = 'location';
        header
            .loadPageAndAcceptCookies(testUrl, testUrlValidation)
            .verifyT1HeaderLinks(testUrl, testUrlValidation)
            .verifyHeaderSearch(searchText, testUrl, testUrlValidation)
            .verifyT2HeaderLinks(testUrl, testUrlValidation);
        browser.end();
    },
};