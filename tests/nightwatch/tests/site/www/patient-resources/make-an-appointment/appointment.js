// tests.site.www.patient-resources.make-an-appointment.appointment.js
module.exports = {
    'disabled': false,
    '@tags': [
        'header',
        'footer',
        'patient-resources',
        'appointments'
    ],

    'Home Page Verify T1 Header - elements and search bar': function (browser) {
        const header = browser.page.uwmHeader();
        const footer = browser.page.uwmFooter();
        const body = browser.page.makeAnAppointment();
        const testUrl = 'https://www.uwmedicine.org/patient-resources/make-an-appointment';
        const testUrlValidation = 'body.path-node-20836';
        const searchText = 'location';

        header
            .loadPageAndAcceptCookies(testUrl, testUrlValidation)
            .verifyT1HeaderLinks(testUrl, testUrlValidation)
            .verifyHeaderSearch(searchText, testUrl, testUrlValidation)
            .verifyT2HeaderLinks(testUrl, testUrlValidation);

        body
            .verifyBreadcrumbLinks(testUrl, testUrlValidation)
            .verifyGridcardLinks(testUrl, testUrlValidation)
            .verifySectionCollapse(testUrl, testUrlValidation)
            .verifyRescheduleLinks(testUrl, testUrlValidation)
            .verifyInterpreterLinks(testUrl, testUrlValidation);

        footer
            .verifySocialLinks(testUrl, testUrlValidation)
            .verifyT1FooterLinks(testUrl, testUrlValidation)
            .verifyT2FooterLinks(testUrl, testUrlValidation);
        browser.end();
    },
};