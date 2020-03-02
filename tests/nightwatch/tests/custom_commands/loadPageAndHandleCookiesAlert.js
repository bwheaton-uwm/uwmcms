module.exports = {
    command: async function (pageUrl, urlValidation, browser) {
        // loads a page then attempts to handle the Cookies Alert popup.
        // This is a helper function for loading most pages. It requires two arguments:
        // pageUrl -- URL of the page to load
        // testUrlValidation -- a css selector string use to check that the correct page
        //                      has loaded. It should be unique to that page.
        browser
            .url(pageUrl)
            .pause(3000)
            .waitForElementVisible('.eu-cookie-compliance-default-button', 'Cookies alert button found.')
            .click('.eu-cookie-compliance-default-button')
            .pause(3000)
            .waitForElementVisible(urlValidation, 'Requested page loaded.');

        return this;
    }
};
