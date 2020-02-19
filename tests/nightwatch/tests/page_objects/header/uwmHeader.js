// page_objects/footer/uwmHeader.js
module.exports = {
    commands: [
        {
            headerSearchFor: function (searchText) {
                // Verifies that the search bar is being shown, clears it, searches for the text
                // provided, then waits for the results. This is a helper function for when other
                // tests would like to search for a given string.
                return this
                    .assert.visible('@headerSearchBar', 'Search bar is present.')
                    .clearValue('@headerSearchBar')
                    .setValue('@headerSearchBar', searchText)
                    .assert.visible('@headerSubmitBtn', 'Search submit button is present.')
                    .click('css selector', 'form.header-search-form svg.fa-search:nth-child(2)');
            },
            loadPageAndAcceptCookies: function (pageUrl, testUrlValidation) {
                // loads a page then attempts to handle the Cookies Alert popup.
                // This is a helper function for loading most pages. It requires two arguments:
                // pageUrl -- URL of the page to load
                // testUrlValidation -- a css selector string use to check that the correct page
                //                      has loaded. It should be unique to that page.
                return this
                    .navigate(pageUrl)
                    .waitForElementVisible('@cookiesAlertButton', 'Cookies alert button found.')
                    .click('@cookiesAlertButton')
                    .waitForElementVisible(testUrlValidation, 'Requested page loaded.');
            },
            verifyT1HeaderLinks: function (pageUrl, testUrlValidation) {
                // Abstracts out the tests for the T1 Header links.
                // This is a helper function for testing the T1 Header links for other pages under test.
                // It requires two arguments:
                // pageUrl -- URL of the page to load
                // testUrlValidation -- a css selector string use to check that the correct page
                //                      has loaded. It should be unique to that page.
                // See site.www.general.uwmHeader.js for an example of how to use this within other
                // test pages
                var UWLogoImageSrc = 'https://www.uwmedicine.org/themes/custom/uwmbase/dist/images/uw-medicine-logo.svg';

                this
                    .assert.visible('@headerImage', 'UW Medicine Image link appears.')
                    .getAttribute('@headerImage', 'src', function (result) {
                        this.assert.equal(result.value, UWLogoImageSrc, 'UW Medicine Image has correct source.');
                    });
                this
                    .assert.visible('@eCareLink', 'eCare Patient Portal link appears.')
                    .click('@eCareLink')
                    .waitForElementVisible('@eCareValidation', 'eCare Patient Portal link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');
                this
                    .assert.visible('@providersLink', 'For Providers link appears.')
                    .click('@providersLink')
                    .waitForElementVisible('@providersValidation', 'For Providers link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');
                this
                    .assert.visible('@researchLink', 'Research link appears.')
                    .click('@researchLink')
                    .waitForElementVisible('@researchValidation', 'Research link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');
                this
                    .assert.visible('@schoolLink', 'School of Medicine link appears.')
                    .click('@schoolLink')
                    .waitForElementVisible('@schoolValidation', 'School of Medicine link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');
                this
                    .assert.visible('@contactLink', 'Contact Us link appears.')
                    .click('@contactLink')
                    .waitForElementVisible('@contactValidation', 'Contact Us link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');

                    return this;
            },
            verifyHeaderSearch: function (searchText='location') {
                // Abstracts out testing the search bar functionality for the T1 Header.
                // Verifies the search bar is present, clears it, adds supplied test (or 'location' if not
                // provided), clicks the search button, and verifies the search resuilt page is loaded and
                // that there are results found.
                // See site.www.general.uwmHeader.js for an example of how to use this within other
                // test pages
                this
                    .assert.visible('@headerSearchBar', 'Search bar is present.')
                    .clearValue('@headerSearchBar')
                    .setValue('@headerSearchBar', searchText)
                    .assert.visible('@headerSubmitBtn', 'Search submit button is present.')
                    .click('css selector', 'form.header-search-form svg.fa-search:nth-child(2)')
                    .waitForElementVisible('@resetButton', 'Results page loaded.')
                    .waitForElementVisible('@results', 'Found search results.');

                return this;
            }
        }
    ],
    elements: {
        contactLink: '#quickLinksNavigation li:nth-child(9) a',
        contactValidation: 'body.path-node-21466',
        cookiesAlertButton: '.eu-cookie-compliance-default-button',
        eCareLink: '#quickLinksNavigation li:nth-child(1) a',
        eCareValidation: 'body.path-node-20841',
        headerImage: '.uwm-header a.navbar-brand img',
        headerSearchBar: 'input[type=text]:nth-child(1)',
        headerSubmitBtn: 'form.header-search-form svg.fa-search:nth-child(2)',
        homeValidation: 'body.is-path-frontpage',
        providersLink: '#quickLinksNavigation li:nth-child(3) a',
        providersValidation: '.homepage-header-inset li:nth-child(1)',
        researchLink: '#quickLinksNavigation li:nth-child(5) a',
        researchValidation: 'body.path-node-21421',
        resetButton: {
            selector: '//a[@class="btn btn-cta btn-cta-link reset"]',
            locateStrategy: 'xpath'
        },
        results: 'article.search-result-card',
        schoolLink: '#quickLinksNavigation li:nth-child(7) a',
        schoolValidation: 'body.path-node-20876'
    }
};
