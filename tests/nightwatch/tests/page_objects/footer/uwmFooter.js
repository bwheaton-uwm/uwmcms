// page_objects/footer/uwmFooter.js
module.exports = {
    commands: [
        {
            headerSearchFor: function (searchText) {
                // Verifies that the search bar is being shown, clears it, searches for the text
                // provided, then waits for the results
                return this
                    .assert.visible("@headerSearchBar")
                    .clearValue("@headerSearchBar")
                    .setValue("@headerSearchBar", searchText)
                    .assert.visible("@headerSubmitBtn")
                    .click("@headerSubmitBtn")
                    .waitForElementVisible("@resetButton");
            }
        }
    ],
    elements: {
        contactLink: '#quickLinksNavigation li:nth-child(9) a',
        eCareLink: '#quickLinksNavigation li:nth-child(1) a',
        headerImage: '.uwm-header a.navbar-brand img',
        headerSearchBar: 'input[type=text]:nth-child(1)',
        headerSubmitBtn: 'input.form-control:nth-chiled(1)',
        providersLink: '#quickLinksNavigation li:nth-child(3) a',
        researchLink: '#quickLinksNavigation li:nth-child(5) a',
        resetButton: {
            selector: "//a[@class='btn btn-cta btn-cta-link reset']",
            locateStrategy: "xpath"
        },
        schoolLink: '#quickLinksNavigation li:nth-child(7) a',
    }
};