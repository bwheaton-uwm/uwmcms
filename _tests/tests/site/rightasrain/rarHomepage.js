module.exports = {

  "disabled": false,
  "@tags": [
    "rar",
    "basic",
  ],


  before: function (browser) {
  },

  after: function (browser) {
  },

  beforeEach: function (browser) {
  },

  afterEach: function (browser) {
  },

  'Verifying we can open RAR\'s home page': function (browser) {

    browser
      .url('https://rightasrain.uwmedicine.org/')
      .assert.titleContains('Rain');

  },


  'Verifying we have at least four "latest" section articles': function (browser) {

    browser
      .waitForElementVisible('.region-content')
      .assert.containsText('.region-content', 'Latest');

    browser.expect.element('.view-latest-articles *:nth-child(4) h3 a').to.be.present;

  },


  'Verifying we have a featured article, it\'s image and summary': function (browser) {

    browser
      .waitForElementVisible('.region-content')
      .assert.visible('.block-views-blockhighlighted-article-block .field--name-field-primary-media img')
      .getText('.block-views-blockhighlighted-article-block .field--name-field-long-summary', function (result) {
        browser.assert.ok(result.value.length > 100);
      });

  },

};
