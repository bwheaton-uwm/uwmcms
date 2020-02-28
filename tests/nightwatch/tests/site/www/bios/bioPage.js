/**
 * @file
 * Generic tests for UWM provider pages.
 */

module.exports = {

  "disabled": false,
  "@tags": [
    "stevie",
    "bios"
  ],


  before: function (browser) {
    this.launch_url = browser.globals.sites.stevie.launch_url + '/bios/rebecca-abay';
    console.log(this.launch_url);
  },

  after: function (browser) {
  },

  beforeEach: function (browser) {
  },

  afterEach: function (browser) {
  },

  'Verifying we can open a provder page, and their name appears in the headering and page title': function (browser) {

    let pageHeading = 'article .provider-page__section1-area2 h1.page-title';

    browser
      .url(this.launch_url)
      .acceptCookiesDialog()
      .assert.titleContains('UW Medicine');

    browser
      .waitForElementVisible('article')
      .getText(pageHeading, function (result) {

        console.log('The provider title is ', result.value);
        browser.assert.ok(result.value.length > 20, 'Provider <h1> title is longer than 20 characters.')
        browser.assert.titleContains(result.value, 'The <head> title includes the provider\'s <h1> title.')

      });

  },

  'Verifying provider book online CTA\'s work correctly': function (browser) {

    const providerCards = browser.page.providerCards();

    providerCards.openModalBookOnline();

  }

};
