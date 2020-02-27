module.exports = {
    command: async function (linkSelector, pageValidator, returnUrl, returnValidator, msg) {
        // verifyLinkClick()
        // Verifies that a given link on a page exists, and that clicking it goes to the
        // expected page/ As cleanup, it returns to the page under test, and verifieds
        // it loaded correctly.
        // Arguments:
        //      linkSelector: css selector or page-object alias for link to test
        //      pageValidator: css selector or page-object alias to check correct page loaded
        //      returnUrl: The URL to return to, ie the page under test
        //      returnValidator: css selector or page-object alias to check correct return page loaded
        //      msg: Short-form message expanded to output action taken
        this
            .assert.visible(linkSelector, msg + ' link appears.')
            .click(linkSelector)
            .waitForElementVisible(pageValidator)
            .url(returnUrl)
            .waitForElementVisible(returnValidator, msg + 'Returned to ' + returnUrl + '.');
        return this;
    }
};

