module.exports = {

  "disabled": false,
  "@tags": [
    "rar",
    "search"
  ],

  before: function (browser) {
  },

  after: function (browser) {
  },

  beforeEach: function (browser) {
  },

  afterEach: function (browser) {
  },

  'Verifying we can search for "heart" results': function (browser) {
    
    browser
      .url('https://rightasrain.uwmedicine.org/')

    browser
      .assert.visible('.region-header input[name=search]')
      .setValue('.region-header input[name=search]', 'Sleep')
      .assert.visible('.region-header button[type=submit]');

    browser.click('.region-header button[type=submit]', function (result) {
      console.log('Click result', result);
    });

  },

  'Verifying a search result is clickable': function (browser) {

    let linkSelector = '.view-content .views-row:last-of-type a';

    browser
      .waitForElementVisible('.region-content .view-content  .views-row')
      .assert.containsText('.region-content .view-content', 'Sleep');

    // browser
    //   .waitForElementVisible(linkSelector)
    //   .click("css selector", linkSelector)
    //   .assert.urlContains("sleep-paralysis");

    browser.assert.visible(linkSelector);

    browser.getAttribute('css selector', linkSelector, 'href', function(result) {

      articleUrl = result.value;
      console.log('linkSelector href', result);
      console.log('articleUrl', articleUrl);

      browser.url(articleUrl);

    });

  },

  'Verifying we have article content and an author': function (browser) {

    browser.waitForElementVisible('.main-container')
      .getText('.field--name-field-author', function (result) {
        browser.assert.ok(result.value.length > 10);
      })
      .getText('.field--name-field-sections .field--name-field-body', function (result) {
        browser.assert.ok(result.value.length > 400);
      });

  }

};
