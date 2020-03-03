// tests.site.www.services.virtual-clinic.virtualClinic.js
module.exports = {
    'disabled': false,
    '@tags': [
        'gray',
        'header',
        'footer',
        'locations',
        'search',
        'stevie'
    ],

    'Home Page Verify T1 Header - elements and search bar': function (browser) {
        const body = browser.page.virtualClinic();
        const testUrl = 'https://www.uwmedicine.org/services/virtual-clinic';
        const testUrlValidation = 'body.path-node-49201';

        browser
            .loadPageAndHandleCookiesAlert(testUrl, testUrlValidation, browser);

        body
            .verifyCTALinks()
            .verifyDownloadLinks()
            .verifyLooseLinks();

        browser.end();
    },
};