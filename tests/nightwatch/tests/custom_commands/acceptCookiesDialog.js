/**
 * @file
 * Custom command to close the Accept Cookies modal, so later visibility tests work.
 */

module.exports = {

    command: async function () {

        let selector = '.eu-cookie-compliance-default-button';
        this.waitForElementVisible(selector, 100);
        this.click(selector);

    }

};
