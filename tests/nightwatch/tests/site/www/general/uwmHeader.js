// tests.site.www.general.uwmHeader.js
module.exports = {
    'disabled': false,
    '@tags': [
        'header',
        'general',
        'stevie'
    ],

    'Verify T1 Header - elements and search bar for Home page': function (browser) {
        var header = browser.page.uwmHeader();
        var testUrl = 'https://www.uwmedicine.org/';
        var testUrlValidation = 'body.is-path-frontpage';
        var searchText = 'location';

        header
            .loadPageAndAcceptCookies(testUrl, testUrlValidation)
            .verifyT1HeaderLinks(testUrl, testUrlValidation)
            .verifyHeaderSearch(searchText);
        browser.end();
    },
};