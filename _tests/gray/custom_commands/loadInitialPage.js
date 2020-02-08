module.exports = {
    command: async function () {
        // Loads the Home page. Attempts to handle the Cookies Alert popup. Resizes the window
        // and verifies it's on the expected page
        return this
            .navigate()
            .waitForElementVisible("@cookiesAlertButton")
            .click("@cookiesAlertButton")
            .navigate()
            .waitForElementVisible("body")
            .resizeWindow(2048, 1536)
            .assert.title("Find a provider | UW Medicine",
                "verify /search/providers page title");
    }
};
