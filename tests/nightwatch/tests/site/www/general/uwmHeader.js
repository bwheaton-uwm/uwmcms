// tests.site.www.general.uwmHeader.js
module.exports = {
    'disabled': false,
    '@tags': [
        'header',
        'general'
    ],

    'header test': function (browser) {
        var searchProviderPage = browser.page.searchProviders();
        var header = browser.page.uwmHeader();

        searchProviderPage
            .loadPage()

        header
            .headerSearchFor("Cat and dog");

        searchProviderPage
            .pause(30000)
            .waitForElementVisible('@resetButton');

        var card = searchProviderPage.section.providerCard;
        card
            .assert.visible('@cardTitle', 'Card Title verified')
            .assert.visible('@accepting', 'Accepting New Patients verified')
            .assert.visible('@specialtiesLabel', 'Medical Specialties label verified')
            .assert.visible('@specialtiesItems', 'Medical Specialties are listed verified')
            .assert.visible('@appointmentsLabel', 'Appointments label text verified')
            .assert.visible('@calendarIcon', 'Appointments calendar icon verified')
            .assert.visible('@locationsLabel', 'Locations label text verified')
            .assert.visible('@locationsPinIcon', 'Locations Pin icon verified')
            // Larry S Dean should have 2 location links
            .assert.elementCount('@locationsItems', 2, 'Correct number of Location links found')
            .assert.visible('@locationsItemOne', 'First location link verified')
            .assert.visible('@locationItemsTwo', 'Second location link verified')
            .assert.visible('@appointmentsViewContactDetailButton',
                'View Contact Details button verified')
            .assert.visible('@seeMoreButton', 'See More button is present')
        browser.end();
    },
};