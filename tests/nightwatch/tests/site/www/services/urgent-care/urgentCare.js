// tests.site.www.services.urgent-care.urgentCare.js
module.exports = {
    'disabled': false,
    '@tags': [
        'header',
        'footer',
        'services',
        'urgent-care',
        'stevie'
    ],

    'Home Page Verify T1 Header - elements and search bar': function (browser) {
        const header = browser.page.uwmHeader();
        const footer = browser.page.uwmFooter();
        const testUrl = 'https://www.uwmedicine.org/services/urgent-care';
        const testUrlValidation = 'body.path-node-21946';
        const searchText = 'location';

        header
            .loadPageAndAcceptCookies(testUrl, testUrlValidation)
            .verifyT1HeaderLinks(testUrl, testUrlValidation)
            .verifyHeaderSearch(searchText, testUrl, testUrlValidation)
            .verifyT2HeaderLinks(testUrl, testUrlValidation);

        const body = browser.page.urgentCare();
        body
            .verifyBreadcrumbLinks(testUrlValidation)
            .verifyLocationSiteLinks(testUrlValidation)
            .verifyLocationAddressLinks(testUrlValidation)
            .verifySeeAllSpecialties(testUrlValidation)
            .verifySeeFullHoursLinks(testUrlValidation)
            .verifyTelephoneLinks(testUrlValidation)
            .verifyBookOnlineLinks(testUrlValidation)
            .verifyGetInLineLinks(testUrlValidation)
            .verifySeeDetailsLinks(testUrlValidation)
            .verifyGetCareNowLinks(testUrlValidation);

        footer
            .verifySocialLinks(testUrl, testUrlValidation)
            .verifyT1FooterLinks(testUrl, testUrlValidation)
            .verifyT2FooterLinks(testUrl, testUrlValidation);
        browser.end();
    },
};