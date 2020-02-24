// tests.site.www.home.homepage.js
module.exports = {
    'disabled': false,
    '@tags': [
        'home',
        'header',
        'general'
    ],

    'Home Page Verify T1 Header - elements and search bar': function (browser) {
        const header = browser.page.uwmHeader();
        const footer = browser.page.uwmFooter();
        const home = browser.page.uwmHome();
        const testUrl = 'https://www.uwmedicine.org/';
        const testUrlValidation = 'body.is-path-frontpage';
        const searchText = 'location';

        header
            .loadPageAndAcceptCookies(testUrl, testUrlValidation)
            .verifyT1HeaderLinks(testUrl, testUrlValidation)
            .verifyHeaderSearch(searchText, testUrl, testUrlValidation)
            .verifyT2HeaderLinks(testUrl, testUrlValidation);

        home
            .verifyHeroCtaLinks(testUrl, testUrlValidation)
            .verifySectionLinks(testUrl, testUrlValidation)
            .verifySpotlightLinks(testUrl, testUrlValidation);

        footer
            .verifySocialLinks(testUrl, testUrlValidation)
            .verifyT1FooterLinks(testUrl, testUrlValidation)
            .verifyT2FooterLinks(testUrl, testUrlValidation);
        browser.end();
    },
};