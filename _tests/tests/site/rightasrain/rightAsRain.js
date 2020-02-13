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

    console.log('afterEach up...');
    browser.url(function (result) {
      console.log(result);
    });

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
      .assert.containsText('.region-content ' +
      '.block-views-blockhighlighted-article-block ' +
      '.field--name-field-long-summary',
      '.')
      .assert.visible('.region-content ' +
      '.block-views-blockhighlighted-article-block ' +
      '.field--name-field-primary-media img');

  },

};
