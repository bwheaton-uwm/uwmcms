// tests.site.www.search.locations.searchLocations.js
module.exports = {
    'disabled': false,
    '@tags': [
        'gray',
        'header',
        'footer',
        'locations',
        'search'
    ],

    'Home Page Verify T1 Header - elements and search bar': function (browser) {
        const header = browser.page.uwmHeader();
        const footer = browser.page.uwmFooter();
        const body = browser.page.searchLocations();
        const testUrl = 'https://www.uwmedicine.org/search/locations';
        const testUrlValidation = 'body.path-search-locations';
        const searchText = 'location';

        header
            .loadPageAndAcceptCookies(testUrl, testUrlValidation)
            .verifyT1HeaderLinks(testUrl, testUrlValidation)
            .verifyHeaderSearch(searchText, testUrl, testUrlValidation)
            .verifyT2HeaderLinks(testUrl, testUrlValidation);

        body
            .verifyTelLinks()
            .verifyCardLinks(testUrlValidation);

        footer
            .verifySocialLinks(testUrl, testUrlValidation)
            .verifyT1FooterLinks(testUrl, testUrlValidation)
            .verifyT2FooterLinks(testUrl, testUrlValidation);
        browser.end();
    },
};