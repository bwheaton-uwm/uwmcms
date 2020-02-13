module.exports = {

  "disabled": false,
  "@tags": [
    "rar",
    "search",
    "jnm3"
  ],

  before: function (browser) {
  },

  after: function (browser) {
  },

  beforeEach: function (browser) {
  },

  afterEach: function (browser) {

    console.log('afterEach up...');
    browser.url(function (result) {
      console.log(result);
    });

  },


  'Verifying we can search for "heart" results': function (browser) {

    browser
      .url('https://rightasrain.uwmedicine.org/')

    browser
      .assert.visible('.region-header input[name=search]')
      .setValue('.region-header input[name=search]', 'Sleep')
      .assert.visible('.region-header button[type=submit]');

    browser.click('.region-header button[type=submit]', function(result) {
      this.assert.strictEqual(result.status, 0);
    });


  },

  'Verifying a search result is clickable': function (browser) {

    let linkSelector = '.view-content .views-row:last-of-type a';

    browser.waitForElementVisible('.region-content .view-content  .views-row')
      .assert.containsText('.region-content .view-content', 'Sleep');

    browser.assert.visible(linkSelector);

    browser.getAttribute('css selector', linkSelector, 'href', function (result) {

      console.log('articleUrl', result.value);
      browser.url(result.value);

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
